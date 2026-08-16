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
$showPageTitle = $showPageTitle ?? true;

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
        <?php if ($showPageTitle): ?>
            <h1 class="text-base font-semibold text-secondary"><?= htmlspecialchars($pageTitle) ?></h1>
        <?php endif; ?>
    </div>

    <!-- Right: Actions + Profile -->
    <div class="flex items-center gap-2">

        <!-- Notification drawer: includes current and older orders. -->
        <?php
        $notificationRows = $adminNotifications ?? [];
        ob_start();
        ?>
        <div class="space-y-3">
            <?php if (!$notificationRows): ?>
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-400">No orders yet.</div>
            <?php else: ?>
                <?php foreach ($notificationRows as $notification): ?>
                    <?php $isUnread = ($notification['notification_status'] ?? 'unread') === 'unread'; ?>
                    <a href="<?= htmlspecialchars(app_url('/admin/orders?view=' . (int) $notification['entity_id'])) ?>"
                       class="group flex items-start gap-3 rounded-2xl border <?= $isUnread ? 'border-primary/20 bg-primary/5' : 'border-slate-200 bg-white' ?> p-4 transition hover:border-primary/30 hover:bg-primary/5">
                        <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?= $isUnread ? 'bg-primary text-white' : 'bg-slate-100 text-slate-500' ?>">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m4.5 3.75a3.75 3.75 0 107.5 0M3.375 7.5h17.25"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-start justify-between gap-3">
                                <strong class="text-sm text-secondary"><?= htmlspecialchars((string) $notification['order_number']) ?></strong>
                                <span class="shrink-0 text-xs font-bold text-primary">LKR <?= number_format((float) $notification['grand_total'], 2) ?></span>
                            </span>
                            <span class="mt-1 block truncate text-xs text-slate-500"><?= htmlspecialchars((string) $notification['customer_name']) ?> · <?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $notification['order_status']))) ?></span>
                            <span class="mt-2 block text-[11px] text-slate-400"><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $notification['created_at']))) ?></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php
        $drawerBody = ob_get_clean();
        $drawerId = 'admin-notifications';
        $drawerSide = 'right';
        $drawerSize = 'lg';
        $drawerTitle = 'Order notifications';
        $drawerDescription = 'Select any new or previous order to view its details.';
        $drawerFooter = '<a href="' . htmlspecialchars(app_url('/admin/orders'), ENT_QUOTES) . '" class="inline-flex items-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-secondary">View all orders</a>';
        $drawerTrigger = '<button type="button" data-notification-trigger aria-expanded="false" class="relative rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-secondary" aria-label="Order notifications">'
            . '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>'
            . (($adminUnreadNotificationCount ?? 0) > 0 ? '<span class="absolute -right-1 -top-1 min-w-5 rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-black text-white">' . min(99, (int) $adminUnreadNotificationCount) . '</span>' : '')
            . '</button>';
        $drawerStatic = false;
        $drawerCloseOnEsc = true;
        $drawerShowCloseButton = true;
        $drawerOverlay = true;
        echo $drawerTrigger;
        // The notification list is rendered below as a compact navbar dropdown.
        ?>

        <div data-notification-dropdown class="relative">
            <div data-notification-panel class="pointer-events-none absolute right-0 top-full z-50 mt-2 w-[22rem] origin-top-right scale-95 opacity-0 transition-all duration-200 sm:w-[26rem]">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/40">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div><p class="text-sm font-bold text-secondary">New orders</p><p class="text-xs text-slate-400"><?= (int)($adminUnreadNotificationCount ?? 0) ?> unread</p></div>
                        <a href="<?= htmlspecialchars(app_url('/admin/orders')) ?>" class="text-xs font-bold text-primary hover:text-secondary">View all</a>
                    </div>
                    <div class="max-h-[28rem] overflow-y-auto p-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        <?php if (!$notificationRows): ?>
                            <div class="px-5 py-10 text-center"><span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">✓</span><p class="mt-3 text-sm font-bold text-secondary">You're all caught up</p><p class="mt-1 text-xs text-slate-400">New orders will appear here.</p></div>
                        <?php else: foreach ($notificationRows as $notification): $isUnread = ($notification['notification_status'] ?? 'unread') === 'unread'; ?>
                            <a href="<?= htmlspecialchars(app_url('/admin/orders?view=' . (int) $notification['entity_id'])) ?>" data-order-notification data-order-id="<?= (int)$notification['entity_id'] ?>" class="group flex items-start gap-3.5 rounded-xl p-3 transition-colors <?= $isUnread ? 'bg-primary/5 hover:bg-primary/10' : 'hover:bg-slate-50' ?>">
                                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full <?= $isUnread ? 'bg-primary text-white shadow-sm shadow-primary/30' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200' ?>">
                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <strong class="truncate text-sm text-secondary font-bold group-hover:text-primary transition-colors"><?= htmlspecialchars((string) $notification['order_number']) ?></strong>
                                        <span class="shrink-0 text-xs font-extrabold text-primary">LKR <?= number_format((float) $notification['grand_total'], 2) ?></span>
                                    </span>
                                    <span class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                        <span class="truncate"><?= htmlspecialchars((string) $notification['customer_name']) ?></span>
                                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                        <span class="shrink-0 font-medium text-slate-600"><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string) $notification['order_status']))) ?></span>
                                    </span>
                                    <span class="mt-2.5 flex items-center justify-between text-[11px] font-medium text-slate-400">
                                        <span><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $notification['created_at']))) ?></span>
                                        <span class="font-bold text-primary opacity-0 transition transform translate-x-[-4px] group-hover:opacity-100 group-hover:translate-x-0">View details &rarr;</span>
                                    </span>
                                </span>
                            </a>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>

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
                    <a href="/admin/logout"
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
    var notificationCsrf = <?= json_encode((string)($notificationCsrfToken ?? '')) ?>;
    var notificationTrigger = document.querySelector('[data-notification-trigger]');
    var notificationPanel = document.querySelector('[data-notification-panel]');
    if (notificationTrigger && notificationPanel) {
        function closeNotifications() {
            notificationPanel.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
            notificationPanel.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            notificationTrigger.setAttribute('aria-expanded', 'false');
        }
        notificationTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            var open = notificationTrigger.getAttribute('aria-expanded') === 'true';
            if (open) {
                closeNotifications();
            } else {
                notificationPanel.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
                notificationPanel.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
                notificationTrigger.setAttribute('aria-expanded', 'true');
            }
        });
        notificationPanel.addEventListener('click', function (event) {
            var link = event.target.closest('[data-order-notification]');
            if (!link) { event.stopPropagation(); return; }
            event.preventDefault();
            var destination = link.href;
            link.classList.add('opacity-50', 'pointer-events-none');
            var body = new FormData();
            body.append('csrf_token', notificationCsrf);
            body.append('order_id', link.getAttribute('data-order-id') || '0');
            fetch(<?= json_encode(app_url('/admin/notifications/read')) ?>, { method: 'POST', body: body, credentials: 'same-origin' })
                .catch(function () {})
                .finally(function () { window.location.href = destination; });
        });
        document.addEventListener('click', closeNotifications);
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeNotifications(); });
    }

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
