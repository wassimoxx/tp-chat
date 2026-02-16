<?php

// Show errors (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Project root
define('BASE_PATH', dirname(__DIR__));

// Define base URL (IMPORTANT FIX)
define('BASE_URL', '/');

// Load router
require_once BASE_PATH . '/app/Core/Router.php';

// Import namespace
use Core\Router;

// Run application
$router = new Router();
$router->dispatch();
