<?php

defined('APPLICATION_PATH') or define('APPLICATION_PATH', dirname(__FILE__));

function __autoload($class)
{
    $file = str_replace('_', '/', $class) . '.php';

    // Models
    if (preg_match('/^Model_/', $class)) {
        require_once APPLICATION_PATH . '/models/' . $file;
        return;
    }

    // Controllers
    if (preg_match('/^.*Controller$/', $class)) {
        require_once APPLICATION_PATH . '/controllers/' . $file;
        return;
    }

    // Everything else
    require_once $file;
}

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');

$frontController = Zend_Controller_Front::getInstance();
$frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');
$frontController->setParam('env', APPLICATION_ENVIRONMENT);

Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');
$view = Zend_Layout::getMvcInstance()->getView();
$view->doctype('XHTML1_STRICT');

$configuration = new Zend_Config_Ini(
    APPLICATION_PATH . '/config/app.ini', 
    APPLICATION_ENVIRONMENT
);

$registry = Zend_Registry::getInstance();
$registry->configuration = $configuration;
$registry->cache = Zend_Cache::factory(
    'Core',
    'Memcached',
    array('lifetime' => 3600)
);
$registry->db = Zend_Db::factory($configuration->database);
$registry->session = new Zend_Session_Namespace('boobsandkittens');
$registry->mogile = new File_Mogile(array($configuration->mogile->server),
                                    $configuration->mogile->domain);
unset($frontController, $view, $configuration, $registry);
