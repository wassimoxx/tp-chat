<?php

// ================================
// SHOW ERRORS (remove in production)
// ================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ================================
// PROJECT PATH
// ================================
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/');

// ================================
// REDIRECT HOMEPAGE TO LOGIN
// ================================
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestPath === '/' || $requestPath === '') {
    header('Location: /login');
    exit;
}

// ================================
// LOAD ROUTER
// ================================
require_once BASE_PATH . '/app/Core/Router.php';

use Core\Router;

// ================================
// RUN APPLICATION
// ================================
$router = new Router();
$router->dispatch();
