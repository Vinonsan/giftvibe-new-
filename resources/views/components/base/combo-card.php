<?php
declare(strict_types=1);

/* Normalize combo data and render the exact reusable Product Card UI. */
$combo = $combo ?? [];
$productCardHref = '/combos?combo=' . rawurlencode((string) ($combo['slug'] ?? ''));
$product = [
    'id' => $combo['id'] ?? null,
    'name' => $combo['name'] ?? 'Gift combo',
    'slug' => $combo['slug'] ?? '',
    'image_path' => $combo['image_path'] ?? '/assets/images/combo-showcase-giftvibe.png',
    'base_price' => $combo['price'] ?? 0,
    'sale_price' => null,
    'category_names' => (int) ($combo['product_count'] ?? 0) . ' products included',
    'is_featured' => 0,
];
require BASE_PATH . '/resources/views/components/base/product-card.php';
