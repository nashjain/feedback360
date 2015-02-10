<?php
define('BASE_PATH', __DIR__ . '/');
define('CONTROLLER_DIR', dirname(__FILE__).'/controllers/');
define('MODELS_DIR', dirname(__FILE__).'/models/');
define('TEMPLATE_PATH', __DIR__ . '/templates/');
define('VENDOR_PATH', __DIR__ . '/vendor/');
define('ASSETS_PATH', '/static');

function convert_to_associative_map($my_array, $key, $value)
{
    $result = array();
    foreach ($my_array as $each_array) {
        $result[$each_array[$key]] = $each_array[$value];
    }
    return $result;
}

function convert_to_associative_array($my_array, $key)
{
    $result = array();
    foreach ($my_array as $each_array) {
        $result[$each_array[$key]] = $each_array;
    }
    return $result;
}
