<?php

namespace App\Core;

use Closure;
use RuntimeException;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->normalizePath($path),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function load(string $file): self
    {
        if (!is_file($file)) {
            throw new RuntimeException("Route file not found: {$file}");
        }

        $routes = require $file;
        foreach ($routes as $route) {
            $this->add(
                $route['method'],
                $route['path'],
                $route['handler'],
                $route['middleware'] ?? []
            );
        }

        return $this;
    }

    public function dispatch(?string $method = null, ?string $uri = null): mixed
    {
        $method = strtoupper($method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path = $this->normalizePath($uri ?? $this->currentPath());

        foreach ($this->routes as $route) {
            $params = $this->matches($route['path'], $path);
            if ($route['method'] !== $method || $params === null) {
                continue;
            }

            foreach ($route['middleware'] as $middleware) {
                $this->runMiddleware($middleware);
            }

            return $this->call($route['handler'], $params);
        }

        http_response_code(404);
        return View::renderPage('public/pages/404', [
            'title' => 'Page Not Found | Gift Vibe LK',
        ], 'public/layouts/main');
    }

    private function currentPath(): string
    {
        if (isset($_GET['route'])) {
            return (string) $_GET['route'];
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = parse_url(BASE_URL, PHP_URL_PATH) ?: '';

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return $path;
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);
        $path = preg_replace('#/+#', '/', $path);
        $path = trim($path, '/');

        return $path === '' ? '/' : '/' . $path;
    }

    private function matches(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function runMiddleware(string|object $middleware): void
    {
        $instance = is_string($middleware) ? new $middleware() : $middleware;
        if (!method_exists($instance, 'handle')) {
            throw new RuntimeException('Middleware must expose a handle method.');
        }

        $instance->handle();
    }

    private function call(callable|array $handler, array $params): mixed
    {
        if ($handler instanceof Closure || is_callable($handler)) {
            return $handler(...array_values($params));
        }

        [$class, $method] = $handler;
        $controller = new $class();

        return $controller->{$method}(...array_values($params));
    }
}
