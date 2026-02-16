<?php

// Show errors (debug only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Project root
define('BASE_PATH', dirname(__DIR__));

// Load router file
require_once BASE_PATH . '/app/Core/Router.php';

// IMPORT the class namespace (IMPORTANT)
use App\Core\Router;

// Run app
$router = new Router();
$router->dispatch();   // or run() if your router uses that
