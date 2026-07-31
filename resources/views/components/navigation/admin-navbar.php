<?php

declare(strict_types=1);

/**
 * Global admin top navbar — hamburger toggle, page title, notification bell, profile dropdown.
 *
 * Expected variables (set in the layout before requiring this file):
 *   $adminUser     array   ['name' => string, 'role' => string, 'email' => string, 'avatar' => string|null]
 *   $pageTitle     string  Optional — overrides the default "Dashboard" heading.
 */

$adminUser = $adminUser ?? [
    'name'   => 'Admin User',
    'role'   => 'Administrator',
    'email'  => 'admin@giftvibe.lk',
    'avatar' => null,
];
$pageTitle = $pageTitle ?? 'Dashboard';

$initials = '';
$nameParts = explode(' ', trim($adminUser['name']));
foreach ($nameParts as $part) {
    if ($part !== '') $initials .= strtoupper($part[0]);
}
$initials = substr($initials, 0, 2);
?>

<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 backdrop-blur-md px-5">

    <!-- Left: Hamburger + Page title -->
    <div class="flex items-center gap-3">
        <!-- Mobile hamburger -->
        <button type="button"
                class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-secondary lg:hidden"
                data-sidebar-open
                aria-label="Open sidebar">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
        <h1 class="text-base font-semibold text-secondary"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <!-- Right: Actions + Profile -->
    <div class="flex items-center gap-2">

        <!-- Notification Bell -->
        <button type="button"
                class="relative rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-secondary"
                aria-label="Notifications">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            <!-- Notification dot -->
            <span class="absolute right-1.5 top-1.5 flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
            </span>
        </button>

        <!-- Separator -->
        <div class="mx-1 h-8 w-px bg-slate-200"></div>

        <!-- Profile Dropdown -->
        <div class="relative" data-profile-dropdown>
            <button type="button"
                    class="flex items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-slate-50 cursor-pointer"
                    data-profile-trigger
                    aria-expanded="false"
                    aria-haspopup="true">
                <!-- Avatar -->
                <?php if (!empty($adminUser['avatar'])): ?>
                    <img src="<?= htmlspecialchars($adminUser['avatar']) ?>"
                         alt="<?= htmlspecialchars($adminUser['name']) ?>"
                         class="h-9 w-9 rounded-full object-cover ring-2 ring-primary/20">
                <?php else: ?>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-xs font-bold text-white ring-2 ring-primary/20">
                        <?= htmlspecialchars($initials) ?>
                    </span>
                <?php endif; ?>
                <!-- Name & Role -->
                <div class="hidden text-left sm:block">
                    <p class="text-sm font-semibold text-secondary leading-tight"><?= htmlspecialchars($adminUser['name']) ?></p>
                    <p class="text-xs text-slate-400 leading-tight"><?= htmlspecialchars($adminUser['role']) ?></p>
                </div>
                <!-- Chevron -->
                <svg class="hidden h-4 w-4 text-slate-400 transition-transform duration-200 sm:block" data-profile-chevron viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- Dropdown Panel -->
            <div class="absolute right-0 top-full mt-2 w-60 origin-top-right scale-95 opacity-0 pointer-events-none
                        rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50
                        transition-all duration-200 ease-out"
                 data-profile-panel>

                <!-- User Info Header -->
                <div class="border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-semibold text-secondary"><?= htmlspecialchars($adminUser['name']) ?></p>
                    <p class="text-xs text-slate-400"><?= htmlspecialchars($adminUser['email']) ?></p>
                </div>

                <!-- Menu Items -->
                <div class="p-1.5">
                    <a href="/admin/profile"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-secondary">
                        <svg class="h-4.5 w-4.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        View Profile
                    </a>
                    <a href="/admin/settings"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-secondary">
                        <svg class="h-4.5 w-4.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </div>

                <!-- Divider + Logout -->
                <div class="border-t border-slate-100 p-1.5">
                    <a href="/logout"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.profileDropdown) return;
    window.GiftVibeUI = window.GiftVibeUI || {};

    document.querySelectorAll('[data-profile-dropdown]').forEach(function (wrapper) {
        var trigger = wrapper.querySelector('[data-profile-trigger]');
        var panel   = wrapper.querySelector('[data-profile-panel]');
        var chevron = wrapper.querySelector('[data-profile-chevron]');
        if (!trigger || !panel) return;

        function open() {
            panel.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
            panel.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            if (chevron) chevron.classList.add('rotate-180');
            trigger.setAttribute('aria-expanded', 'true');
        }
        function close() {
            panel.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
            panel.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            if (chevron) chevron.classList.remove('rotate-180');
            trigger.setAttribute('aria-expanded', 'false');
        }
        function isOpen() {
            return trigger.getAttribute('aria-expanded') === 'true';
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            isOpen() ? close() : open();
        });

        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    });

    window.GiftVibeUI.profileDropdown = true;
})();
</script>
