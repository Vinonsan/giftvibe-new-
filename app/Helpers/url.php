<?php

declare(strict_types=1);

/**
 * Application base path when running in a subdirectory (e.g. /giftvibe-new- on XAMPP).
 * Empty string on cPanel when document root is public/.
 */
function app_base_path(): string
{
    if (defined('APP_BASE_PATH')) {
        return APP_BASE_PATH;
    }

    $scriptDir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
    $basePath = str_ends_with($scriptDir, '/public')
        ? rtrim(substr($scriptDir, 0, -7), '/')
        : rtrim($scriptDir, '/');

    if ($basePath === '/') {
        $basePath = '';
    }

    return $basePath;
}

function app_url(string $path = '/'): string
{
    $path = $path === '' ? '/' : $path;
    if (!str_starts_with($path, '/')) {
        $path = '/' . $path;
    }

    return app_base_path() . $path;
}

/** Public static asset URL (files under public/assets/). */
function app_asset(string $path = ''): string
{
    return app_url('/assets/' . ltrim($path, '/'));
}
