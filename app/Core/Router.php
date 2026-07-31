<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimal HTTP router.
 * Registers routes and dispatches a request to a controller action.
 *
 * Usage:
 *   $router->get('/', 'HomeController@index');
 */
class Router
{
    /** @var array<string, array<string, string>> */
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, string $handler): void
    {
        $path = rtrim($path, '/') ?: '/';
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$controller, $action] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller not found: {$controllerClass}";
            return;
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $action)) {
            http_response_code(500);
            echo "Action not found: {$controller}@{$action}";
            return;
        }

        $instance->{$action}();
    }
}
