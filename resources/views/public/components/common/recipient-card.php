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
    <div class="mx-auto w-16 h-16 rounded-full overflow-hidden bg-slate-100 mb-4 border-2 border-transparent group-hover:border-primary transition-colors">
        <?php if (!empty($image)): ?>
            <img src="<?= e($image) ?>" alt="<?= e($title) ?>" class="w-full h-full object-cover">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-secondary-50 text-secondary-500">
                <?php component('public/components/common/icon', ['name' => 'user', 'size' => 'lg']); ?>
            </div>
        <?php endif; ?>
    </div>
    <h3 class="text-base font-bold text-slate-800 group-hover:text-primary transition-colors"><?= e($title) ?></h3>
    <?php if (!empty($description)): ?>
        <p class="mt-2 text-xs text-slate-500"><?= e($description) ?></p>
    <?php endif; ?>
</a>