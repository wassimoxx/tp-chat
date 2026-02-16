<?php

namespace Core;

/**
 * Simple Router class to handle URL routing
 */
class Router
{
    private array $routes = [];

    /**
     * Register a GET route
     */
    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch the current request to the appropriate controller
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip BASE_URL from URI if it exists
        if (BASE_URL && strpos($uri, BASE_URL) === 0) {
            $uri = substr($uri, strlen(BASE_URL));
        }

        // Remove trailing slash (except for root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Ensure URI starts with /
        if (empty($uri)) {
            $uri = '/';
        }

        // Check if route exists
        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        $handler = $this->routes[$method][$uri];
        $this->executeHandler($handler);
    }

    /**
     * Execute a controller method
     */
    private function executeHandler(string $handler): void
    {
        [$controllerName, $methodName] = explode('@', $handler);

        $controllerClass = "Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller not found: {$controllerClass}";
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Method not found: {$methodName}";
            return;
        }

        $controller->$methodName();
    }
}
