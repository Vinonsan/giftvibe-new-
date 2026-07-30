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
$groupClass = $hasActiveChild
    ? 'bg-primary-900 text-white font-extrabold shadow-card'
    : 'text-primary-900 hover:bg-primary-50 hover:text-primary-900';
$submenuId = 'sidebar-submenu-' . uniqid('', false);
?>
<div class="space-y-1" data-sidebar-group>
    <button type="button" class="flex min-h-12 w-full items-center justify-between rounded-button px-4 py-2 text-base transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 <?= $groupClass ?>" data-sidebar-group-trigger aria-expanded="<?= $isExpanded ? 'true' : 'false' ?>" aria-controls="<?= e($submenuId) ?>">
        <div class="flex min-w-0 items-center gap-3">
            <?php if (!empty($item['icon'])): ?>
                <?php component('admin/components/common/icon', ['name' => $item['icon'], 'size' => 'md', 'class' => 'shrink-0']); ?>
            <?php endif; ?>
            <span class="truncate"><?= e($item['label']) ?></span>
        </div>
        <span class="shrink-0 transition-transform duration-200 <?= $isExpanded ? 'rotate-90' : '' ?>" data-sidebar-group-icon>
            <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'xs']); ?>
        </span>
    </button>
    <div id="<?= e($submenuId) ?>" class="grid transition-[grid-template-rows] duration-200 ease-out <?= $isExpanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]' ?>" data-sidebar-submenu>
        <div class="min-h-0 overflow-hidden">
    <div class="ml-7 space-y-2 border-l border-blue-100 py-3 pl-4">
        <?php foreach ($item['children'] as $child): 
            $childActive = $child['active'] ?? false;
            $childClass = $childActive ? 'bg-blue-50 text-primary-900 font-extrabold' : 'text-primary-900 hover:bg-primary-50 hover:text-primary-900';
        ?>
            <a href="<?= e($child['url']) ?>" class="block min-h-10 rounded-button px-4 py-2 text-base transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 <?= $childClass ?>" data-sidebar-link>
                <?= e($child['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>
        </div>
    </div>
</div>
