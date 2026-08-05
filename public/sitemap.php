<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/Database.php';

use App\Core\Database;

header('Content-Type: application/xml; charset=UTF-8');
$siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
$urls = ['/', '/shop', '/combos', '/services', '/blog', '/reviews', '/about', '/contact', '/privacy', '/terms'];
$pdo = Database::connection();
foreach ($pdo->query("SELECT slug FROM categories WHERE status='active' ORDER BY id")->fetchAll() as $row) $urls[] = '/shop?category=' . rawurlencode((string) $row['slug']);
foreach ($pdo->query("SELECT slug FROM products WHERE status='active' ORDER BY id")->fetchAll() as $row) $urls[] = '/shop?product=' . rawurlencode((string) $row['slug']);
foreach ($pdo->query("SELECT slug FROM combos WHERE status='active' ORDER BY id")->fetchAll() as $row) $urls[] = '/combos?combo=' . rawurlencode((string) $row['slug']);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $path) echo '  <url><loc>' . htmlspecialchars($siteUrl . $path, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc></url>\n";
echo "</urlset>\n";
