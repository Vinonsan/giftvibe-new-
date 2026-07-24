<?php
// src/Views/layouts/app_layout.php
function renderAppLayout(string $title, string $contentView, array $data = [], string $activeNav = ''): void
{
    extract($data);
    $userName = $_SESSION['user_name'] ?? 'Guest';
    $userRole = $_SESSION['user_role'] ?? 'user';

    $managementItems = [
        'users'     => ['label' => 'Users',     'icon' => 'users',   'href' => BASE_URL . '/admin/users'],
        'billing'   => ['label' => 'Billing',   'icon' => 'receipt', 'href' => BASE_URL . '/admin/billing'],
        'inventory' => ['label' => 'Inventory', 'icon' => 'package', 'href' => BASE_URL . '/admin/inventory'],
    ];
    $managementActive = array_key_exists($activeNav, $managementItems);
?>
<!DOCTYPE html>
<html lang="en" x-data="{ sidebarExpanded: true, managementOpen: <?= $managementActive ? 'true' : 'false' ?>, profileOpen: false, modal: '', drawer: '' }" @keydown.escape.window="profileOpen = false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/giftvibe-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: <?= adminTailwindColorsJs() ?> } } };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white text-dark antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- ===== SIDEBAR ===== -->
    <aside class="hidden lg:flex lg:flex-col bg-white border-r border-primary-100 overflow-y-auto transition-all duration-300"
           :class="sidebarExpanded ? 'w-64' : 'w-20'">
        <!-- Brand -->
        <div class="flex items-center gap-3 px-4 py-4 border-b border-primary-100 overflow-hidden">
            <img src="<?= BASE_URL ?>/assets/img/giftvibe-logo.png" alt="<?= htmlspecialchars(APP_NAME) ?>" class="w-10 h-10 rounded-xl object-cover shrink-0">
            <span x-show="sidebarExpanded" x-transition class="min-w-0 flex-1 text-lg font-bold text-primary-700 whitespace-nowrap"><?= APP_NAME ?></span>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <button @click="sidebarExpanded = !sidebarExpanded" class="mb-3 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-primary-700 hover:bg-primary-50" :aria-label="sidebarExpanded ? 'Collapse sidebar' : 'Expand sidebar'" :title="sidebarExpanded ? 'Collapse sidebar' : 'Expand sidebar'">
                <i data-lucide="panel-left-close" class="w-5 h-5 shrink-0 transition-transform" :class="!sidebarExpanded && 'rotate-180'"></i>
                <span x-show="sidebarExpanded" class="text-sm font-semibold">Collapse sidebar</span>
            </button>
            <p x-show="sidebarExpanded" class="px-3 text-xs font-semibold text-primary-400 uppercase tracking-wider mb-2">Main Menu</p>
            <a href="<?= BASE_URL ?>/admin" title="Dashboard" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition <?= $activeNav === 'dashboard' ? 'bg-primary-50 text-primary-700' : 'text-secondary hover:bg-secondary-100' ?>">
                <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                <span x-show="sidebarExpanded" x-transition>Dashboard</span>
            </a>

            <button @click="if (!sidebarExpanded) sidebarExpanded = true; managementOpen = !managementOpen" title="Management" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition <?= $managementActive ? 'text-primary-700' : 'text-secondary hover:bg-secondary-100' ?>">
                <i data-lucide="settings-2" class="w-5 h-5 shrink-0"></i>
                <span x-show="sidebarExpanded" class="flex-1 text-left">Management</span>
                <i x-show="sidebarExpanded" data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="managementOpen && 'rotate-180'"></i>
            </button>

            <div x-cloak x-show="sidebarExpanded && managementOpen" x-transition class="ml-4 space-y-1 border-l border-primary-100 pl-3">
                <?php foreach ($managementItems as $key => $item): ?>
                    <a href="<?= $item['href'] ?>" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition <?= $activeNav === $key ? 'bg-primary-50 text-primary-700' : 'text-secondary hover:bg-secondary-100' ?>">
                        <i data-lucide="<?= $item['icon'] ?>" class="w-4 h-4 shrink-0"></i>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- User footer -->
        <div class="border-t border-primary-100 p-4" x-show="sidebarExpanded">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                    <?= strtoupper(substr($userName, 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($userName) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($userRole) ?></p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Nav Bar -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($title) ?></h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500"><?= date('M d, Y') ?></span>
                <div class="relative" @click.outside="profileOpen = false">
                    <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 rounded-xl border border-primary-100 px-2.5 py-1.5 hover:bg-primary-50" aria-label="Open profile menu">
                        <span class="flex w-8 h-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white"><?= strtoupper(substr($userName, 0, 1)) ?></span>
                        <span class="hidden sm:block text-sm font-semibold text-gray-700"><?= htmlspecialchars($userName) ?></span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 z-50 mt-2 w-48 rounded-xl border border-gray-100 bg-white p-2 shadow-xl">
                        <div class="px-3 py-2 border-b border-gray-100 mb-1"><p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($userName) ?></p><p class="text-xs text-gray-400"><?= htmlspecialchars($userRole) ?></p></div>
                        <a href="<?= BASE_URL ?>/logout" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"><i data-lucide="log-out" class="w-4 h-4"></i>Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($success_message)): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php require_once $contentView; ?>
        </main>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
<?php
}
