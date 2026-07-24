<?php
/**
 * Variables:
 * @var string $title
 */
?>
<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shadow-xs flex-shrink-0">
    <div class="flex items-center gap-4">
        <!-- Sidebar Toggle for Mobile -->
        <button type="button" class="md:hidden p-2 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500" data-sidebar-toggle aria-expanded="false" aria-label="Toggle Navigation Sidebar">
            <?php component('admin/components/common/icon', ['name' => 'menu', 'size' => 'md']); ?>
        </button>
        <h2 class="text-lg font-bold text-slate-800"><?= e($title) ?></h2>
    </div>

    <!-- Actions Area -->
    <div class="flex items-center gap-4">
        <!-- View Website Link -->
        <a href="<?= BASE_URL ?>" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 py-1.5 px-3 border border-slate-200 rounded hover:bg-slate-50 transition-colors">
            <?php component('admin/components/common/icon', ['name' => 'globe', 'size' => 'xs']); ?>
            <span>View Site</span>
        </a>

        <!-- Notifications Placeholder -->
        <?php component('admin/components/common/icon-button', [
            'icon' => 'bell',
            'label' => 'View Notifications',
            'variant' => 'ghost',
            'size' => 'sm'
        ]); ?>

        <!-- User Profile Dropdown -->
        <?php component('admin/components/navigation/user-menu', [
            'name' => 'Administrator',
            'email' => 'admin@giftvibelk.com'
        ]); ?>
    </div>
</header>