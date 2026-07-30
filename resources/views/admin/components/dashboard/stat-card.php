<?php
/**
 * Variables:
 * @var string $title
 * @var string $value
 * @var string|null $change Percent increase/decrease: +12%
 * @var string|null $description
 * @var string|null $icon
 */
$isPositive = isset($change) && str_starts_with($change, '+');
$changeColor = $isPositive ? 'text-green-600' : 'text-red-500';
?>
<div class="flex items-center justify-between gap-4 rounded-card border border-primary-900/10 bg-white p-5 shadow-card sm:p-6">
    <div class="space-y-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= e($title) ?></span>
        <h3 class="text-xl font-extrabold text-primary sm:text-2xl"><?= e($value) ?></h3>
        <?php if (isset($change)): ?>
            <span class="text-xs font-semibold <?= $changeColor ?>"><?= e($change) ?> vs last month</span>
        <?php elseif (!empty($description)): ?>
            <span class="block text-xs font-semibold text-slate-500"><?= e($description) ?></span>
        <?php endif; ?>
    </div>
    <?php if (!empty($icon)): ?>
        <div class="rounded-card border border-primary-100 bg-primary-50 p-3 text-primary">
            <?php component('admin/components/common/icon', ['name' => $icon, 'size' => 'lg']); ?>
        </div>
    <?php endif; ?>
</div>
