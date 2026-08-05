<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Database;

final class SeoController
{
    private function siteUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
        return rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        $siteUrl = $this->siteUrl();
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /cart\n";
        echo "Sitemap: {$siteUrl}/sitemap.xml\n";
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        $siteUrl = $this->siteUrl();
        $urls = [
            ['path' => '/', 'modified' => null],
            ['path' => '/shop', 'modified' => null],
            ['path' => '/combos', 'modified' => null],
            ['path' => '/services', 'modified' => null],
            ['path' => '/blog', 'modified' => null],
            ['path' => '/reviews', 'modified' => null],
            ['path' => '/about', 'modified' => null],
            ['path' => '/contact', 'modified' => null],
            ['path' => '/privacy', 'modified' => null],
            ['path' => '/terms', 'modified' => null],
        ];

        $pdo = Database::connection();
        foreach ($pdo->query("SELECT slug, updated_at FROM categories WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/shop?category=' . rawurlencode((string) $row['slug']), 'modified' => $row['updated_at']];
        }
        foreach ($pdo->query("SELECT slug, updated_at FROM products WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/shop?product=' . rawurlencode((string) $row['slug']), 'modified' => $row['updated_at']];
        }
        foreach ($pdo->query("SELECT slug, updated_at FROM combos WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/combos?combo=' . rawurlencode((string) $row['slug']), 'modified' => $row['updated_at']];
        }

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $url) {
            $location = htmlspecialchars($siteUrl . $url['path'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
            echo "  <url><loc>{$location}</loc>";
            if (!empty($url['modified'])) echo '<lastmod>' . htmlspecialchars(date('c', strtotime((string) $url['modified'])), ENT_XML1) . '</lastmod>';
            echo "</url>\n";
        }
        echo "</urlset>\n";
    }
}
