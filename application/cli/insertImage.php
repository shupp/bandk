<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(APPLICATION_PATH .  PATH_SEPARATOR . get_include_path());

if (!isset($argv[1])) {
    echo "Error: missing type\n";
}

if (!isset($argv[2])) {
    echo "Error: missing filename\n";
}

$type     = $argv[1];
$filename = $argv[2];

require 'bootstrap.php';

$model = new Model_Images();
$model->addImage($type, $filename);
