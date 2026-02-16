<?php

namespace Core;

/**
 * Simple View class for rendering templates
 */
class View
{
    /**
     * Render a view file
     */
    public static function render(string $name, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Build view file path
        $viewFile = APP_PATH . '/Views/' . $name . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$name}";
            return;
        }

        // Include the view file
        include $viewFile;
    }

    /**
     * Escape output for safe HTML display
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
