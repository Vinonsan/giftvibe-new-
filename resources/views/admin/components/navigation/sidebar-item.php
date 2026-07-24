<?php
/**
 * Variables:
 * @var array $item
 */
$isActive = $item['active'] ?? false;
$activeClass = $isActive ? 'bg-primary text-white font-bold' : 'hover:bg-slate-800 hover:text-white text-slate-400';
?>
<a href="<?= e($item['url']) ?>" class="flex items-center justify-between px-3 py-2 text-sm rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 <?= $activeClass ?>">
    <div class="flex items-center gap-3">
        <?php if (!empty($item['icon'])): ?>
            <?php component('admin/components/common/icon', ['name' => $item['icon'], 'size' => 'sm']); ?>
        <?php endif; ?>
        <span><?= e($item['label']) ?></span>
    </div>
    <?php if (isset($item['badge'])): ?>
        <span class="px-2 py-0.5 rounded-full text-2xs bg-slate-800 text-slate-300 font-bold border border-slate-700"><?= e($item['badge']) ?></span>
    <?php endif; ?>
</a>