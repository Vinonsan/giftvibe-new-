<?php

namespace App\Core;

use Exception;

class View
{
    public static function renderPage(string $view, array $data = [], string $layout = 'public/layouts/main'): void
    {
        $content = self::capture($view, $data);
        $data['content'] = $content;

        self::renderComponent($layout, $data);
    }

    public static function capture(string $name, array $data = []): string
    {
        ob_start();
        self::renderComponent($name, $data);

        return ob_get_clean();
    }

    /**
     * Render a component safely.
     *
     * @param string $name Name of the component relative to resources/views
     * @param array $data Variables to pass to the component
     * @throws Exception If component file is not found or is outside views directory
     */
    public static function renderComponent(string $name, array $data = []): void
    {
        // 1. Sanitize name to prevent path traversal
        $cleanName = str_replace(['..', "\0"], '', $name);
        $cleanName = ltrim(str_replace('\\', '/', $cleanName), '/');

        // Resolve absolute views path
        $viewsPath = realpath(__DIR__ . '/../../resources/views');
        if (!$viewsPath) {
            throw new Exception("Views directory not found.");
        }

        // Target path
        $suffix = (str_ends_with($cleanName, '.php')) ? '' : '.php';
        $componentPath = $viewsPath . '/' . $cleanName . $suffix;
        $realComponentPath = realpath($componentPath);

        // 2. Path traversal check (realpath must reside within the views root directory)
        if (!$realComponentPath || strpos($realComponentPath, $viewsPath) !== 0) {
            throw new Exception("Component file not found or access denied: " . htmlspecialchars($name));
        }

        // 3. Render inside closed scope
        (static function() use ($realComponentPath, $data) {
            extract($data, EXTR_SKIP);
            include $realComponentPath;
        })();
    }
}
