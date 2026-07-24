<?php
/**
 * Variables:
 * @var array $item Group item with children
 */
$hasActiveChild = false;
foreach ($item['children'] as $child) {
    if ($child['active'] ?? false) {
        $hasActiveChild = true;
        break;
    }
}
$isExpanded = $hasActiveChild;
?>
<div class="space-y-1" data-sidebar-group>
    <button type="button" class="w-full flex items-center justify-between px-3 py-2 text-sm text-slate-400 hover:bg-slate-800 hover:text-white rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500" data-sidebar-group-trigger aria-expanded="<?= $isExpanded ? 'true' : 'false' ?>">
        <div class="flex items-center gap-3">
            <?php if (!empty($item['icon'])): ?>
                <?php component('admin/components/common/icon', ['name' => $item['icon'], 'size' => 'sm']); ?>
            <?php endif; ?>
            <span><?= e($item['label']) ?></span>
        </div>
        <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'xs', 'class' => 'transform transition-transform ' . ($isExpanded ? 'rotate-90' : ''), 'data-sidebar-group-icon' => true]); ?>
    </button>
    <div class="<?= $isExpanded ? '' : 'hidden' ?> pl-9 space-y-1">
        <?php foreach ($item['children'] as $child): 
            $childActive = $child['active'] ?? false;
            $childClass = $childActive ? 'text-white font-bold' : 'text-slate-500 hover:text-white';
        ?>
            <a href="<?= e($child['url']) ?>" class="block py-2 text-xs transition-colors focus:outline-none <?= $childClass ?>">
                &bull; <?= e($child['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>