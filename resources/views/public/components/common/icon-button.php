<?php
/**
 * Variables:
 * @var string $icon
 * @var string $label
 * @var string|null $href
 * @var string|null $variant
 * @var string|null $size
 * @var bool|null $disabled
 * @var string|null $type
 */
$variant = $variant ?? 'outline';
$size = $size ?? 'md';
$type = $type ?? 'button';
$disabled = $disabled ?? false;

$variantClasses = [
    'primary' => 'bg-primary text-white hover:bg-primary-600 focus:ring-primary-500',
    'secondary' => 'bg-secondary text-white hover:bg-secondary-600 focus:ring-secondary-500',
    'outline' => 'border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-primary-500',
    'ghost' => 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-500',
];

$sizeClasses = [
    'xs' => 'p-1',
    'sm' => 'p-1.5',
    'md' => 'p-2',
    'lg' => 'p-2.5',
    'xl' => 'p-3',
];

$classes = implode(' ', [
    'inline-flex items-center justify-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
    $variantClasses[$variant] ?? $variantClasses['outline'],
    $sizeClasses[$size] ?? $sizeClasses['md'],
    $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
]);

if (!empty($href)): ?>
    <a href="<?= e($href) ?>" class="<?= $classes ?>" aria-label="<?= e($label) ?>">
        <?php component('public/components/common/icon', ['name' => $icon, 'size' => $size]); ?>
    </a>
<?php else: ?>
    <button type="<?= e($type) ?>" class="<?= $classes ?>" aria-label="<?= e($label) ?>" <?= $disabled ? 'disabled' : '' ?>>
        <?php component('public/components/common/icon', ['name' => $icon, 'size' => $size]); ?>
    </button>
<?php endif; ?>