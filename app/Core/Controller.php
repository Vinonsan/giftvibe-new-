<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller that every controller should extend.
 */
abstract class Controller
{
    /**
     * Render a view directly to the response.
     */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    /**
     * Render a view and return it as a string (e.g. to pass as layout content).
     */
    protected function render(string $view, array $data = []): string
    {
        return View::renderToString($view, $data);
    }
}
