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

        $html = (string) ob_get_clean();

        // Database-backed media paths are stored as production-root URLs
        // (for example, /assets/uploads/...). When the app runs from an
        // XAMPP subdirectory, prefix those public paths with the detected
        // application base path. On production app_base_path() is empty, so
        // the rendered URLs remain unchanged.
        $basePath = app_base_path();
        if ($basePath !== '') {
            $quotedBasePath = preg_quote(ltrim($basePath, '/'), '~');
            // Keep every internal link and form action inside the application
            // when XAMPP serves it from /giftvibe-new- (or another subfolder).
            $html = preg_replace(
                '~((?:href|action)=["\'])/(?!' . $quotedBasePath . '(?:/|["\']))~i',
                '$1' . $basePath . '/',
                $html,
            ) ?? $html;
            $html = preg_replace(
                '~(["\'])/(?!' . $quotedBasePath . '/)(assets|favicon_io)/~',
                '$1' . $basePath . '/$2/',
                $html,
            ) ?? $html;
        }

        // Prefer generated WebP variants while retaining original uploads as
        // a safe source/fallback. This substantially reduces transferred bytes.
        $html = preg_replace_callback(
            '~(["\'])(' . preg_quote(app_base_path(), '~') . '/assets/[^"\']+)\.(png|jpe?g)(\?[^"\']*)?\1~i',
            static function (array $match): string {
                $relative = substr($match[2], strlen(app_base_path()));
                $webpFile = BASE_PATH . '/public' . str_replace('/', DIRECTORY_SEPARATOR, $relative) . '.webp';
                if (!is_file($webpFile)) return $match[0];
                return $match[1] . $match[2] . '.webp' . ($match[4] ?? '') . $match[1];
            },
            $html,
        ) ?? $html;

        return $html;
    }
}
