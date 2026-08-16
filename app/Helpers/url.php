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

    if (preg_match('~^https?://~i', $path) === 1) {
        return $path;
    }

    $basePath = app_base_path();
    if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
        return $path;
    }

    if (!str_starts_with($path, '/')) {
        $path = '/' . $path;
    }

    return $basePath . $path;
}

/** Public static asset URL (files under public/assets/). */
function app_asset(string $path = ''): string
{
    return app_url('/assets/' . ltrim($path, '/'));
}

function dicebear_avatar_url(string $seed, int $size = 128): string
{
    return 'https://api.dicebear.com/10.x/avataaars/svg?seed=' . rawurlencode($seed)
        . '&size=' . max(32, min(512, $size)) . '&backgroundColor=f3f4f6';
}
