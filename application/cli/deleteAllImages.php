<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(APPLICATION_PATH .  PATH_SEPARATOR . get_include_path());

require 'bootstrap.php';

$model = new Model_Images();

foreach ($model->getAllBoobsIDs() as $id) {
    $type = Model_Images::TYPE_BOOBS;
    $key  = $type . '-' . $id;
    echo "deleting " . $key . "\n";
    try {
        $model->deleteImage($type, $id);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
}

foreach ($model->getAllKittensIDs() as $id) {
    $type = Model_Images::TYPE_KITTENS;
    $key  = $type . '-' . $id;
    echo "deleting " . $key . "\n";
    try {
        $model->deleteImage($type, $id);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
}
