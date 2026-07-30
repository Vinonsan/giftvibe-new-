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
<div class="min-w-0 rounded-card border border-primary-900/10 bg-white p-5 shadow-card sm:p-6">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 space-y-2">
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-500"><?= e($title) ?></span>
            <h3 class="break-words text-xl font-extrabold leading-tight text-primary sm:text-2xl"><?= e($value) ?></h3>
        </div>
        <?php if (!empty($icon)): ?>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-card border border-primary-100 bg-primary-50 text-primary">
                <?php component('admin/components/common/icon', ['name' => $icon, 'size' => 'sm']); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="mt-3">
        <?php if (isset($change)): ?>
            <span class="text-xs font-semibold <?= $changeColor ?>"><?= e($change) ?> vs last month</span>
        <?php elseif (!empty($description)): ?>
            <span class="block text-xs font-semibold text-slate-500"><?= e($description) ?></span>
        <?php endif; ?>
    </div>
</div>
