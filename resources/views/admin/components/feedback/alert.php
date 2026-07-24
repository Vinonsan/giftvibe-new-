<?php
/**
 * Variables:
 * @var string $message
 * @var string|null $type (success, warning, danger, info)
 */
$type = $type ?? 'info';
$variants = [
    'success' => 'bg-green-50 text-green-800 border-green-200',
    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
    'danger' => 'bg-red-50 text-red-800 border-red-200',
    'info' => 'bg-blue-50 text-blue-800 border-blue-200',
];
$alertClass = $variants[$type] ?? $variants['info'];
?>
<div class="p-4 border rounded-card text-sm font-semibold flex items-center gap-2 <?= $alertClass ?>" role="alert">
    <?php component('admin/components/common/icon', ['name' => $type === 'danger' ? 'warning' : 'check', 'size' => 'sm']); ?>
    <span><?= e($message) ?></span>
</div>