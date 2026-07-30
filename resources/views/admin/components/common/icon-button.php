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
    'primary' => 'bg-primary text-white hover:bg-secondary focus:ring-primary-500',
    'outline' => 'border border-primary-900/15 text-primary bg-white hover:bg-primary-50 focus:ring-primary-500',
    'ghost' => 'text-primary/70 hover:bg-primary-50 hover:text-primary focus:ring-primary-500',
];

$sizes = [
    'xs' => 'min-h-9 min-w-9 p-1.5',
    'sm' => 'min-h-10 min-w-10 p-2',
    'md' => 'min-h-11 min-w-11 p-2.5',
    'lg' => 'min-h-12 min-w-12 p-3',
];

$classes = implode(' ', [
    'inline-flex items-center justify-center rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
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
