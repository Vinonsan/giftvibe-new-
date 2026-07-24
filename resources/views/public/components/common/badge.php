<?php
/**
 * Variables:
 * @var string $label
 * @var string|null $variant (primary, secondary, success, warning, danger, info, gray)
 */
$variant = $variant ?? 'primary';
$variants = [
    'primary' => 'bg-primary-50 text-primary-700 border-primary-200',
    'secondary' => 'bg-secondary-50 text-secondary-700 border-secondary-200',
    'success' => 'bg-green-50 text-green-700 border-green-200',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
    'info' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
    'gray' => 'bg-slate-100 text-slate-600 border-slate-200',
];
$badgeClass = $variants[$variant] ?? $variants['primary'];
?>
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
    <?= e($label) ?>
</span>