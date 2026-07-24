<?php
/**
 * Variables:
 * @var string $title
 * @var string $value
 * @var string $icon
 * @var string|null $link
 */
?>
<div class="bg-white border border-slate-100 p-5 rounded-card shadow-card flex gap-4">
    <div class="flex-shrink-0 text-primary bg-primary-50 w-10 h-10 rounded-full flex items-center justify-center">
        <?php component('public/components/common/icon', ['name' => $icon, 'size' => 'md']); ?>
    </div>
    <div>
        <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider"><?= e($title) ?></h4>
        <?php if (!empty($link)): ?>
            <a href="<?= e($link) ?>" class="mt-1 block text-base font-semibold text-slate-800 hover:text-primary transition-colors"><?= e($value) ?></a>
        <?php else: ?>
            <p class="mt-1 text-base font-semibold text-slate-800"><?= e($value) ?></p>
        <?php endif; ?>
    </div>
</div>