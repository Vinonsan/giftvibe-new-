<?php

date_default_timezone_set('Asia/Colombo');

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('RESOURCE_PATH', BASE_PATH . '/resources');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

function loadEnv(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return match (strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        'empty', '(empty)' => '',
        default => $value,
    };
}

loadEnv(BASE_PATH . '/.env');

require_once CONFIG_PATH . '/theme.php';

define('APP_NAME', (string) env('APP_NAME', 'Gift Vibe LK'));
define('APP_ENV', (string) env('APP_ENV', 'local'));
define('APP_DEBUG', (bool) env('APP_DEBUG', true));

$configuredUrl = rtrim((string) env('APP_URL', ''), '/');
if ($configuredUrl !== '') {
    define('BASE_URL', $configuredUrl);
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $subfolder = rtrim(str_replace('\\', '/', dirname($scriptName)), '/.');
    $subfolder = preg_replace('#/(public|admin)$#', '', $subfolder);
    define('BASE_URL', rtrim($protocol . $host . $subfolder, '/'));
}

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\' => BASE_PATH . '/app/',
        'Controllers\\' => BASE_PATH . '/src/Controllers/',
        'Models\\' => BASE_PATH . '/src/Models/',
        'Services\\' => BASE_PATH . '/src/Services/',
        'Middleware\\' => BASE_PATH . '/src/Middleware/',
        'Helpers\\' => BASE_PATH . '/src/Helpers/',
        'Components\\' => BASE_PATH . '/src/Components/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
            continue;
        }

        $file = $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

foreach ([
    BASE_PATH . '/app/Helpers/component.php',
    BASE_PATH . '/app/Helpers/url.php',
    BASE_PATH . '/app/Helpers/security.php',
] as $helperFile) {
    if (is_file($helperFile)) {
        require_once $helperFile;
    }
}

if (session_status() === PHP_SESSION_NONE) {
    if (is_dir(STORAGE_PATH . '/sessions') && is_writable(STORAGE_PATH . '/sessions')) {
        session_save_path(STORAGE_PATH . '/sessions');
    }

    session_name('giftvibe_session');
    session_start([
        'cookie_lifetime' => 0,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'use_strict_mode' => true,
    ]);
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
