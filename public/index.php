<?php

// Show errors (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Project root
define('BASE_PATH', dirname(__DIR__));

// Define base URL
define('BASE_URL', '/');

// Load router
require_once BASE_PATH . '/app/Core/Router.php';

// Import namespace
use Core\Router;

/* ======================================
   REDIRECT HOMEPAGE (FIX 404)
   Change /login to your real first page
====================================== */
if ($_SERVER['REQUEST_URI'] === '/') {
    header('Location: /login');   // change if needed
    exit;
}

// Run application
$router = new Router();
$router->dispatch();
