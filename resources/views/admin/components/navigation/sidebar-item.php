<?php
/**
 * Variables:
 * @var array $item
 */
$isActive = $item['active'] ?? false;
$activeClass = $isActive
    ? 'bg-primary-900 text-white font-extrabold shadow-card'
    : 'text-primary-900 hover:bg-primary-50 hover:text-primary-900';
?>
<a href="<?= e($item['url']) ?>" class="flex min-h-12 items-center justify-between rounded-button px-4 py-2 text-base transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 <?= $activeClass ?>" data-sidebar-link>
    <div class="flex min-w-0 items-center gap-3">
        <?php if (!empty($item['icon'])): ?>
            <?php component('admin/components/common/icon', ['name' => $item['icon'], 'size' => 'md', 'class' => 'shrink-0']); ?>
        <?php endif; ?>
        <span class="truncate"><?= e($item['label']) ?></span>
    </div>
    <?php if (isset($item['badge'])): ?>
        <span class="rounded-full border border-primary-100 bg-white/90 px-2 py-0.5 text-xs font-bold text-primary"><?= e($item['badge']) ?></span>
    <?php endif; ?>
</a>
