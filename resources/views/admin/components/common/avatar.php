<?php
/**
 * Variables:
 * @var string|null $src
 * @var string $name
 * @var string|null $size (sm, md, lg)
 */
$size = $size ?? 'md';
$sizeClasses = [
    'sm' => 'w-8 h-8 text-xs',
    'md' => 'w-10 h-10 text-sm',
    'lg' => 'w-12 h-12 text-base',
];
$avatarClass = $sizeClasses[$size] ?? $sizeClasses['md'];
?>
<div class="<?= $avatarClass ?> rounded-full overflow-hidden bg-slate-200 flex items-center justify-center font-bold text-slate-600 uppercase border border-slate-300">
    <?php if (!empty($src)): ?>
        <img src="<?= e($src) ?>" alt="<?= e($name) ?>" class="w-full h-full object-cover">
    <?php else: ?>
        <span><?= e(substr($name, 0, 1)) ?></span>
    <?php endif; ?>
</div>