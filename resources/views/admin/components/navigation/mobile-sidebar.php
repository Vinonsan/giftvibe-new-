<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<div id="mobile-sidebar-overlay" class="pointer-events-none fixed inset-0 z-40 bg-slate-950/0 opacity-0 transition-opacity duration-200 ease-out lg:hidden" data-sidebar-overlay></div>

<aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 flex h-screen w-[min(18rem,88vw)] -translate-x-full flex-col border-r border-primary-900/10 bg-white text-primary shadow-xl transition-transform duration-200 ease-out will-change-transform lg:hidden" aria-label="Mobile admin navigation" aria-hidden="true">
    <!-- Brand Logo / Close -->
    <div class="flex h-16 min-h-16 items-center justify-between border-b border-primary-900/10 bg-white px-4">
        <a href="<?= e(url('/admin/dashboard')) ?>" class="flex min-w-0 items-center gap-3 rounded-button font-bold text-primary focus:outline-none focus:ring-2 focus:ring-primary-500" data-sidebar-link>
            <img src="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>" alt="Gift Vibe LK logo" class="h-10 w-10 shrink-0 rounded-button object-contain">
            <span class="truncate text-sm font-extrabold tracking-tight text-primary-900">GiftVibeLK</span>
        </a>
        <button type="button" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-button text-primary transition hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500" data-sidebar-close aria-label="Close navigation">
            <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'md']); ?>
        </button>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3" aria-label="Mobile Sidebar Navigation">
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
