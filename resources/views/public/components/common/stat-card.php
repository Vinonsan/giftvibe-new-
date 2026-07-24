<?php
/**
 * Variables:
 * @var string $title
 * @var string $value
 * @var string|null $icon
 */
?>
<div class="bg-white border border-slate-100 p-6 rounded-card shadow-card flex items-center justify-between">
    <div class="space-y-1">
        <span class="text-sm text-slate-500 font-medium"><?= e($title) ?></span>
        <h3 class="text-2xl font-bold text-slate-900"><?= e($value) ?></h3>
    </div>
    <?php if (!empty($icon)): ?>
        <div class="text-primary bg-primary-50 p-3 rounded-full">
            <?php component('public/components/common/icon', ['name' => $icon, 'size' => 'lg']); ?>
        </div>
    <?php endif; ?>
</div>