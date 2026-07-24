<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<aside id="admin-sidebar" class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 border-r border-slate-800 flex-shrink-0 min-h-screen transition-transform duration-300">
    <!-- Brand Logo -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950">
        <a href="/admin" class="flex items-center gap-2 font-bold text-white focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">
            <svg class="h-6 w-auto text-primary" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" rx="20" fill="currentColor"/>
                <path d="M50 20L75 45H25L50 20Z" fill="white"/>
                <rect x="35" y="45" width="30" height="35" fill="white"/>
            </svg>
            <span class="text-sm tracking-tight font-extrabold text-white">Gift Vibe <span class="text-primary-400">Admin</span></span>
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1" aria-label="Sidebar Navigation">
        <?php foreach ($navigation as $item): 
            $hasChildren = !empty($item['children']);
        ?>
            <?php if ($hasChildren): ?>
                <?php component('admin/components/navigation/sidebar-group', ['item' => $item]); ?>
            <?php else: ?>
                <?php component('admin/components/navigation/sidebar-item', ['item' => $item]); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Logout Area -->
    <div class="p-4 border-t border-slate-850 bg-slate-950">
        <a href="/logout" class="flex items-center gap-3 px-3 py-2 text-sm font-semibold rounded-button hover:bg-slate-800 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500">
            <?php component('admin/components/common/icon', ['name' => 'logout', 'size' => 'sm']); ?>
            <span>Log Out</span>
        </a>
    </div>
</aside>