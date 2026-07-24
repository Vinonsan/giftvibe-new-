<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<div id="mobile-sidebar" class="-translate-x-full fixed inset-y-0 left-0 w-64 bg-slate-900 text-slate-300 border-r border-slate-800 flex-shrink-0 min-h-screen transition-transform duration-300 md:hidden z-50">
    <!-- Brand Logo / Close -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950">
        <a href="/admin" class="flex items-center gap-2 font-bold text-white">
            <svg class="h-6 w-auto text-primary" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" rx="20" fill="currentColor"/>
                <path d="M50 20L75 45H25L50 20Z" fill="white"/>
            </svg>
            <span class="text-sm font-extrabold text-white">Gift Vibe</span>
        </a>
        <button type="button" class="text-slate-400 hover:text-white" data-sidebar-toggle>
            <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'md']); ?>
        </button>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
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
</div>