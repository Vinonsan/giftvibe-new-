<?php
/**
 * Variables:
 * @var string $icon
 * @var string $label
 * @var string|null $href
 * @var string|null $variant
 * @var string|null $size
 * @var bool|null $disabled
 */
$variant = $variant ?? 'outline';
$size = $size ?? 'md';
$disabled = $disabled ?? false;

$variants = [
    'primary' => 'bg-primary text-white hover:bg-primary-600 focus:ring-primary-500',
    'outline' => 'border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 focus:ring-primary-500',
    'ghost' => 'text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus:ring-slate-500',
];

$sizes = [
    'xs' => 'p-1',
    'sm' => 'p-1.5',
    'md' => 'p-2',
    'lg' => 'p-2.5',
];

$classes = implode(' ', [
    'inline-flex items-center justify-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
    $variants[$variant] ?? $variants['outline'],
    $sizes[$size] ?? $sizes['md'],
    $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
]);

if (!empty($href)): ?>
    <a href="<?= e($href) ?>" class="<?= $classes ?>" aria-label="<?= e($label) ?>">
        <?php component('admin/components/common/icon', ['name' => $icon, 'size' => $size]); ?>
    </a>
<?php else: ?>
    <button type="button" class="<?= $classes ?>" aria-label="<?= e($label) ?>" <?= $disabled ? 'disabled' : '' ?>>
        <?php component('admin/components/common/icon', ['name' => $icon, 'size' => $size]); ?>
    </button>
<?php endif; ?>