<?php
/**
 * Public Portal Entry Point
 *
 * Bootstraps and loads the public-facing customer portal.
 */

require_once __DIR__ . '/../config/config.php';

$route = isset($_GET['route']) ? rtrim($_GET['route'], '/') : '';

// 1. Navigation items
$navigation = [
    ['label' => 'Home', 'url' => 'index.php', 'active' => ($route === '')],
    ['label' => 'Shop All', 'url' => '?route=component-showcase', 'active' => false],
    [
        'label' => 'Occasions',
        'url' => '#',
        'active' => false,
        'children' => [
            ['label' => 'Birthday Gifts', 'url' => '?route=component-showcase'],
            ['label' => 'Anniversary Gifts', 'url' => '?route=component-showcase'],
        ]
    ],
    ['label' => 'Component Showcase', 'url' => '?route=component-showcase', 'active' => ($route === 'component-showcase')]
];

// 2. Routing logic
if ($route === 'component-showcase') {
    $title = "Public UI Component Showcase";
    
    ob_start();
    include __DIR__ . '/../resources/views/public/pages/component-showcase.php';
    $content = ob_get_clean();
    
    include __DIR__ . '/../resources/views/public/layouts/main.php';
    exit;
} else {
    // Default Home page
    $title = "Send Premium Gifts to Sri Lanka | Gift Vibe LK";
    $meta = [
        'description' => 'Send premium, handpicked gift boxes, fresh flower bouquets, and gourmet chocolates to Sri Lanka. Fast, same-day delivery across Colombo and major cities.',
        'image' => BASE_URL . '/public/assets/images/hero_gift_box.jpg'
    ];
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'GiftStore',
        'name' => 'Gift Vibe LK',
        'description' => 'Premium Gift Delivery Shop in Sri Lanka',
        'url' => BASE_URL,
        'logo' => BASE_URL . '/public/assets/images/hero_gift_box.jpg',
        'telephone' => '+94771234567',
        'priceRange' => '$$'
    ];

    ob_start();
    include __DIR__ . '/../resources/views/public/pages/home.php';
    $content = ob_get_clean();
    
    include __DIR__ . '/../resources/views/public/layouts/main.php';
    exit;
}
