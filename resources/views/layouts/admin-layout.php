<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? ($title ?? 'Dashboard');

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin';
$basePath = app_base_path();
if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath)) ?: '/';
}

$sidebarMenu = [
    [
        'label' => 'Dashboard',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
        'href'  => '/admin',
    ],
    [
        'label' => 'Home Page',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>',
        'href'  => '#',
        'children' => [
            ['label' => 'Hero Banners',  'href' => '/admin/hero'],
            ['label' => 'Page CTAs',     'href' => '/admin/cta'],
            ['label' => 'Reviews',       'href' => '/admin/reviews'],
            ['label' => 'FAQs',          'href' => '/admin/faqs'],
        ],
    ],
    [
        'label' => 'Product Catalog',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>',
        'href'  => '#',
        'children' => [
            ['label' => 'All Products',  'href' => '/admin/products'],
            ['label' => 'Categories',    'href' => '/admin/categories'],
            ['label' => 'Combos',        'href' => '/admin/combos'],
        ],
    ],
    [
        'label' => 'Order Management',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>',
        'href'  => '#',
        'children' => [
            ['label' => 'Orders',        'href' => '/admin/orders'],
            ['label' => 'Customers',     'href' => '/admin/customers'],
            ['label' => 'Messages',      'href' => '/admin/messages'],
        ],
    ],
    [
        'label' => 'Financials',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-1.971-.659-1.172-.879-1.172-2.303 0-3.182 1.172-.879 3.07-.879 4.242 0L15 8.818M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'href'  => '#',
        'children' => [
            ['label' => 'Financial Reports', 'href' => '/admin/finance'],
            ['label' => 'Expenses',      'href' => '/admin/expenses'],
            ['label' => 'Inventory',     'href' => '/admin/inventory'],
            ['label' => 'Investments',   'href' => '/admin/investments'],
        ],
    ],
    [
        'label' => 'Settings',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        'href'  => '/admin/settings',
        'placement' => 'footer',
    ],
];

$adminUser = $adminUser ?? [
    'name'   => 'Vinonsan',
    'role'   => 'Administrator',
    'email'  => 'admin@giftvibe.lk',
    'avatar' => null,
];

// Build the notification panel from the order table itself so older orders
// remain available even when they pre-date admin_notifications records.
$adminNotifications = [];
$adminUnreadNotificationCount = 0;
try {
    $notificationPdo = \App\Core\Database::connection();
    $adminNotifications = $notificationPdo->query(
        "SELECT o.id AS entity_id, o.order_number, o.customer_name, o.grand_total,
                o.order_status, o.created_at,
                COALESCE(n.status, 'unread') AS notification_status
         FROM orders o
         LEFT JOIN admin_notifications n
           ON n.entity_type = 'order' AND n.entity_id = o.id
         ORDER BY o.id DESC
         LIMIT 30"
    )->fetchAll(\PDO::FETCH_ASSOC);
    $adminUnreadNotificationCount = count(array_filter(
        $adminNotifications,
        static fn (array $notification): bool => $notification['notification_status'] === 'unread'
    ));
} catch (\Throwable) {
    // Keep admin pages usable during a fresh installation before tables exist.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Admin &mdash; Gift Vibe</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= app_url('/favicon_io/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= app_url('/favicon_io/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= app_url('/favicon_io/favicon-16x16.png') ?>">
    <link rel="shortcut icon" href="<?= app_url('/favicon_io/favicon.ico') ?>" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <?php require BASE_PATH . '/resources/views/components/admin/tailwind-head.php'; ?>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <style>
        #admin-sidebar {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out !important;
        }
        [data-admin-content] {
            transition: padding-left 0.3s ease-in-out, width 0.3s ease-in-out !important;
        }

        @media (min-width: 1024px) {
            body.sidebar-collapsed [data-admin-content] {
                padding-left: 5rem !important;
            }
            body.sidebar-collapsed #admin-sidebar {
                width: 5rem !important;
            }
            body.sidebar-collapsed #admin-sidebar [data-sidebar-text],
            body.sidebar-collapsed #admin-sidebar [data-sidebar-chevron],
            body.sidebar-collapsed #admin-sidebar [data-sidebar-submenu],
            body.sidebar-collapsed #admin-sidebar [data-sidebar-logo-text] {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            body.sidebar-collapsed #admin-sidebar [data-sidebar-group] button,
            body.sidebar-collapsed #admin-sidebar a {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            body.sidebar-collapsed #admin-sidebar [data-sidebar-group] button span,
            body.sidebar-collapsed #admin-sidebar a span {
                margin: 0 !important;
            }
        }
    </style>
    <script>
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('sidebar-collapsed');
            });
        }
    </script>
</head>
<body class="bg-white font-sans text-slate-800 antialiased">
    <?php require BASE_PATH . '/resources/views/components/base/feedback.php'; ?>
    <?php require BASE_PATH . '/resources/views/components/navigation/admin-sidebar.php'; ?>

    <div class="flex min-h-screen min-w-0 flex-col lg:pl-72" data-admin-content>
        <?php require BASE_PATH . '/resources/views/components/navigation/admin-navbar.php'; ?>

        <main class="min-w-0 flex-1 overflow-x-hidden p-4 sm:p-6" data-admin-main>
            <div class="mx-auto w-full min-w-0 max-w-[1600px]">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</body>
</html>
