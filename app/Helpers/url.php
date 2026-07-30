<?php

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $path = trim($path);
        if ($path === '') {
            return BASE_URL;
        }

        return BASE_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url(ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}
