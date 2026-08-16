<?php
declare(strict_types=1);

/* Adapt combo data to the exact Product Single View template. */
$product = [
    'id' => $combo['id'],
    'name' => $combo['name'],
    'slug' => $combo['slug'],
    'sku' => 'COMBO-' . $combo['id'],
    'base_price' => $combo['price'],
    'sale_price' => null,
    'stock_quantity' => 1,
    'image_path' => $combo['image_path'] ?? ($gallery[0]['image_path'] ?? '/assets/images/combo-showcase-giftvibe.png'),
    'category_names' => 'GiftVibe combo',
    'short_description' => $combo['description'],
    'description' => '',
    'video_url' => '',
    '_type' => 'combo',
];
$productVideos = $comboVideos;
$relatedProducts = $comboProducts;
$relatedHeading = 'Products included in this combo';
$relatedDescription = 'Every product selected by the admin for this combo is shown below.';
$detailBackUrl = '/combos';
require BASE_PATH . '/resources/views/public/product/show.php';
