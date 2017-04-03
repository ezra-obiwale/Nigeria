<?php

/**
 * Fetches a value from the config file
 * @param string $name
 * @param mixed $_
 * @return mixed
 */
function config($name, $_) {
    $args = func_get_args();
    $data = include ROOT . 'config' . DIRECTORY_SEPARATOR . $name . '.php';
    array_shift($args);
    if (count($args)) {
        foreach ($args as $arg) {
            if (!array_key_exists($arg, $data)) break;
            $data = $data[$arg];
        }
    }
    return $data;
}

/**
 * Changes a string from snake_case to CamelCase
 * @param string $str
 * @return string
 */
function _toCamel($str) {
    if (!is_string($str)) return '';
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return ucfirst(preg_replace_callback('/_([a-z])/', $func, $str));
}

/**
 * Creates a global unique id
 * @return string
 */
function createGUID() {
    if (function_exists('com_create_guid')) {
        return substr(com_create_guid(), 1, 36);
    }
    else {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8) . $hyphen .
                substr($charid, 8, 4) . $hyphen .
                substr($charid, 12, 4) . $hyphen .
                substr($charid, 16, 4) . $hyphen .
                substr($charid, 20, 12);

        return $uuid;
    }
}
