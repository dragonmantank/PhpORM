<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register('autoload');

function autoload($class) {
    $name = str_replace('_', DIRECTORY_SEPARATOR, $class);
    require_once $name.'.php';
}

//Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../'),
    get_include_path()
)));