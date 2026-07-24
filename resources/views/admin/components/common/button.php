<?php
/**
 * Variables:
 * @var string $label
 * @var string|null $href
 * @var string|null $type
 * @var string|null $variant (primary, secondary, outline, ghost, danger, success)
 * @var string|null $size (xs, sm, md, lg)
 * @var bool|null $disabled
 * @var bool|null $loading
 * @var string|null $leftIcon
 * @var string|null $rightIcon
 * @var bool|null $fullWidth
 */
$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$type = $type ?? 'button';
$disabled = $disabled ?? false;
$loading = $loading ?? false;
$fullWidth = $fullWidth ?? false;

$variants = [
    'primary' => 'bg-primary text-white hover:bg-primary-600 focus:ring-primary-500',
    'secondary' => 'bg-slate-800 text-white hover:bg-slate-700 focus:ring-slate-500',
    'outline' => 'border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-primary-500',
    'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-500',
    'danger' => 'bg-danger text-white hover:bg-danger-600 focus:ring-danger-500',
    'success' => 'bg-success text-white hover:bg-success-600 focus:ring-success-500',
];

$sizes = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-2.5 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
];

$classes = implode(' ', [
    'inline-flex items-center justify-center font-semibold rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2',
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['md'],
    $fullWidth ? 'w-full' : '',
    ($disabled || $loading) ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''
]);
?>
<?php if (!empty($href)): ?>
    <a href="<?= e($href) ?>" class="<?= $classes ?>">
        <?php if ($loading): ?>
            <?php component('admin/components/common/spinner', ['size' => 'sm', 'class' => 'mr-2']); ?>
        <?php elseif (!empty($leftIcon)): ?>
            <?php component('admin/components/common/icon', ['name' => $leftIcon, 'size' => 'sm', 'class' => 'mr-1.5']); ?>
        <?php endif; ?>
        <span><?= e($label) ?></span>
        <?php if (!empty($rightIcon) && !$loading): ?>
            <?php component('admin/components/common/icon', ['name' => $rightIcon, 'size' => 'sm', 'class' => 'ml-1.5']); ?>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= e($type) ?>" class="<?= $classes ?>" <?= $disabled || $loading ? 'disabled' : '' ?>>
        <?php if ($loading): ?>
            <?php component('admin/components/common/spinner', ['size' => 'sm', 'class' => 'mr-2']); ?>
        <?php elseif (!empty($leftIcon)): ?>
            <?php component('admin/components/common/icon', ['name' => $leftIcon, 'size' => 'sm', 'class' => 'mr-1.5']); ?>
        <?php endif; ?>
        <span><?= e($label) ?></span>
        <?php if (!empty($rightIcon) && !$loading): ?>
            <?php component('admin/components/common/icon', ['name' => $rightIcon, 'size' => 'sm', 'class' => 'ml-1.5']); ?>
        <?php endif; ?>
    </button>
<?php endif; ?>