<?php
/**
 * Variables:
 * @var string $name
 * @var string $text
 * @var float $rating
 * @var string|null $location
 * @var string|null $avatar
 */
?>
<figure class="bg-white border border-slate-100 p-6 rounded-card shadow-card flex flex-col justify-between space-y-4">
    <blockquote class="text-slate-600 text-sm italic">
        &ldquo;<?= e($text) ?>&rdquo;
    </blockquote>
    <figcaption class="flex items-center gap-3 pt-4 border-t border-slate-100">
        <?php if (!empty($avatar)): ?>
            <img class="h-10 w-10 rounded-full object-cover" src="<?= e($avatar) ?>" alt="<?= e($name) ?>">
        <?php else: ?>
            <div class="h-10 w-10 rounded-full bg-primary-50 text-primary flex items-center justify-center font-bold text-sm uppercase">
                <?= e(substr($name, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <div class="text-sm font-bold text-slate-900"><?= e($name) ?></div>
            <div class="flex items-center gap-2 mt-0.5">
                <?php component('public/components/common/rating', ['rating' => $rating, 'size' => 'xs']); ?>
                <?php if (!empty($location)): ?>
                    <span class="text-xs text-slate-400">&bull; <?= e($location) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </figcaption>
</figure>