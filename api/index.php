<?php

function checkMethod($PROCESSOR, $method) {
    if (!method_exists($PROCESSOR, $method)) {
        http_response_code(404);
        throw new Exception('Not Implemented');
    }
}

// TO ALLOW CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-Request-With, Request-With');
// CORS THINGIES ENDED

require_once 'bootstrap.php';
$PROCESSOR = config('global', 'dataProcessor');

// default status is true: expecting a successful action
$response = ['status' => true];
// default status
// get path from GET parameters and into array
$path = filter_input(INPUT_GET, 'path');
// get node class
$NodeClass = _toCamel($PROCESSOR::getTargetClassName($path));
// process request on filename
if (!$node = $PROCESSOR::getNodeFromPath($path)) {
    $response = [
        'status' => false,
        'message' => 'Invalid path'
    ];
}
else {
// fetch request data into $request_data
    parse_str(file_get_contents('php://input'), $request_data);
// check request method/type
    try {
        // Block if node is marked blocked 
        if (in_array($node, config('global', 'blockedNodes') ?: []))
            throw new Exception('Access denied');
        // Use node class if exists
        if (class_exists($NodeClass))
            $PROCESSOR = $NodeClass;
        // Node class doesn't exist. Throw exception if it must exist to continue
        else if (config('global', 'appNodesOnly')) {
            http_response_code(403);
            throw new Exception('Access denied');
        }
        // Check if method is allowed
        $allowedMethods = config('global', 'allowedMethods');
        if ($allowedMethods && array_key_exists($node, $allowedMethods) &&
                !in_array($_SERVER['REQUEST_METHOD'], $allowedMethods[$node])) {
            http_response_code(403);
            throw new \Exception('Method Not Allowed');
        }
        if ($path === 'search') {
            $response['data'] = $PROCESSOR::search($node);
        }
        else {
            // process request
            switch (filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
                case 'GET':
                    checkMethod($PROCESSOR, 'get');
                    $response['data'] = $PROCESSOR::get($node, $path);
                    break;
                case 'POST':
                    checkMethod($PROCESSOR, 'create');
                    $response['data'] = $PROCESSOR::create($node, $request_data, $path);
                    break;
                case 'PUT':
                case 'PATCH':
                    checkMethod($PROCESSOR, 'update');
                    $response['data'] = $PROCESSOR::update($node, $path, $request_data);
                    break;
                case 'DELETE':
                    checkMethod($PROCESSOR, 'delete');
                    $response['data'] = $PROCESSOR::delete($node, $path);
                    break;
            }
        }
    }
    catch (Exception $ex) {
        $response['status'] = false;
        $response['message'] = $ex->getMessage();
    }
}
$PROCESSOR::output($response);
