<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? ($title ?? 'Dashboard');

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH) ?: '/admin';

$sidebarMenu = [
    [
        'label' => 'Dashboard',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
        'href'  => '/admin',
    ],
    [
        'label' => 'Products',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>',
        'href'  => '#',
        'children' => [
            ['label' => 'All Products',  'href' => '/admin/products'],
            ['label' => 'Categories',    'href' => '/admin/categories'],
            ['label' => 'Combos',        'href' => '/admin/combos'],
        ],
    ],
    [
        'label' => 'Hero Banners',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.75A2 2 0 015.75 3.75h12.5a2 2 0 012 2v12.5a2 2 0 01-2 2H5.75a2 2 0 01-2-2V5.75zM3.75 16l4.2-4.2a2 2 0 012.83 0l1.47 1.47 1.2-1.2a2 2 0 012.83 0l3.97 3.97M15.5 8.25h.01"/></svg>',
        'href'  => '/admin/hero',
    ],
    [
        'label' => 'Page CTAs',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5.75A1.75 1.75 0 0 1 5.75 4h12.5A1.75 1.75 0 0 1 20 5.75v12.5A1.75 1.75 0 0 1 18.25 20H5.75A1.75 1.75 0 0 1 4 18.25V5.75ZM8 9h5M8 12h8M8 15h4"/></svg>',
        'href'  => '/admin/cta',
    ],
    [
        'label' => 'Orders',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>',
        'href'  => '/admin/orders',
    ],
    [
        'label' => 'Customers',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
        'href'  => '/admin/customers',
    ],
    [
        'label' => 'Messages',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>',
        'href'  => '/admin/messages',
    ],
    [
        'label' => 'Reviews',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
        'href'  => '/admin/reviews',
    ],
    [
        'label' => 'FAQs',
        'icon'  => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M9.8 9a2.4 2.4 0 1 1 3.3 2.22c-.72.33-1.1.75-1.1 1.53M12 16.5h.01"/></svg>',
        'href'  => '/admin/faqs',
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Admin &mdash; Gift Vibe</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        <?= file_get_contents(BASE_PATH . '/public/assets/css/global.css') ?>

        #admin-sidebar {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out !important;
        }
        [data-admin-content] {
            transition: padding-left 0.3s ease-in-out !important;
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
