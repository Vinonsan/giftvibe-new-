<?php
/**
 * Variables:
 * @var string $label
 * @var string $content
 * @var string|null $align (right, left)
 */
$align = $align ?? 'left';
$alignClass = $align === 'right' ? 'right-0' : 'left-0';
?>
<div class="relative inline-block text-left" data-dropdown>
    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 bg-white text-xs font-semibold text-slate-700 rounded-button hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-500" data-dropdown-trigger aria-expanded="false">
        <span><?= e($label) ?></span>
        <?php component('admin/components/common/icon', ['name' => 'chevron-down', 'size' => 'xs']); ?>
    </button>
    <div class="absolute mt-2 w-48 bg-white border border-slate-200 rounded-card shadow-lg py-1 hidden z-30 <?= $alignClass ?>" data-dropdown-menu>
        <?= $content ?>
    </div>
</div>