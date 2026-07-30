<?php
use App\Core\Auth;
use Services\AdminNotificationService;

/**
 * Variables:
 * @var string $title
 * @var string|null $subtitle
 */
$adminUser = Auth::user() ?? [];
$unreadNotifications = AdminNotificationService::unreadCount();
?>
<header class="sticky top-0 z-30 flex h-24 min-h-24 shrink-0 items-center justify-between gap-3 border-b border-primary-900/10 bg-white px-3 sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
        <!-- Mobile sidebar open -->
        <button type="button" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-button text-primary transition hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 lg:hidden" data-sidebar-open aria-expanded="false" aria-controls="mobile-sidebar" aria-label="Open navigation">
            <?php component('admin/components/common/icon', ['name' => 'menu', 'size' => 'md']); ?>
        </button>

        <div class="min-w-0">
            <h2 class="truncate text-2xl font-extrabold leading-7 text-primary-900"><?= e($title) ?></h2>
            <p class="hidden truncate text-base font-semibold leading-5 text-primary-900 md:block"><?= e($subtitle ?? 'Gift operations') ?></p>
        </div>
    </div>

    <div class="flex min-w-0 flex-1 items-center justify-end gap-1.5 sm:gap-2">
        <!-- Date/Time Display -->
        <div class="hidden items-center gap-2 rounded-button border border-slate-200 bg-white px-4 py-2 shadow-sm xl:flex">
            <?php component('admin/components/common/icon', ['name' => 'calendar', 'size' => 'sm', 'class' => 'text-slate-400']); ?>
            <span id="header-datetime" class="text-sm font-semibold text-slate-700"><?= e(date('l, M d, Y')) ?></span>
        </div>

        <a href="<?= e(url('/admin/notifications')) ?>" class="relative inline-flex min-h-10 min-w-10 items-center justify-center rounded-button text-primary transition-colors hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2" aria-label="View Notifications">
            <?php component('admin/components/common/icon', ['name' => 'bell', 'size' => 'md']); ?>
            <?php if ($unreadNotifications > 0): ?>
                <span class="absolute right-1 top-1 min-w-5 rounded-full border-2 border-white bg-primary px-1 text-center text-[10px] font-extrabold leading-4 text-white"><?= e((string) min($unreadNotifications, 99)) ?></span>
            <?php endif; ?>
        </a>

        <!-- User Profile Dropdown -->
        <?php component('admin/components/navigation/user-menu', [
            'name' => $adminUser['name'] ?? 'Administrator',
            'email' => $adminUser['phone'] ?? ($adminUser['email'] ?? 'admin@giftvibe.lk')
        ]); ?>
    </div>
</header>
