<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/Database.php';

use App\Core\Database;

header('Content-Type: text/markdown; charset=UTF-8');
$siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
$products = Database::connection()->query("SELECT p.name,p.slug,p.short_description,p.description,p.base_price,p.stock_quantity,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') categories FROM products p LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id WHERE p.status='active' GROUP BY p.id ORDER BY p.name")->fetchAll();
echo "# GiftVibe live product catalog\n\nThis feed is generated from the current active product database.\n\n";
foreach ($products as $product) {
    echo '## ' . $product['name'] . "\n\n";
    echo '- Canonical URL: ' . $siteUrl . '/shop?product=' . rawurlencode((string) $product['slug']) . "\n";
    echo '- Categories: ' . ($product['categories'] ?: 'GiftVibe collection') . "\n";
    echo '- Price: LKR ' . number_format((float) $product['base_price'], 2, '.', '') . "\n";
    echo '- Availability: ' . ((int) $product['stock_quantity'] > 0 ? 'In stock' : 'Out of stock') . "\n";
    echo '- Summary: ' . trim((string) ($product['short_description'] ?: $product['description'])) . "\n\n";
}
