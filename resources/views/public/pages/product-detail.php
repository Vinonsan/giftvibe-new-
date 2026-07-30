<?php
$imageUrl = static function (?string $path): string {
    if (!$path) {
        return asset('/public/assets/images/hero_gift_box.jpg');
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return asset('/' . ltrim($path, '/'));
};
$mainImage = $imageUrl($images[0]['image_path'] ?? ($product['primary_image'] ?? null));
$currentPrice = (float) ($product['sale_price'] ?: $product['base_price']);
$hasOffer = !empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['base_price'];
$shareUrl = url('/product/' . $product['slug']);
?>
<section class="bg-white py-10">
    <div class="mx-auto grid max-w-container grid-cols-1 gap-10 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-6">
            <div class="overflow-hidden rounded-card border border-slate-200 bg-slate-100 shadow-card">
                <img src="<?= e($mainImage) ?>" alt="<?= e($product['name']) ?> main product image" class="aspect-square w-full object-cover">
            </div>
            <?php if (count($images ?? []) > 1): ?>
                <div class="mt-4 grid grid-cols-4 gap-3">
                    <?php foreach ($images as $image): ?>
                        <img src="<?= e($imageUrl($image['image_path'])) ?>" alt="<?= e($image['alt_text'] ?: $product['name'] . ' additional product image') ?>" class="aspect-square rounded-button border border-slate-200 object-cover">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="lg:col-span-6">
            <div class="flex flex-wrap gap-2">
                <?php if ($hasOffer): ?><?php component('public/components/common/badge', ['label' => 'Offer', 'variant' => 'danger']); ?><?php endif; ?>
                <?php if (!empty($product['is_featured'])): ?><?php component('public/components/common/badge', ['label' => 'Featured', 'variant' => 'success']); ?><?php endif; ?>
            </div>
            <h1 class="mt-4 text-3xl font-black text-slate-950"><?= e($product['name']) ?></h1>
            <p class="mt-3 text-sm leading-6 text-slate-600"><?= e($product['short_description'] ?: 'Published GiftVibe.lk product available for gifting.') ?></p>
            <div class="mt-5"><?php component('public/components/common/price', ['price' => $currentPrice, 'oldPrice' => $hasOffer ? (float) $product['base_price'] : null]); ?></div>
            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Stock</span>
                    <strong class="<?= (int) $product['stock_quantity'] > 0 ? 'text-green-700' : 'text-danger' ?>"><?= (int) $product['stock_quantity'] > 0 ? (int) $product['stock_quantity'] . ' available' : 'Out of stock' ?></strong>
                </div>
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Likes</span>
                    <strong class="text-primary"><?= (int) ($product['like_count'] ?? 0) ?> wishlist saves</strong>
                </div>
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4 col-span-2">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Average rating</span>
                    <strong class="text-primary"><?= number_format((float) ($averageRating ?? 0), 1) ?>/5 from <?= (int) ($reviewsCount ?? 0) ?> approved reviews</strong>
                </div>
            </div>
            <?php if (!empty($variants)): ?>
                <div class="mt-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Colours and variants</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <?php foreach ($variants as $variant): ?>
                            <div class="rounded-card border border-slate-200 bg-white p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <strong class="text-sm text-slate-900"><?= e($variant['name']) ?></strong>
                                    <?php if (!empty($variant['color_hex'])): ?><span class="h-5 w-5 rounded-full border border-slate-300" style="background: <?= e($variant['color_hex']) ?>"></span><?php endif; ?>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">SKU <?= e($variant['sku']) ?> - <?= (int) $variant['stock_quantity'] ?> in stock</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?= e(url('/cart/add')) ?>" class="mt-6 rounded-card border border-slate-200 bg-slate-50 p-4">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <?php if (!empty($variants)): ?>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Select variant</label>
                    <select name="variant_id" class="mb-3 w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm">
                        <?php foreach ($variants as $variant): ?>
                            <option value="<?= (int) $variant['id'] ?>"><?= e($variant['name']) ?><?= $variant['color_name'] ? ' - ' . e($variant['color_name']) : '' ?> - <?= (int) $variant['stock_quantity'] ?> in stock</option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Quantity</label>
                        <input type="number" name="quantity" min="1" max="<?= max(1, (int) $product['stock_quantity']) ?>" value="1" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Gift message</label>
                        <input name="gift_message" maxlength="500" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm" placeholder="Optional message for the recipient">
                    </div>
                </div>
                <button class="mt-4 w-full rounded-button bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-primary-700" <?= (int) $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>Add to Cart</button>
            </form>

            <div class="mt-4 flex flex-wrap gap-3">
                <form method="POST" action="<?= e(url('/wishlist/toggle')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="redirect_to" value="<?= e('/product/' . $product['slug']) ?>">
                    <button class="inline-flex items-center gap-2 rounded-button bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-primary-700"><?php component('public/components/common/icon', ['name' => 'heart', 'size' => 'sm']); ?> Save to Wishlist</button>
                </form>
                <a href="<?= e('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl)) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-button border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"><?php component('public/components/common/icon', ['name' => 'external-link', 'size' => 'sm']); ?> Share</a>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-extrabold text-slate-950">Related published gifts</h2>
        <div class="mt-6"><?php component('public/partials/product-grid', ['products' => $relatedProducts ?? []]); ?></div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto grid max-w-container grid-cols-1 gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-7">
            <h2 class="text-2xl font-extrabold text-slate-950">Customer reviews</h2>
            <div class="mt-5 space-y-4">
                <?php foreach (($reviews ?? []) as $review): ?>
                    <article class="rounded-card border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <strong class="text-slate-950"><?= e($review['title'] ?: 'GiftVibe.lk review') ?></strong>
                            <span class="text-sm font-bold text-primary"><?= (int) $review['rating'] ?>/5</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-600"><?= e($review['body']) ?></p>
                        <p class="mt-2 text-xs text-slate-400"><?= e(trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?: 'Customer') ?></p>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($reviews)): ?><p class="rounded-card border border-slate-200 bg-slate-50 p-5 text-sm text-slate-500">No approved reviews yet.</p><?php endif; ?>
            </div>
        </div>
        <div class="lg:col-span-5">
            <div class="rounded-card border border-slate-200 bg-slate-50 p-5 shadow-card">
                <h3 class="text-lg font-extrabold text-slate-950">Write a review</h3>
                <?php if (App\Core\Auth::isCustomer()): ?>
                    <form method="POST" action="<?= e(url('/reviews')) ?>" class="mt-4 space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <select name="rating" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm">
                            <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?>
                        </select>
                        <input name="title" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm" placeholder="Review title">
                        <textarea name="body" rows="4" required class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm" placeholder="Share your experience"></textarea>
                        <button class="w-full rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Submit for approval</button>
                    </form>
                <?php else: ?>
                    <a href="<?= e(url('/login')) ?>" class="mt-4 inline-flex rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Login to review</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
