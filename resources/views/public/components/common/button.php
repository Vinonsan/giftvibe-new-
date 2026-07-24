<?php
/**
 * Variables:
 * @var string $label
 * @var string|null $href
 * @var string|null $type
 * @var string|null $variant
 * @var string|null $size
 * @var bool|null $disabled
 * @var bool|null $loading
 * @var string|null $leftIcon
 * @var string|null $rightIcon
 * @var bool|null $fullWidth
 * @var string|null $target
 * @var string|null $rel
 * @var string|null $id
 */

$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$type = $type ?? 'button';
$disabled = $disabled ?? false;
$loading = $loading ?? false;
$fullWidth = $fullWidth ?? false;

$variantClasses = [
    'primary' => 'bg-primary text-white hover:bg-primary-600 focus:ring-primary-500',
    'secondary' => 'bg-secondary text-white hover:bg-secondary-600 focus:ring-secondary-500',
    'accent' => 'bg-rose-500 text-white hover:bg-rose-600 focus:ring-rose-400',
    'outline' => 'border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-primary-500',
    'ghost' => 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-500',
    'danger' => 'bg-danger text-white hover:bg-danger-600 focus:ring-danger-500',
    'success' => 'bg-success text-white hover:bg-success-600 focus:ring-success-500',
    'link' => 'text-primary hover:underline bg-transparent p-0 focus:ring-0 focus:ring-offset-0',
];

$sizeClasses = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    'xl' => 'px-6 py-3 text-lg',
];

$baseClass = "inline-flex items-center justify-center font-medium rounded-button transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2";
$widthClass = $fullWidth ? "w-full" : "";
$disabledClass = ($disabled || $loading) ? "opacity-50 cursor-not-allowed pointer-events-none" : "";

$classes = implode(' ', array_filter([
    $baseClass,
    $variantClasses[$variant] ?? $variantClasses['primary'],
    $sizeClasses[$size] ?? $sizeClasses['md'],
    $widthClass,
    $disabledClass
]));

$ariaLabelAttr = isset($label) ? 'aria-label="' . e($label) . '"' : '';
$idAttr = isset($id) ? 'id="' . e($id) . '"' : '';

if (!empty($href)): ?>
    <a href="<?= e($href) ?>" class="<?= $classes ?>" <?= $idAttr ?> <?= $ariaLabelAttr ?> <?= !empty($target) ? 'target="' . e($target) . '"' : '' ?> <?= !empty($rel) ? 'rel="' . e($rel) . '"' : '' ?>>
        <?php if ($loading): ?>
            <?php component('public/components/common/spinner', ['size' => 'sm', 'class' => 'mr-2']); ?>
        <?php elseif (!empty($leftIcon)): ?>
            <?php component('public/components/common/icon', ['name' => $leftIcon, 'size' => 'sm', 'class' => 'mr-1.5']); ?>
        <?php endif; ?>
        <span><?= e($label ?? '') ?></span>
        <?php if (!empty($rightIcon) && !$loading): ?>
            <?php component('public/components/common/icon', ['name' => $rightIcon, 'size' => 'sm', 'class' => 'ml-1.5']); ?>
        <?php endif; ?>
    </a>
<?php else: ?>
    <button type="<?= e($type) ?>" class="<?= $classes ?>" <?= $idAttr ?> <?= $ariaLabelAttr ?> <?= $disabled || $loading ? 'disabled' : '' ?>>
        <?php if ($loading): ?>
            <?php component('public/components/common/spinner', ['size' => 'sm', 'class' => 'mr-2']); ?>
        <?php elseif (!empty($leftIcon)): ?>
            <?php component('public/components/common/icon', ['name' => $leftIcon, 'size' => 'sm', 'class' => 'mr-1.5']); ?>
        <?php endif; ?>
        <span><?= e($label ?? '') ?></span>
        <?php if (!empty($rightIcon) && !$loading): ?>
            <?php component('public/components/common/icon', ['name' => $rightIcon, 'size' => 'sm', 'class' => 'ml-1.5']); ?>
        <?php endif; ?>
    </button>
<?php endif; ?>