<?php
/**
 * Variables:
 * @var string $name
 * @var string $email
 */
?>
<div class="relative" data-dropdown>
    <button type="button" class="flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-full" data-user-menu-trigger aria-expanded="false">
        <?php component('admin/components/common/avatar', ['name' => $name, 'size' => 'sm']); ?>
    </button>
    <!-- Dropdown Menu -->
    <div class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-card shadow-lg py-2 hidden z-30" data-user-menu>
        <div class="px-4 py-2 border-b border-slate-100">
            <p class="text-sm font-bold text-slate-800"><?= e($name) ?></p>
            <p class="text-xs text-slate-400 truncate"><?= e($email) ?></p>
        </div>
        <a href="/admin/profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors focus:bg-slate-50 focus:outline-none">
            Your Profile
        </a>
        <a href="/admin/settings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors focus:bg-slate-50 focus:outline-none">
            Settings
        </a>
        <div class="border-t border-slate-100 mt-2 pt-2">
            <a href="/logout" class="block px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors focus:bg-red-50 focus:outline-none">
                Log Out
            </a>
        </div>
    </div>
</div>