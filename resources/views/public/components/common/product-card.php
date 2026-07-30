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
 * @var int|null $productId
 * @var int|null $likeCount
 * @var string|null $shareUrl
 * @var bool|null $wishlisted
 */
$featured = $featured ?? false;
$width = $width ?? 320;
$height = $height ?? 320;
$alt = $alt ?? $name;
$likeCount = (int) ($likeCount ?? 0);
?>
<article class="bg-white rounded-card shadow-card overflow-hidden border border-slate-100 flex flex-col group relative">
    <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
        <?php if (!empty($discountBadge)): ?>
            <?php component('public/components/common/badge', ['label' => $discountBadge, 'variant' => 'danger']); ?>
        <?php endif; ?>
        <?php if ($featured): ?>
            <?php component('public/components/common/badge', ['label' => 'Featured', 'variant' => 'success']); ?>
        <?php endif; ?>
    </div>

    <div class="absolute top-2 right-2 z-20 flex gap-1">
        <?php if (!empty($productId)): ?>
            <form method="POST" action="<?= e(url('/wishlist/toggle')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $productId ?>">
                <input type="hidden" name="redirect_to" value="<?= e($_SERVER['REQUEST_URI'] ?? '/shop') ?>">
                <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-white/95 px-2 py-1 text-xs font-bold text-primary shadow-sm ring-1 ring-slate-200 hover:bg-primary hover:text-white" aria-label="Toggle wishlist for <?= e($name) ?>">
                    <?php component('public/components/common/icon', ['name' => 'heart', 'size' => 'xs']); ?>
                    <span><?= $likeCount ?></span>
                </button>
            </form>
        <?php endif; ?>
        <?php if (!empty($shareUrl)): ?>
            <a href="<?= e('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl)) ?>" target="_blank" rel="noopener" class="inline-flex rounded-full bg-white/95 p-1.5 text-slate-600 shadow-sm ring-1 ring-slate-200 hover:text-primary" aria-label="Share <?= e($name) ?>">
                <?php component('public/components/common/icon', ['name' => 'external-link', 'size' => 'xs']); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="relative overflow-hidden bg-slate-100 aspect-square">
        <a href="<?= e($url) ?>" class="block h-full focus:outline-none focus:ring-2 focus:ring-primary">
            <?php component('public/components/common/image', [
                'src' => $image,
                'alt' => $alt,
                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                'width' => $width,
                'height' => $height,
                'lazy' => true
            ]); ?>
        </a>
    </div>

    <div class="p-4 flex-grow flex flex-col justify-between">
        <div class="space-y-1">
            <?php if (!empty($category)): ?>
                <span class="text-xs uppercase font-semibold text-slate-400 tracking-wider"><?= e($category) ?></span>
            <?php endif; ?>
            <h3 class="text-sm font-semibold text-slate-800 group-hover:text-primary transition-colors">
                <a href="<?= e($url) ?>" class="focus:outline-none">
                    <?= e($name) ?>
                </a>
            </h3>
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
