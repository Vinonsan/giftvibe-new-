<?php
namespace Components\Layouts;

final class AppLayout
{
    public static function render(string $title, string $content, string $activeNav = ''): void
    {
        $userName = $_SESSION['user_name'] ?? 'User';
        ?>
<!DOCTYPE html>
<html lang="en" x-data="{ sidebarExpanded: true, managementOpen: <?= in_array($activeNav, ['users', 'billing', 'inventory'], true) ? 'true' : 'false' ?>, profileOpen: false, open: '', drawer: '' }" @keydown.escape.window="profileOpen = false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | <?= \APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= \BASE_URL ?>/assets/img/giftvibe-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: <?= \adminTailwindColorsJs() ?> } } };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white text-dark antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col bg-white border-r border-primary-100 overflow-y-auto transition-all duration-300" :class="sidebarExpanded ? 'w-64' : 'w-20'">
        <div class="flex items-center gap-2 px-4 py-4 border-b border-primary-100">
            <img src="<?= \BASE_URL ?>/assets/img/giftvibe-logo.png" alt="<?= htmlspecialchars(\APP_NAME) ?>" class="w-10 h-10 rounded-xl object-cover shrink-0">
            <span x-show="sidebarExpanded" class="min-w-0 flex-1 text-lg font-bold text-primary-700 whitespace-nowrap"><?= \APP_NAME ?></span>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            <button @click="sidebarExpanded = !sidebarExpanded" class="mb-3 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-primary-700 hover:bg-primary-50"><i data-lucide="panel-left-close" class="w-5 h-5 shrink-0" :class="!sidebarExpanded && 'rotate-180'"></i><span x-show="sidebarExpanded" class="text-sm font-semibold">Collapse sidebar</span></button>
            <?= self::navItem('Dashboard', '/admin', 'layout-dashboard', $activeNav === 'dashboard') ?>
            <button @click="if (!sidebarExpanded) sidebarExpanded = true; managementOpen = !managementOpen" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-secondary hover:bg-secondary-100"><i data-lucide="settings-2" class="w-5 h-5 shrink-0"></i><span x-show="sidebarExpanded" class="flex-1 text-left">Management</span><i x-show="sidebarExpanded" data-lucide="chevron-down" class="w-4 h-4" :class="managementOpen && 'rotate-180'"></i></button>
            <div x-cloak x-show="sidebarExpanded && managementOpen" class="ml-4 space-y-1 border-l border-primary-100 pl-3">
                <?= self::navItem('Users', '/admin/users', 'users', $activeNav === 'users') ?>
                <?= self::navItem('Billing', '/admin/billing', 'receipt', $activeNav === 'billing') ?>
                <?= self::navItem('Inventory', '/admin/inventory', 'package', $activeNav === 'inventory') ?>
            </div>
        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Nav -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($title) ?></h2>
            </div>
            <div class="relative" @click.outside="profileOpen = false">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 rounded-xl border border-primary-100 px-2.5 py-1.5 hover:bg-primary-50"><span class="flex w-8 h-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white"><?= strtoupper(substr($userName, 0, 1)) ?></span><span class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($userName) ?></span><i data-lucide="chevron-down" class="w-4 h-4"></i></button>
                <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 z-50 mt-2 w-44 rounded-xl border bg-white p-2 shadow-xl"><a href="<?= \BASE_URL ?>/logout" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"><i data-lucide="log-out" class="w-4 h-4"></i>Logout</a></div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <?= $content ?>
        </main>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
<?php
    }

    private static function navItem(string $label, string $href, string $icon, bool $active): string
    {
        $activeClass = $active ? 'bg-primary-50 text-primary-700' : 'text-secondary hover:bg-secondary-100';
        return '<a href="' . \BASE_URL . $href . '" title="' . $label . '" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition ' . $activeClass . '">'
             . '<i data-lucide="' . $icon . '" class="w-5 h-5 shrink-0"></i><span x-show="sidebarExpanded">' . $label . '</span></a>';
    }
}
