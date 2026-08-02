<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Tiny view renderer.
 * Extracts data into variables and includes the view file.
 */
class View
{
    public static function render(string $view, array $data = []): void
    {
        echo static::renderToString($view, $data);
    }

    public static function renderToString(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        $file = BASE_PATH . '/resources/views/' . $view . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$file}");
        }

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
