<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';
setlocale (LC_TIME, 'fr_FR.utf8','fra');
session_start();
/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('catalogue', ['controller' => 'Enchere', 'action' => 'index']);
$router->add('enchere', ['controller' => 'Enchere', 'action' => 'index']);
$router->add('membre', ['controller' => 'Membre', 'action' => 'index']);

$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\w+}');

$router->dispatch($_SERVER['QUERY_STRING']);
