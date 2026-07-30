<?php
/**
 * Variables:
 * @var string $name
 * @var string $email
 */
?>
<div class="relative" data-dropdown>
    <button type="button" class="flex min-h-10 items-center gap-2 rounded-button px-1.5 transition hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:px-2" data-user-menu-trigger aria-expanded="false" aria-haspopup="true">
        <?php component('admin/components/common/avatar', ['name' => $name, 'size' => 'sm']); ?>
        <span class="hidden max-w-32 truncate text-left text-sm font-semibold text-primary xl:block"><?= e($name) ?></span>
        <?php component('admin/components/common/icon', ['name' => 'chevron-down', 'size' => 'xs', 'class' => 'hidden text-primary/60 sm:block']); ?>
    </button>
    <!-- Dropdown Menu -->
    <div class="absolute right-0 z-40 mt-2 hidden w-[min(14rem,calc(100vw-2rem))] rounded-card border border-slate-200 bg-white py-2 shadow-lg" data-user-menu>
        <div class="px-4 py-2 border-b border-slate-100">
            <p class="text-sm font-bold text-slate-800"><?= e($name) ?></p>
            <p class="text-xs text-slate-400 truncate"><?= e($email) ?></p>
        </div>
        <a href="<?= e(url('/admin/settings')) ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors focus:bg-slate-50 focus:outline-none">
            Settings
        </a>
        <div class="border-t border-slate-100 mt-2 pt-2">
            <form method="POST" action="<?= e(url('/admin/logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-danger hover:bg-red-50 transition-colors focus:bg-red-50 focus:outline-none">Log Out</button>
            </form>
        </div>
    </div>
</div>
