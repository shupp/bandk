<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
set_include_path(APPLICATION_PATH .  PATH_SEPARATOR . get_include_path());

try {
    require '../application/bootstrap.php';
} catch (Exception $exception) {
    echo '<html><body><center>'
       . 'An exception occured while bootstrapping the application.';
    if (defined('APPLICATION_ENVIRONMENT')
        && APPLICATION_ENVIRONMENT != 'production'
    ) {
        echo '<br /><br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:' 
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }
    echo '</center></body></html>';
    exit(1);
}


$controller = Zend_Controller_Front::getInstance();
$controller->dispatch();
