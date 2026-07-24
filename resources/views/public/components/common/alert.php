<?php
/**
 * Variables:
 * @var string $message
 * @var string|null $type (success, info, warning, danger)
 * @var string|null $title
 * @var bool|null $dismissible
 */
$type = $type ?? 'info';
$dismissible = $dismissible ?? false;

$variants = [
    'success' => ['bg' => 'bg-green-50 text-green-800 border-green-200', 'icon' => 'success'],
    'info' => ['bg' => 'bg-blue-50 text-blue-800 border-blue-200', 'icon' => 'info'],
    'warning' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'icon' => 'warning'],
    'danger' => ['bg' => 'bg-red-50 text-red-800 border-red-200', 'icon' => 'error'],
];
$alert = $variants[$type] ?? $variants['info'];
?>
<div class="flex p-4 border rounded-card shadow-sm <?= $alert['bg'] ?>" role="alert" data-toast>
    <div class="flex-shrink-0">
        <?php component('public/components/common/icon', ['name' => $alert['icon'], 'size' => 'md']); ?>
    </div>
    <div class="ml-3 flex-1">
        <?php if (!empty($title)): ?>
            <h3 class="text-sm font-bold"><?= e($title) ?></h3>
        <?php endif; ?>
        <div class="text-sm mt-1"><?= e($message) ?></div>
    </div>
    <?php if ($dismissible): ?>
        <div class="ml-auto pl-3">
            <button type="button" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2" data-toast-dismiss>
                <span class="sr-only">Dismiss</span>
                <?php component('public/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
            </button>
        </div>
    <?php endif; ?>
</div>