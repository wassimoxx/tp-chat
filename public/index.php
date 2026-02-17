<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/');

require_once BASE_PATH . '/app/Core/Router.php';

use Core\Router;

$router = new Router();
$router->dispatch();
