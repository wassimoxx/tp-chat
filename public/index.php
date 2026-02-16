<?php

// Show errors (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Project root
define('BASE_PATH', dirname(__DIR__));

// Load router file
require_once BASE_PATH . '/app/Core/Router.php';

// Import correct namespace
use Core\Router;

// Start application
$router = new Router();
$router->dispatch();   // if error, change to run()
