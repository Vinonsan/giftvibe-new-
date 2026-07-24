<?php
/**
 * Variables:
 * @var string $title
 * @var string $url
 * @var string|null $image
 * @var string|null $icon
 * @var string|null $description
 */
?>
<a href="<?= e($url) ?>" class="block bg-white border border-slate-100 rounded-card p-6 shadow-card hover:shadow-md transition-shadow group text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
    <div class="mx-auto w-12 h-12 rounded-full bg-primary-50 text-primary flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-colors">
        <?php if (!empty($icon)): ?>
            <?php component('public/components/common/icon', ['name' => $icon, 'size' => 'lg']); ?>
        <?php else: ?>
            <?php component('public/components/common/icon', ['name' => 'gift', 'size' => 'lg']); ?>
        <?php endif; ?>
    </div>
    <h3 class="text-base font-bold text-slate-800 group-hover:text-primary transition-colors"><?= e($title) ?></h3>
    <?php if (!empty($description)): ?>
        <p class="mt-2 text-xs text-slate-500 line-clamp-2"><?= e($description) ?></p>
    <?php endif; ?>
</a>