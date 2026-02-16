<?php

namespace Core;

/**
 * Base Controller class
 */
abstract class Controller
{
    /**
     * Render a view
     */
    protected function view(string $name, array $data = []): void
    {
        View::render($name, $data);
    }

    /**
     * Send JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void
    {
        // Prepend BASE_URL to internal paths
        if (strpos($url, '/') === 0) {
            $url = BASE_URL . $url;
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Require authentication, redirect to login if not authenticated
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/');
        }
    }

    /**
     * Get current logged in username
     */
    protected function currentUser(): ?string
    {
        return $_SESSION['user'] ?? null;
    }
}
