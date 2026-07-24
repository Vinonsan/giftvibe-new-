<?php
/**
 * Variables:
 * @var string $title
 * @var string $description
 * @var string|null $icon
 * @var array|null $cta Button array e.g., ['label' => 'Shop Now', 'href' => '/products']
 */
$icon = $icon ?? 'gift';
?>
<div class="text-center py-16 px-4 bg-white border border-slate-100 rounded-card shadow-card max-w-md mx-auto">
    <div class="mx-auto h-16 w-16 text-slate-300 flex items-center justify-center bg-slate-50 rounded-full mb-6">
        <?php component('public/components/common/icon', ['name' => $icon, 'size' => 'xl']); ?>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2"><?= e($title) ?></h3>
    <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto leading-relaxed"><?= e($description) ?></p>
    <?php if (!empty($cta)): ?>
        <?php component('public/components/common/button', [
            'label' => $cta['label'],
            'href' => $cta['href'] ?? null,
            'variant' => 'primary',
            'size' => 'md'
        ]); ?>
    <?php endif; ?>
</div>