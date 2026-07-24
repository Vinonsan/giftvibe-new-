<?php
/**
 * Variables:
 * @var string $title
 * @var string $value
 * @var string|null $change Percent increase/decrease: +12%
 * @var string|null $icon
 */
$isPositive = isset($change) && str_starts_with($change, '+');
$changeColor = $isPositive ? 'text-green-600' : 'text-red-500';
?>
<div class="bg-white border border-slate-200 p-6 rounded-card shadow-card flex items-center justify-between">
    <div class="space-y-2">
        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider"><?= e($title) ?></span>
        <h3 class="text-2xl font-extrabold text-slate-900"><?= e($value) ?></h3>
        <?php if (isset($change)): ?>
            <span class="text-xs font-semibold <?= $changeColor ?>"><?= e($change) ?> vs last month</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($icon)): ?>
        <div class="text-primary bg-primary-50 p-3 rounded-full border border-primary-100">
            <?php component('admin/components/common/icon', ['name' => $icon, 'size' => 'lg']); ?>
        </div>
    <?php endif; ?>
</div>