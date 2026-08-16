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

require BASE_PATH . '/app/Helpers/url.php';

$scriptDir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
define('APP_BASE_PATH', str_ends_with($scriptDir, '/public')
    ? (rtrim(substr($scriptDir, 0, -7), '/') === '/' ? '' : rtrim(substr($scriptDir, 0, -7), '/'))
    : (rtrim($scriptDir, '/') === '/' ? '' : rtrim($scriptDir, '/')));

/* Bust OPcache for recently edited admin controllers (XAMPP dev). */
if (function_exists('opcache_invalidate')) {
    opcache_invalidate(BASE_PATH . '/app/Controllers/Admin/OrderController.php', true);
    opcache_invalidate(BASE_PATH . '/app/Controllers/Admin/CustomerController.php', true);
    opcache_invalidate(BASE_PATH . '/app/Controllers/Admin/FinanceController.php', true);
    opcache_invalidate(BASE_PATH . '/app/Controllers/Admin/ExpenseController.php', true);
    opcache_invalidate(BASE_PATH . '/app/Services/FinanceSummaryService.php', true);
    opcache_invalidate(BASE_PATH . '/app/Controllers/Public/CheckoutController.php', true);
    opcache_invalidate(BASE_PATH . '/resources/views/components/navigation/navbar.php', true);
    opcache_invalidate(BASE_PATH . '/resources/views/public/auth/login.php', true);
    opcache_invalidate(BASE_PATH . '/resources/views/public/checkout/index.php', true);
    opcache_invalidate(BASE_PATH . '/resources/views/public/checkout/success.php', true);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.gc_maxlifetime', (string) (60 * 60 * 24 * 365));
    $sessionPath = BASE_PATH . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }
    session_save_path($sessionPath);
    session_start();
}

if (isset($_SESSION['user']['id'])) {
    setcookie(session_name(), session_id(), [
        'expires' => time() + (60 * 60 * 24 * 365),
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
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

$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$basePath = app_base_path();

if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $requestPath . (str_contains($requestUri, '?') ? '?' . (parse_url($requestUri, PHP_URL_QUERY) ?? '') : ''),
);
