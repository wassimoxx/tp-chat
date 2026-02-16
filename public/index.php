<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/Core/Router.php';

$router = new Router();
$router->dispatch();   // or run() if dispatch not exist
