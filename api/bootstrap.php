<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('DATA', ROOT . 'data' . DIRECTORY_SEPARATOR);

error_reporting(0);

require_once 'core/helpers.php';

spl_autoload_register(function($class_name) {
    // load from core classes
    $class_path = __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'classes' .
            DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (is_readable($class_path)) require_once $class_path;
    // load from app classes
    $class_path = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
    if (is_readable($class_path)) require_once $class_path;
}, true);
