<?php
/**
 * Variables:
 * @var string $title
 * @var string $description
 * @var string|null $icon
 * @var array|null $cta ['label' => '...', 'href' => '...']
 */
$icon = $icon ?? 'shopping-bag';
?>
<div class="text-center py-12 px-4 bg-white border border-slate-200 rounded-card shadow-sm max-w-md mx-auto">
    <div class="mx-auto h-12 w-12 text-slate-300 flex items-center justify-center bg-slate-50 rounded-full mb-4">
        <?php component('admin/components/common/icon', ['name' => $icon, 'size' => 'lg']); ?>
    </div>
    <h3 class="text-sm font-bold text-slate-800 mb-1"><?= e($title) ?></h3>
    <p class="text-xs text-slate-500 mb-5 max-w-xs mx-auto leading-relaxed"><?= e($description) ?></p>
    <?php if (!empty($cta)): ?>
        <?php component('admin/components/common/button', [
            'label' => $cta['label'],
            'href' => $cta['href'] ?? null,
            'variant' => 'primary',
            'size' => 'sm'
        ]); ?>
    <?php endif; ?>
</div>