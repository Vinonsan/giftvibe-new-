<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<aside id="admin-sidebar" class="relative sticky top-0 hidden h-screen w-80 shrink-0 flex-col border-r border-primary-900/15 bg-white text-primary shadow-[1px_0_0_rgba(12,43,78,0.04)] lg:flex">
    <!-- Floating sidebar toggle button (on the right edge) -->
    <button
        type="button"
        class="absolute -right-3 top-1/2 z-50 flex min-h-8 min-w-8 -translate-y-1/2 items-center justify-center rounded-full border border-primary-900/15 bg-white text-primary-900 shadow-md transition-all duration-200 hover:bg-primary-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        data-sidebar-toggle
        data-sidebar-floating-toggle
        aria-label="Collapse sidebar"
        title="Toggle sidebar"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 transition-transform duration-200" data-sidebar-toggle-icon>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
    </button>

    <!-- Brand Logo -->
    <div class="flex h-24 min-h-24 items-center border-b border-primary-900/10 bg-white px-6">
        <a href="<?= e(url('/admin/dashboard')) ?>" class="flex min-w-0 items-center gap-3 rounded-button font-bold text-primary focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            <img src="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>" alt="Gift Vibe LK logo" class="h-11 w-11 shrink-0 rounded-button object-contain">
            <span class="truncate text-xl font-extrabold tracking-tight text-primary-900" data-sidebar-brand-text>GiftVibeLK</span>
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 space-y-2 overflow-y-auto px-4 py-6" aria-label="Sidebar Navigation">
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

</aside>
