<?php
/**
 * Variables:
 * @var string $message
 * @var string|null $type
 */
$type = $type ?? 'success';
$bgColors = [
    'success' => 'bg-green-600',
    'warning' => 'bg-amber-500',
    'danger' => 'bg-red-600',
    'info' => 'bg-blue-600',
];
$bgColor = $bgColors[$type] ?? $bgColors['success'];
?>
<div class="fixed bottom-4 right-4 z-50 flex items-center gap-3 text-white px-4 py-3 rounded-card shadow-lg <?= $bgColor ?>" data-toast role="status">
    <span class="text-xs font-bold"><?= e($message) ?></span>
    <button type="button" class="text-white/80 hover:text-white focus:outline-none" data-toast-dismiss>
        <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'xs']); ?>
    </button>
</div>