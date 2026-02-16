<?php

// =======================================
// SHOW ERRORS (remove later in production)
// =======================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// =======================================
// LOAD CORE FILES
// =======================================

// Path to project root
define('BASE_PATH', dirname(__DIR__));

// Load Router
require_once BASE_PATH . '/app/Core/Router.php';

// =======================================
// START APPLICATION
// =======================================

$router = new Router();
$router->dispatch();
