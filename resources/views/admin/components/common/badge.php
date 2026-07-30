<?php
/**
 * Variables:
 * @var string $label
 * @var string|null $variant (primary, success, warning, danger, info, gray)
 */
$variant = $variant ?? 'gray';
$variants = [
    'primary' => 'bg-primary-50 text-primary-700 border-primary-100',
    'success' => 'bg-green-50 text-green-700 border-green-200',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
    'info' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
    'gray' => 'bg-slate-100 text-slate-700 border-slate-200',
];
$badgeClass = $variants[$variant] ?? $variants['gray'];
?>
<span class="inline-flex items-center rounded-button border px-2.5 py-1 text-xs font-semibold <?= $badgeClass ?>">
    <?= e($label) ?>
</span>
