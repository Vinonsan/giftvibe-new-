<?php
/**
 * Variables:
 * @var string $title
 * @var string $url
 * @var string $icon
 * @var string $description
 */
?>
<a href="<?= e($url) ?>" class="block bg-white border border-slate-200 p-5 rounded-card shadow-card hover:shadow-md transition-shadow group focus:outline-none focus:ring-2 focus:ring-primary-500">
    <div class="text-primary bg-primary-50 w-10 h-10 rounded-full flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-colors border border-primary-100">
        <?php component('admin/components/common/icon', ['name' => $icon, 'size' => 'md']); ?>
    </div>
    <h3 class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors"><?= e($title) ?></h3>
    <p class="mt-1 text-xs text-slate-500 leading-relaxed"><?= e($description) ?></p>
</a>