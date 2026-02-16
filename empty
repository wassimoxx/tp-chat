<?php
/**
 * Front Controller - Entry point for all requests
 */

session_start();

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Define URL base path (for subdirectory support)
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
// Standardize slashes
$scriptDir = str_replace('\\', '/', $scriptDir);
// Remove trailing slash if present (except root)
$baseUrl = ($scriptDir === '/') ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $baseUrl);

// Simple autoloader
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load core classes
require_once APP_PATH . '/Core/Router.php';
require_once APP_PATH . '/Core/Controller.php';
require_once APP_PATH . '/Core/View.php';

// Load models
require_once APP_PATH . '/Models/UserStore.php';
require_once APP_PATH . '/Models/ChatStore.php';

// Load controllers
require_once APP_PATH . '/Controllers/AuthController.php';
require_once APP_PATH . '/Controllers/ChatController.php';

use Core\Router;

// Create router instance
$router = new Router();

// Register routes
$router->get('/', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');
$router->get('/chat', 'ChatController@showRoom');
$router->post('/chat/send', 'ChatController@send');
$router->get('/chat/poll', 'ChatController@poll');

// Dispatch the request
$router->dispatch();
