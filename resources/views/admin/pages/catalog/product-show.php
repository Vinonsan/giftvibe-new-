<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
$imageUrl = static function (?string $path): string {
    if (!$path) {
        return asset('/public/assets/images/hero_gift_box.jpg');
    }

    return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
        ? $path
        : asset('/' . ltrim($path, '/'));
};

$categoryNames = array_map(static fn ($category) => $category['name'], $categories ?? []);
$averageRating = (float) ($stats['average_rating'] ?? 0);
$reviewsCount = (int) ($stats['reviews_count'] ?? 0);
$orderCount = (int) ($stats['order_count'] ?? 0);
$soldCount = (int) ($stats['sold_count'] ?? 0);
$likesCount = (int) ($stats['wishlist_count'] ?? 0);
$salesTotal = (float) ($stats['sales_total'] ?? 0);
$costTotal = (float) ($stats['cost_total'] ?? 0);
$profit = $salesTotal - $costTotal;
$primaryImage = $imageUrl($product['image_path'] ?? null);
$sellingPrice = (float) ($product['sale_price'] ?: $product['base_price']);
$costPrice = (float) ($product['cost_price'] ?? 0);
$unit = trim((string) ($product['unit'] ?? '')) ?: 'items';
$status = (string) ($product['status'] ?? 'draft');
$statusVariant = $status === 'active' ? 'success' : ($status === 'draft' ? 'warning' : 'gray');
$createdDate = !empty($product['created_at']) ? date('M d, Y', strtotime($product['created_at'])) : '-';
$updatedDate = !empty($product['updated_at']) ? date('M d, Y', strtotime($product['updated_at'])) : '-';
$recentOrders = array_slice($orders ?? [], 0, 5);
$latestReviews = array_slice($reviews ?? [], 0, 3);
$fallbackTags = array_filter(array_merge($categoryNames, [$unit]));
$ratingStars = static function (float $rating): string {
    $full = (int) round($rating);
    return str_repeat('&#9733;', max(0, min(5, $full))) . str_repeat('&#9734;', max(0, 5 - $full));
};
$row = static function (string $label, string $value): void {
    ?>
    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3 last:border-b-0">
        <dt class="text-sm font-medium text-slate-500"><?= e($label) ?></dt>
        <dd class="min-w-0 text-sm font-bold text-slate-900"><?= $value ?></dd>
    </div>
    <?php
};
$statusBadgeClass = static fn (string $variant) => $variant === 'success'
    ? 'border-green-200 bg-green-50 text-green-700'
    : ($variant === 'warning' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-slate-200 bg-slate-100 text-slate-700');
$variantPayloads = [];
$variantPayloads[] = [
    'id' => 0,
    'name' => (string) $product['name'],
    'sku' => (string) $product['sku'],
    'image' => $primaryImage,
    'price' => $money($sellingPrice),
    'cost' => $money($costPrice),
    'stock' => (int) $product['stock_quantity'],
    'stockLabel' => (int) $product['stock_quantity'] . ' (' . $unit . ')',
    'status' => $label($status),
    'statusClass' => $statusBadgeClass($statusVariant),
    'rating' => number_format($averageRating, 1),
    'reviews' => $reviewsCount,
    'orders' => $orderCount,
    'sales' => $money($salesTotal),
    'likes' => $likesCount,
    'profit' => $money($profit),
    'stars' => $ratingStars($averageRating),
];
foreach (($variants ?? []) as $variant) {
    $variantRating = (float) ($variant['average_rating'] ?? 0);
    $variantReviews = (int) ($variant['reviews_count'] ?? 0);
    $variantOrders = (int) ($variant['order_count'] ?? 0);
    $variantSold = (int) ($variant['sold_count'] ?? 0);
    $variantLikes = (int) ($variant['likes_count'] ?? 0);
    $variantPrice = $sellingPrice + (float) ($variant['price_adjustment'] ?? 0);
    $variantStatus = ($variant['status'] ?? 'active') === 'active' ? 'Active' : 'Inactive';
    $variantPayloads[] = [
        'id' => (int) $variant['id'],
        'name' => (string) $variant['name'],
        'sku' => (string) $variant['sku'],
        'image' => $imageUrl($variant['image_path'] ?? $product['image_path'] ?? null),
        'price' => $money($variantPrice),
        'cost' => $money($costPrice),
        'stock' => (int) $variant['stock_quantity'],
        'stockLabel' => (int) $variant['stock_quantity'],
        'status' => $variantStatus,
        'statusClass' => $variantStatus === 'Active' ? $statusBadgeClass('success') : $statusBadgeClass('gray'),
        'rating' => number_format($variantRating, 1),
        'reviews' => $variantReviews,
        'orders' => $variantOrders,
        'sales' => $money($variantPrice * $variantSold),
        'likes' => $variantLikes,
        'profit' => $money(($variantPrice - $costPrice) * $variantSold),
        'stars' => $ratingStars($variantRating),
    ];
}
?>

<div class="space-y-5" data-product-show>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-primary-950">Products</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Manage and edit your product details</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?= e(url('/admin/products')) ?>" class="inline-flex min-h-11 items-center gap-2 rounded-button border border-slate-200 bg-white px-5 text-sm font-extrabold text-primary-950 shadow-card transition hover:border-primary-200 hover:bg-primary-50">
                <span aria-hidden="true">&lt;</span>
                Back
            </a>
            <a href="<?= e(url('/admin/products')) ?>" class="inline-flex min-h-11 items-center gap-2 rounded-button bg-primary-900 px-5 text-sm font-extrabold text-white shadow-card transition hover:bg-primary-800">
                <?php component('admin/components/common/icon', ['name' => 'edit', 'size' => 'sm']); ?>
                Edit product
            </a>
        </div>
    </div>

    <section aria-label="Overview" class="space-y-3">
        <h2 class="text-base font-extrabold text-primary-950">Overview</h2>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="flex items-center gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-button bg-amber-50 text-amber-500">
                    <?php component('admin/components/common/icon', ['name' => 'star', 'size' => 'sm']); ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Average Rating</p>
                    <p class="mt-1 text-xl font-extrabold text-primary-950"><span data-product-selected-rating><?= number_format($averageRating, 1) ?></span><span class="text-sm text-slate-500">/5</span></p>
                    <p class="text-xs font-medium text-slate-500">(<span data-product-selected-reviews><?= $reviewsCount ?></span> reviews)</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-button bg-emerald-50 text-emerald-600">
                    <?php component('admin/components/common/icon', ['name' => 'shopping-bag', 'size' => 'sm']); ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Orders</p>
                    <p class="mt-1 text-xl font-extrabold text-primary-950" data-product-selected-orders><?= $orderCount ?></p>
                    <p class="text-xs font-medium text-slate-500">Total orders</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div>
                    <p class="text-xs font-semibold text-slate-500">$ Sales</p>
                    <p class="mt-1 text-xl font-extrabold text-primary-950" data-product-selected-sales><?= $money($salesTotal) ?></p>
                    <p class="text-xs font-medium text-slate-500">Total sales</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-button bg-emerald-50 text-emerald-600">
                    <?php component('admin/components/common/icon', ['name' => 'archive-box', 'size' => 'sm']); ?>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500">Stock</p>
                    <p class="mt-1 text-xl font-extrabold text-primary-950" data-product-selected-stock><?= (int) $product['stock_quantity'] ?></p>
                    <p class="text-xs font-medium text-slate-500"><?= e($unit) ?> in stock</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div>
                    <p class="text-xs font-semibold text-amber-600">Status</p>
                    <div class="mt-2"><?php component('admin/components/common/badge', ['label' => $label($status), 'variant' => $statusVariant]); ?></div>
                    <p class="mt-2 text-xs font-medium text-slate-500"><?= $status === 'active' ? 'Published' : 'Catalog item' ?></p>
                </div>
            </article>
        </div>
    </section>

    <div class="grid gap-5 xl:grid-cols-12">
        <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card xl:col-span-5">
            <h2 class="text-lg font-extrabold text-primary-950">Product Images</h2>
            <div class="group relative mt-5 overflow-hidden rounded-button bg-slate-100">
                <img src="<?= e($primaryImage) ?>" alt="<?= e($product['name']) ?>" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" width="720" height="540" data-product-main-image>
                <a href="<?= e($primaryImage) ?>" target="_blank" class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-button bg-white/95 text-primary-900 shadow-card transition hover:bg-primary-900 hover:text-white" aria-label="Open product image" data-product-main-image-link>
                    <?php component('admin/components/common/icon', ['name' => 'search', 'size' => 'sm']); ?>
                </a>
            </div>
            <div class="mt-4 grid grid-cols-[2.25rem_1fr_2.25rem] items-center gap-2">
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-button border border-slate-200 bg-white text-primary-950 shadow-card transition hover:bg-primary-50" aria-label="Scroll variants left" data-variant-scroll-left>&lt;</button>
                <div class="flex gap-3 overflow-x-auto scroll-smooth pb-1" data-variant-strip>
                    <button type="button" class="h-20 w-20 shrink-0 overflow-hidden rounded-button border border-primary-500 bg-slate-100 ring-2 ring-primary-100" data-variant-select data-variant-id="0" aria-label="Show main product">
                        <img src="<?= e($primaryImage) ?>" alt="" class="h-full w-full object-cover" width="80" height="80">
                    </button>
                    <?php foreach (($variants ?? []) as $variant): ?>
                        <button type="button" class="h-20 w-20 shrink-0 overflow-hidden rounded-button border border-slate-200 bg-slate-100 transition hover:border-primary-300" data-variant-select data-variant-id="<?= e((string) $variant['id']) ?>" aria-label="Show <?= e($variant['name']) ?>">
                            <img src="<?= e($imageUrl($variant['image_path'] ?? $product['image_path'] ?? null)) ?>" alt="" class="h-full w-full object-cover" width="80" height="80" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-button border border-slate-200 bg-white text-primary-950 shadow-card transition hover:bg-primary-50" aria-label="Scroll variants right" data-variant-scroll-right>&gt;</button>
            </div>
        </section>

        <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card xl:col-span-7">
            <h2 class="text-lg font-extrabold text-primary-950">Product Information</h2>
            <dl class="mt-5 border-t border-slate-200">
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">Product Name</dt>
                    <dd class="min-w-0 text-sm font-bold text-slate-900" data-product-info-name><?= e($product['name']) ?></dd>
                </div>
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">SKU</dt>
                    <dd class="min-w-0 text-sm font-bold text-slate-900" data-product-info-sku><?= e($product['sku']) ?></dd>
                </div>
                <?php $row('Category', e($categoryNames ? implode(', ', $categoryNames) : 'Uncategorized')); ?>
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">Selling Price</dt>
                    <dd class="min-w-0 text-sm font-bold text-success-700" data-product-info-price><?= e($money($sellingPrice)) ?></dd>
                </div>
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">Original Cost</dt>
                    <dd class="min-w-0 text-sm font-bold text-slate-900" data-product-info-cost><?= e($money($costPrice)) ?></dd>
                </div>
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">Stock</dt>
                    <dd class="min-w-0 text-sm font-bold text-slate-900" data-product-info-stock><?= e((string) (int) $product['stock_quantity']) ?> <span class="text-slate-500">(<?= e($unit) ?>)</span></dd>
                </div>
                <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                    <dt class="text-sm font-medium text-slate-500">Status</dt>
                    <dd class="min-w-0 text-sm font-bold text-slate-900"><span class="inline-flex rounded-button border px-2.5 py-1 text-xs font-semibold <?= e($statusBadgeClass($statusVariant)) ?>" data-product-info-status><?= e($label($status)) ?></span></dd>
                </div>
                <?php $row('Created', e($createdDate)); ?>
                <?php $row('Updated', e($updatedDate)); ?>
            </dl>
        </section>
    </div>

    <div class="grid gap-5 xl:grid-cols-12">
        <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card xl:col-span-6">
            <h2 class="text-lg font-extrabold text-primary-950">Description</h2>
            <p class="mt-4 text-sm leading-6 text-slate-600"><?= e($product['short_description'] ?: $product['description'] ?: 'No description added.') ?></p>
            <?php if (!empty($product['short_description']) && !empty($product['description'])): ?>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-500"><?= e($product['description']) ?></p>
            <?php endif; ?>
        </section>

        <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card xl:col-span-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-extrabold text-primary-950">Variants (<?= count($variants ?? []) ?>)</h2>
                <span class="text-primary-900"><?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'xs']); ?></span>
            </div>
            <div class="mt-5 space-y-4">
                <?php foreach (array_slice($variants ?? [], 0, 3) as $variant): ?>
                    <?php
                        $variantActive = ($variant['status'] ?? 'active') === 'active';
                        $variantPrice = $sellingPrice + (float) ($variant['price_adjustment'] ?? 0);
                    ?>
                    <article class="grid gap-4 sm:grid-cols-[8rem_1fr_auto]">
                        <img src="<?= e($imageUrl($variant['image_path'] ?? $product['image_path'] ?? null)) ?>" alt="<?= e($variant['name']) ?>" class="h-28 w-32 rounded-button object-cover" width="128" height="112" loading="lazy">
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-primary-950"><?= e($variant['name']) ?></h3>
                            <p class="mt-2 text-sm text-slate-500">SKU: <span class="font-semibold text-slate-700"><?= e($variant['sku']) ?></span></p>
                            <p class="mt-2 text-sm text-slate-500">Price: <span class="font-bold text-slate-900"><?= $money($variantPrice) ?></span></p>
                            <p class="mt-2 text-sm text-slate-500">Stock: <span class="font-bold text-slate-900"><?= (int) $variant['stock_quantity'] ?></span></p>
                            <button type="button" class="mt-4 inline-flex min-h-9 items-center rounded-button border border-slate-200 px-4 text-sm font-extrabold text-primary-950 transition hover:bg-primary-50" data-variant-select data-variant-id="<?= e((string) $variant['id']) ?>">View variant</button>
                        </div>
                        <div><?php component('admin/components/common/badge', ['label' => $variantActive ? 'Active' : 'Inactive', 'variant' => $variantActive ? 'success' : 'gray']); ?></div>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($variants)): ?>
                    <p class="rounded-button border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">No variants added for this product yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="grid gap-5 xl:grid-cols-12">
        <div class="space-y-5 xl:col-span-5">
            <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-primary-950">Categories</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach ($categoryNames as $categoryName): ?>
                        <?php component('admin/components/common/badge', ['label' => $categoryName, 'variant' => 'primary']); ?>
                    <?php endforeach; ?>
                    <?php if (!$categoryNames): ?>
                        <span class="text-sm text-slate-500">No categories assigned.</span>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card xl:col-span-7">
                <h2 class="text-lg font-extrabold text-primary-950">Performance Summary</h2>
                <dl class="mt-5 border-t border-slate-200">
                    <?php $row('Total Views', e((string) (int) ($stats['views_count'] ?? 0))); ?>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                        <dt class="text-sm font-medium text-slate-500">Total Likes</dt>
                        <dd class="min-w-0 text-sm font-bold text-slate-900" data-summary-likes><?= e((string) $likesCount) ?></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                        <dt class="text-sm font-medium text-slate-500">Total Reviews</dt>
                        <dd class="min-w-0 text-sm font-bold text-slate-900" data-summary-reviews><?= e((string) $reviewsCount) ?></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                        <dt class="text-sm font-medium text-slate-500">Average Rating</dt>
                        <dd class="min-w-0 text-sm font-bold text-slate-900"><span class="text-amber-500" data-summary-stars><?= $ratingStars($averageRating) ?></span> <span data-summary-rating><?= e(number_format($averageRating, 1) . '/5') ?></span></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                        <dt class="text-sm font-medium text-slate-500">Total Orders</dt>
                        <dd class="min-w-0 text-sm font-bold text-slate-900" data-summary-orders><?= e((string) $orderCount) ?></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 border-b border-slate-200/80 py-3">
                        <dt class="text-sm font-medium text-slate-500">Total Sales</dt>
                        <dd class="min-w-0 text-sm font-bold text-slate-900" data-summary-sales><?= e($money($salesTotal)) ?></dd>
                    </div>
                    <div class="grid grid-cols-[minmax(8rem,14rem)_1fr] items-center gap-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Total Profit</dt>
                        <dd class="min-w-0 text-sm font-bold text-success-700" data-summary-profit><?= e($money($profit)) ?></dd>
                    </div>
                </dl>
        </section>
    </div>

    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-extrabold text-primary-950">Recent Orders <span class="text-sm font-bold text-slate-500">(Last 5)</span></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-white text-xs font-extrabold text-primary-950">
                    <tr>
                        <th class="px-5 py-4">Order ID</th>
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Quantity</th>
                        <th class="px-5 py-4">Total</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" data-orders-body>
                    <?php foreach ($recentOrders as $order): ?>
                        <?php
                            $orderStatus = (string) ($order['order_status'] ?? 'pending');
                            $orderVariant = in_array($orderStatus, ['delivered', 'completed'], true) ? 'success' : (in_array($orderStatus, ['cancelled', 'failed'], true) ? 'danger' : 'info');
                        ?>
                        <tr class="transition hover:bg-slate-50" data-order-row data-order-variant-id="<?= e((string) (int) ($order['variant_id'] ?? 0)) ?>">
                            <td class="px-5 py-4 font-bold text-primary-950"><a href="<?= e(url('/admin/orders/' . $order['id'])) ?>"><?= e($order['order_number']) ?></a></td>
                            <td class="px-5 py-4 font-semibold text-slate-700"><?= e($order['customer_name']) ?></td>
                            <td class="px-5 py-4 font-semibold text-slate-700"><?= (int) $order['quantity'] ?></td>
                            <td class="px-5 py-4 font-bold text-primary-950"><?= $money($order['total_price']) ?></td>
                            <td class="px-5 py-4"><?php component('admin/components/common/badge', ['label' => $label($orderStatus), 'variant' => $orderVariant]); ?></td>
                            <td class="px-5 py-4 font-semibold text-slate-700"><?= e(date('M d, Y', strtotime($order['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentOrders): ?>
                        <tr data-order-empty><td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">No orders include this product yet.</td></tr>
                    <?php endif; ?>
                    <tr class="hidden" data-order-filter-empty><td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">No recent orders for this variant yet.</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-extrabold text-primary-950">Customer Reviews <span class="text-sm font-bold text-slate-500">(Latest 3)</span></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-white text-xs font-extrabold text-primary-950">
                    <tr>
                        <th class="px-5 py-4">Customer</th>
                        <th class="px-5 py-4">Rating</th>
                        <th class="px-5 py-4">Review</th>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($latestReviews as $review): ?>
                        <?php
                            $reviewer = trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?: 'Customer';
                            $reviewStatus = (string) ($review['status'] ?? 'pending');
                            $reviewVariant = $reviewStatus === 'approved' ? 'success' : ($reviewStatus === 'rejected' ? 'danger' : 'warning');
                        ?>
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-extrabold text-primary-900"><?= e(strtoupper(substr($reviewer, 0, 1))) ?></span>
                                    <span class="font-bold text-primary-950"><?= e($reviewer) ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-bold text-amber-500"><?= $ratingStars((float) $review['rating']) ?></td>
                            <td class="max-w-xl px-5 py-4 text-slate-600"><?= e($review['body'] ?: $review['title'] ?: 'No review text.') ?></td>
                            <td class="px-5 py-4 font-semibold text-slate-700"><?= e(date('M d, Y', strtotime($review['created_at']))) ?></td>
                            <td class="px-5 py-4"><?php component('admin/components/common/badge', ['label' => $label($reviewStatus), 'variant' => $reviewVariant]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$latestReviews): ?>
                        <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">No reviews for this product yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <script>
        (() => {
            const root = document.querySelector('[data-product-show]');
            if (!root) return;

            const variants = <?= json_encode($variantPayloads, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const byId = new Map(variants.map((variant) => [String(variant.id), variant]));
            const strip = root.querySelector('[data-variant-strip]');
            const mainImage = root.querySelector('[data-product-main-image]');
            const mainImageLink = root.querySelector('[data-product-main-image-link]');
            const orderRows = Array.from(root.querySelectorAll('[data-order-row]'));
            const emptyFilteredOrders = root.querySelector('[data-order-filter-empty]');

            const text = (selector, value) => {
                const node = root.querySelector(selector);
                if (node) node.textContent = value;
            };

            const html = (selector, value) => {
                const node = root.querySelector(selector);
                if (node) node.innerHTML = value;
            };

            const selectVariant = (id) => {
                const selected = byId.get(String(id)) || byId.get('0');
                if (!selected) return;

                if (mainImage) {
                    mainImage.src = selected.image;
                    mainImage.alt = selected.name;
                }
                if (mainImageLink) {
                    mainImageLink.href = selected.image;
                }

                text('[data-product-selected-rating]', selected.rating);
                text('[data-product-selected-reviews]', selected.reviews);
                text('[data-product-selected-orders]', selected.orders);
                text('[data-product-selected-sales]', selected.sales);
                text('[data-product-selected-stock]', selected.stock);

                text('[data-product-info-name]', selected.name);
                text('[data-product-info-sku]', selected.sku);
                text('[data-product-info-price]', selected.price);
                text('[data-product-info-cost]', selected.cost);
                html('[data-product-info-stock]', `${selected.stockLabel}`);

                const status = root.querySelector('[data-product-info-status]');
                if (status) {
                    status.textContent = selected.status;
                    status.className = `inline-flex rounded-button border px-2.5 py-1 text-xs font-semibold ${selected.statusClass}`;
                }

                text('[data-summary-likes]', selected.likes);
                text('[data-summary-reviews]', selected.reviews);
                html('[data-summary-stars]', selected.stars);
                text('[data-summary-rating]', `${selected.rating}/5`);
                text('[data-summary-orders]', selected.orders);
                text('[data-summary-sales]', selected.sales);
                text('[data-summary-profit]', selected.profit);

                root.querySelectorAll('[data-variant-select]').forEach((button) => {
                    const active = button.dataset.variantId === String(selected.id);
                    button.classList.toggle('border-primary-500', active);
                    button.classList.toggle('ring-2', active);
                    button.classList.toggle('ring-primary-100', active);
                    button.classList.toggle('border-slate-200', !active);
                });

                let visibleOrders = 0;
                orderRows.forEach((row) => {
                    const visible = selected.id === 0 || row.dataset.orderVariantId === String(selected.id);
                    row.classList.toggle('hidden', !visible);
                    if (visible) visibleOrders += 1;
                });
                if (emptyFilteredOrders) {
                    emptyFilteredOrders.classList.toggle('hidden', selected.id === 0 || visibleOrders > 0);
                }
            };

            root.querySelectorAll('[data-variant-select]').forEach((button) => {
                button.addEventListener('click', () => selectVariant(button.dataset.variantId || '0'));
            });

            root.querySelector('[data-variant-scroll-left]')?.addEventListener('click', () => {
                strip?.scrollBy({ left: -220, behavior: 'smooth' });
            });

            root.querySelector('[data-variant-scroll-right]')?.addEventListener('click', () => {
                strip?.scrollBy({ left: 220, behavior: 'smooth' });
            });
        })();
    </script>
</div>
