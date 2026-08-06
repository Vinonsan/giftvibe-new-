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
        
        // Define priority order: Home (1.0), Shop (0.9), About (0.8), Services (0.7), Contact (0.6), Blog (0.5)
        $urls = [
            ['path' => '/', 'priority' => '1.0', 'changefreq' => 'daily', 'modified' => null],
            ['path' => '/shop', 'priority' => '0.9', 'changefreq' => 'daily', 'modified' => null],
            ['path' => '/about', 'priority' => '0.8', 'changefreq' => 'weekly', 'modified' => null],
            ['path' => '/services', 'priority' => '0.7', 'changefreq' => 'weekly', 'modified' => null],
            ['path' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly', 'modified' => null],
            ['path' => '/blog', 'priority' => '0.5', 'changefreq' => 'daily', 'modified' => null],
            ['path' => '/combos', 'priority' => '0.4', 'changefreq' => 'daily', 'modified' => null],
            ['path' => '/reviews', 'priority' => '0.4', 'changefreq' => 'daily', 'modified' => null],
            ['path' => '/privacy', 'priority' => '0.3', 'changefreq' => 'yearly', 'modified' => null],
            ['path' => '/terms', 'priority' => '0.3', 'changefreq' => 'yearly', 'modified' => null],
        ];

        $pdo = Database::connection();
        foreach ($pdo->query("SELECT slug, updated_at FROM categories WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/shop?category=' . rawurlencode((string) $row['slug']), 'priority' => '0.4', 'changefreq' => 'weekly', 'modified' => $row['updated_at']];
        }
        foreach ($pdo->query("SELECT slug, updated_at FROM products WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/shop?product=' . rawurlencode((string) $row['slug']), 'priority' => '0.4', 'changefreq' => 'weekly', 'modified' => $row['updated_at']];
        }
        foreach ($pdo->query("SELECT slug, updated_at FROM combos WHERE status='active' ORDER BY id")->fetchAll() as $row) {
            $urls[] = ['path' => '/combos?combo=' . rawurlencode((string) $row['slug']), 'priority' => '0.4', 'changefreq' => 'weekly', 'modified' => $row['updated_at']];
        }

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($urls as $url) {
            $location = htmlspecialchars($siteUrl . $url['path'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
            echo "  <url>\n";
            echo "    <loc>{$location}</loc>\n";
            if (!empty($url['modified'])) {
                echo '    <lastmod>' . htmlspecialchars(date('c', strtotime((string) $url['modified'])), ENT_XML1) . "</lastmod>\n";
            }
            echo "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            echo "    <priority>" . $url['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo "</urlset>\n";
    }
}
