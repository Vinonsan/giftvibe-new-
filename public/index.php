<?php

declare(strict_types=1);

/* Let PHP's development server serve existing static assets directly. */
if (PHP_SAPI === 'cli-server') {
    $requestPath = rawurldecode((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
    $publicFile = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $requestPath);
    if ($requestPath !== '/' && is_file($publicFile)) {
        return false;
    }
}

/**
 * Front controller — the single entry point for the application.
 *
 * Flow: define paths → autoloader → load routes → dispatch request.
 */

define('BASE_PATH', dirname(__DIR__));

/* Bust OPcache for recently edited admin controllers (XAMPP dev). */
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(BASE_PATH . '/app/Controllers/Admin/OrderController.php', true);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionPath = BASE_PATH . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }
    session_save_path($sessionPath);
    session_start();
}

/* Simple PSR-4 style autoloader for the App\ namespace (app/ folder). */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

/**
 * Return the public URL for an asset inside public/assets/.
 */
function asset(string $path): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');

    return $base . '/assets/' . ltrim($path, '/');
}

$router = new App\Core\Router();

require BASE_PATH . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
);
