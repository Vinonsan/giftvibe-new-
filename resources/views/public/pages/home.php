<?php
$deliveredTrust = $deliveredTrust ?? ['deliveredOrders' => 0, 'deliveredCities' => 0, 'items' => []];
$heroProduct = $featuredProducts[0] ?? $latestProducts[0] ?? null;
$heroImage = asset('/public/assets/images/hero_gift_box.jpg');
if ($heroProduct && !empty($heroProduct['primary_image'])) {
    $heroImage = str_starts_with($heroProduct['primary_image'], 'http') ? $heroProduct['primary_image'] : asset('/' . ltrim($heroProduct['primary_image'], '/'));
}
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
?>
<section class="relative overflow-hidden bg-slate-950 text-white">
    <img src="<?= e($heroImage) ?>" alt="GiftVibe.lk featured gift collection" class="absolute inset-0 h-full w-full object-cover opacity-45">
    <div class="absolute inset-0 bg-slate-950/70"></div>
    <div class="relative mx-auto grid min-h-[620px] max-w-container grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-12 lg:px-8 lg:py-16">
        <div class="self-center lg:col-span-7">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-200">GiftVibe.lk 2026 collection</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">Shop curated gifts, flowers, hampers, and custom surprises.</h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200">A modern Sri Lankan gifting store with published products, secure checkout, custom requests, customer reviews, and delivery tracking.</p>
            <form method="GET" action="<?= e(url('/search')) ?>" class="mt-8 flex max-w-2xl flex-col gap-3 rounded-card border border-white/15 bg-white/10 p-2 backdrop-blur sm:flex-row">
                <input name="q" type="search" class="min-w-0 flex-1 rounded-button border border-white/20 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="Search birthday boxes, roses, chocolates...">
                <button class="rounded-button bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-primary-700">Search gifts</button>
            </form>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="<?= e(url('/shop')) ?>" class="rounded-button bg-white px-5 py-3 text-sm font-bold text-slate-950">Shop now</a>
                <a href="<?= e(url('/shop?offers=1')) ?>" class="rounded-button border border-white/25 px-5 py-3 text-sm font-bold text-white hover:bg-white/10">Today&apos;s offers</a>
                <a href="<?= e(url('/custom-gifts')) ?>" class="rounded-button border border-white/25 px-5 py-3 text-sm font-bold text-white hover:bg-white/10">Custom gift</a>
            </div>
        </div>
        <aside class="self-end lg:col-span-5">
            <div class="rounded-card border border-white/15 bg-white/10 p-4 backdrop-blur">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-button bg-white/10 p-3"><strong class="block text-2xl"><?= count($categories ?? []) ?></strong><span class="text-xs text-slate-200">Categories</span></div>
                    <div class="rounded-button bg-white/10 p-3"><strong class="block text-2xl"><?= count($featuredProducts ?? []) ?></strong><span class="text-xs text-slate-200">Featured</span></div>
                    <div class="rounded-button bg-white/10 p-3"><strong class="block text-2xl"><?= number_format((int) $deliveredTrust['deliveredOrders']) ?></strong><span class="text-xs text-slate-200">Delivered</span></div>
                </div>
                <?php if ($heroProduct): ?>
                    <a href="<?= e(url('/product/' . $heroProduct['slug'])) ?>" class="mt-4 block rounded-button bg-white p-4 text-slate-950">
                        <span class="text-xs font-bold uppercase tracking-wider text-primary">Featured gift</span>
                        <strong class="mt-1 block text-lg"><?= e($heroProduct['name']) ?></strong>
                        <span class="mt-1 block text-sm text-slate-500"><?= e($money($heroProduct['sale_price'] ?: $heroProduct['base_price'])) ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>

<section class="border-y border-slate-200 bg-white py-8">
    <div class="mx-auto flex max-w-container gap-3 overflow-x-auto px-4 pb-1 sm:px-6 lg:px-8">
        <a href="<?= e(url('/shop')) ?>" class="shrink-0 rounded-full bg-slate-950 px-4 py-2 text-sm font-bold text-white">All gifts</a>
        <?php foreach (($categories ?? []) as $category): ?>
            <a href="<?= e(url('/category/' . $category['slug'])) ?>" class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700 hover:border-primary-200 hover:text-primary"><?= e($category['name']) ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Limited offers</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Deals worth opening today</h2>
            </div>
            <a href="<?= e(url('/shop?offers=1')) ?>" class="text-sm font-bold text-primary hover:text-primary-800">View all</a>
        </div>
        <div class="flex gap-5 overflow-x-auto pb-3">
            <?php foreach (($offerProducts ?? []) as $product): ?>
                <div class="w-72 shrink-0"><?php component('public/partials/product-grid', ['products' => [$product]]); ?></div>
            <?php endforeach; ?>
            <?php if (empty($offerProducts)): ?><div class="rounded-card border border-slate-200 bg-white px-5 py-6 text-sm text-slate-500">No published offers yet.</div><?php endif; ?>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Featured collection</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Popular picks customers notice first</h2>
            </div>
            <a href="<?= e(url('/shop?featured=1')) ?>" class="text-sm font-bold text-primary hover:text-primary-800">View featured</a>
        </div>
        <?php component('public/partials/product-grid', ['products' => $featuredProducts ?? []]); ?>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="mx-auto grid max-w-container gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-4">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Shop by category</p>
            <h2 class="mt-2 text-2xl font-extrabold text-slate-950">Find the right gift faster</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Browse by occasion, style, and recipient. Each collection is built for fast ecommerce scanning and confident checkout.</p>
            <a href="<?= e(url('/categories')) ?>" class="mt-5 inline-flex rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">All categories</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:col-span-8">
            <?php foreach (($categories ?? []) as $category): ?>
                <a href="<?= e(url('/category/' . $category['slug'])) ?>" class="rounded-card border border-slate-200 bg-white p-5 shadow-sm transition hover:border-primary-200 hover:shadow-card">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary"><?= (int) ($category['products_count'] ?? 0) ?> published</span>
                    <h3 class="mt-2 text-lg font-extrabold text-slate-900"><?= e($category['name']) ?></h3>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-500"><?= e($category['description'] ?: 'Curated GiftVibe.lk products in this category.') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">New arrivals</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Fresh gifts in the shop</h2>
            </div>
            <a href="<?= e(url('/shop')) ?>" class="text-sm font-bold text-primary hover:text-primary-800">Open shop</a>
        </div>
        <?php component('public/partials/product-grid', ['products' => $latestProducts ?? []]); ?>
    </div>
</section>

<section class="bg-slate-950 py-12 text-white">
    <div class="mx-auto grid max-w-container gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-4">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-200">Delivered with care</p>
            <h2 class="mt-2 text-2xl font-extrabold">Real orders, real delivery confidence</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-card bg-white/10 p-4"><span class="text-xs font-bold uppercase tracking-wider text-slate-300">Delivered orders</span><strong class="mt-1 block text-3xl text-white"><?= number_format((int) $deliveredTrust['deliveredOrders']) ?></strong></div>
                <div class="rounded-card bg-white/10 p-4"><span class="text-xs font-bold uppercase tracking-wider text-slate-300">Delivery cities</span><strong class="mt-1 block text-3xl text-white"><?= number_format((int) $deliveredTrust['deliveredCities']) ?></strong></div>
            </div>
        </div>
        <div class="lg:col-span-8">
            <?php if (!empty($deliveredTrust['items'])): ?>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                    <?php foreach ($deliveredTrust['items'] as $item): ?>
                        <?php
                        $path = $item['image_path'] ?? 'public/assets/images/hero_gift_box.jpg';
                        $src = str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset('/' . ltrim($path, '/'));
                        ?>
                        <article class="overflow-hidden rounded-card bg-white text-slate-950">
                            <img src="<?= e($src) ?>" alt="<?= e($item['product_name']) ?> delivered gift image" class="aspect-square w-full object-cover">
                            <div class="p-3"><strong class="line-clamp-1 text-sm"><?= e($item['product_name']) ?></strong><p class="mt-1 text-xs text-slate-500"><?= e($item['delivery_city'] ?: 'Sri Lanka') ?> delivery</p></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="rounded-card border border-white/15 bg-white/10 p-6 text-sm text-slate-200">Delivered order highlights will appear here once orders are marked delivered.</div>
            <?php endif; ?>
        </div>
    </div>
</section>
