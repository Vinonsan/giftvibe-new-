<?php
/**
 * Variables:
 * @var string $name
 * @var string $url
 * @var string $image
 * @var float $price
 * @var float|null $oldPrice
 * @var string|null $category
 * @var string|null $discountBadge
 * @var bool|null $featured
 * @var float|null $rating
 * @var int|null $reviewCount
 * @var string|null $alt
 * @var int|null $width
 * @var int|null $height
 */
$featured = $featured ?? false;
$width = $width ?? 320;
$height = $height ?? 320;
$alt = $alt ?? $name;
?>
<article class="bg-white rounded-card shadow-card overflow-hidden border border-slate-100 flex flex-col group relative">
    <!-- Badges -->
    <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
        <?php if (!empty($discountBadge)): ?>
            <?php component('public/components/common/badge', ['label' => $discountBadge, 'variant' => 'danger']); ?>
        <?php endif; ?>
        <?php if ($featured): ?>
            <?php component('public/components/common/badge', ['label' => 'Featured', 'variant' => 'success']); ?>
        <?php endif; ?>
    </div>

    <!-- Product Image -->
    <div class="relative overflow-hidden bg-slate-100 aspect-square">
        <?php component('public/components/common/image', [
            'src' => $image,
            'alt' => $alt,
            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
            'width' => $width,
            'height' => $height,
            'lazy' => true
        ]); ?>
    </div>

    <!-- Details -->
    <div class="p-4 flex-grow flex flex-col justify-between">
        <div class="space-y-1">
            <?php if (!empty($category)): ?>
                <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider"><?= e($category) ?></span>
            <?php endif; ?>
            <h3 class="text-sm font-semibold text-slate-800 group-hover:text-primary transition-colors">
                <a href="<?= e($url) ?>" class="focus:outline-none">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    <?= e($name) ?>
                </a>
            </h3>
            <!-- Rating -->
            <?php if (isset($rating)): ?>
                <div class="flex items-center gap-1">
                    <?php component('public/components/common/rating', ['rating' => $rating]); ?>
                    <?php if (isset($reviewCount)): ?>
                        <span class="text-xs text-slate-400">(<?= (int)$reviewCount ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <?php component('public/components/common/price', ['price' => $price, 'oldPrice' => $oldPrice]); ?>
            <span class="text-primary group-hover:translate-x-1 transition-transform">
                <?php component('public/components/common/icon', ['name' => 'arrow-right', 'size' => 'sm']); ?>
            </span>
        </div>
    </div>
</article>