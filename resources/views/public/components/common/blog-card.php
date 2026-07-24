<?php
/**
 * Variables:
 * @var string $title
 * @var string $url
 * @var string $image
 * @var string $excerpt
 * @var string|null $author
 * @var string|null $date
 * @var string|null $category
 * @var string|null $readingTime
 */
?>
<article class="bg-white rounded-card border border-slate-100 overflow-hidden shadow-card flex flex-col group h-full">
    <div class="aspect-video bg-slate-100 overflow-hidden">
        <img src="<?= e($image) ?>" alt="<?= e($title) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
    </div>
    <div class="p-6 flex-grow flex flex-col justify-between">
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <?php if (!empty($category)): ?>
                    <span class="font-semibold uppercase text-primary"><?= e($category) ?></span>
                    <span>&bull;</span>
                <?php endif; ?>
                <span><?= e($date ?? 'Today') ?></span>
                <?php if (!empty($readingTime)): ?>
                    <span>&bull;</span>
                    <span><?= e($readingTime) ?> min read</span>
                <?php endif; ?>
            </div>
            <h3 class="text-lg font-bold text-slate-800 group-hover:text-primary transition-colors line-clamp-2">
                <a href="<?= e($url) ?>"><?= e($title) ?></a>
            </h3>
            <p class="text-slate-500 text-sm line-clamp-3"><?= e($excerpt) ?></p>
        </div>
        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-semibold text-xs uppercase">
                <?= e(substr($author ?? 'A', 0, 1)) ?>
            </div>
            <span class="text-sm font-medium text-slate-700"><?= e($author ?? 'Author') ?></span>
        </div>
    </div>
</article>