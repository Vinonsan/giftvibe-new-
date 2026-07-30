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
?>
<?php if (empty($products)): ?>
    <div class="rounded-card border border-slate-200 bg-white px-6 py-12 text-center shadow-card">
        <h3 class="text-lg font-extrabold text-slate-900">No published products found</h3>
        <p class="mt-2 text-sm text-slate-500">Try a different search, category, or stock filter.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($products as $product): ?>
            <?php
            $price = (float) ($product['sale_price'] ?: $product['base_price']);
            $oldPrice = (!empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['base_price']) ? (float) $product['base_price'] : null;
            $discountBadge = $oldPrice ? 'Offer' : null;
            ?>
            <?php component('public/components/common/product-card', [
                'productId' => (int) $product['id'],
                'name' => $product['name'],
                'url' => url('/product/' . $product['slug']),
                'image' => $imageUrl($product['primary_image'] ?? null),
                'alt' => ($product['name'] ?? 'GiftVibe.lk product') . ' product image',
                'price' => $price,
                'oldPrice' => $oldPrice,
                'discountBadge' => $discountBadge,
                'featured' => (bool) ($product['is_featured'] ?? false),
                'rating' => isset($product['average_rating']) ? (float) $product['average_rating'] : null,
                'reviewCount' => isset($product['reviews_count']) ? (int) $product['reviews_count'] : null,
                'likeCount' => (int) ($product['like_count'] ?? 0),
                'shareUrl' => url('/product/' . $product['slug']),
            ]); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
