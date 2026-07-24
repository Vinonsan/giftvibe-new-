<?php
/**
 * Variables:
 * @var float $rating Numeric value from 1 to 5
 * @var string|null $size (xs, sm, md)
 */
$size = $size ?? 'sm';
$rating = min(5, max(0, $rating));
$fullStars = floor($rating);
$hasHalf = ($rating - $fullStars) >= 0.5;
?>
<div class="flex items-center gap-0.5 text-amber-400" aria-label="Rating: <?= e($rating) ?> stars out of 5">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php if ($i <= $fullStars): ?>
            <?php component('public/components/common/icon', ['name' => 'star', 'size' => $size]); ?>
        <?php elseif ($i == $fullStars + 1 && $hasHalf): ?>
            <!-- Half star visualization with customized style or placeholder opacity -->
            <span class="relative inline-block overflow-hidden">
                <span class="opacity-30 absolute top-0 left-0">
                    <?php component('public/components/common/icon', ['name' => 'star', 'size' => $size]); ?>
                </span>
                <span class="block w-1/2 overflow-hidden">
                    <?php component('public/components/common/icon', ['name' => 'star', 'size' => $size]); ?>
                </span>
            </span>
        <?php else: ?>
            <span class="text-slate-200">
                <?php component('public/components/common/icon', ['name' => 'star', 'size' => $size]); ?>
            </span>
        <?php endif; ?>
    <?php endfor; ?>
</div>