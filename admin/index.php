<?php
/**
 * Admin Portal Entry Point
 *
 * Bootstraps and loads the back-office administrative portal.
 */

require_once __DIR__ . '/../config/config.php';

$route = isset($_GET['route']) ? rtrim($_GET['route'], '/') : '';

if ($route === 'component-showcase') {
    $adminNavigation = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => '?route=component-showcase', 'active' => true],
        [
            'label' => 'Catalog',
            'icon' => 'shopping-bag',
            'children' => [
                ['label' => 'Products', 'url' => '#', 'active' => false],
                ['label' => 'Categories', 'url' => '#', 'active' => false],
            ]
        ],
        ['label' => 'Users', 'icon' => 'users', 'url' => '#', 'active' => false],
        ['label' => 'Settings', 'icon' => 'cog', 'url' => '#', 'active' => false]
    ];

    $title = "Admin UI Component Showcase";
    $breadcrumbs = [
        ['label' => 'Admin', 'url' => '#'],
        ['label' => 'Showcase']
    ];
    $flashMessages = [
        'success' => 'Admin dashboard system initialized successfully.',
        'warning' => 'This is a development preview showing mock data.'
    ];

    ob_start();
    include __DIR__ . '/../resources/views/admin/pages/component-showcase.php';
    $content = ob_get_clean();

    include __DIR__ . '/../resources/views/admin/layouts/admin.php';
    exit;
}

echo "Gift Vibe LK - Admin Portal under construction. Go to <a href='?route=component-showcase' style='color:#841f3b;text-decoration:underline;'>component-showcase</a> to test UI.";
