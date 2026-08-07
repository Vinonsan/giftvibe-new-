<?php
declare(strict_types=1);
/**
 * Public category single page.
 *
 * @var array|null $category
 * @var array      $products
 * @var string     $categoryName
 * @var string     $slug
 */

$catImage = (string) ($category['image_path'] ?? '/assets/images/hero_slide_1.jpg');
if (str_starts_with($catImage, 'public/')) $catImage = '/' . substr($catImage, 7);
$catDesc = trim((string) ($category['description'] ?? ''));
?>

<!-- ── Breadcrumb ─────────────────────────────────────────────────── -->
<nav class="border-b border-slate-100 bg-white" aria-label="Breadcrumb">
    <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
        <ol class="flex items-center gap-2 text-sm text-slate-500">
            <li><a href="/" class="hover:text-primary transition">Home</a></li>
            <li><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></li>
            <li><a href="/shop" class="hover:text-primary transition">Shop</a></li>
            <li><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></li>
            <li class="font-semibold text-secondary" aria-current="page"><?= htmlspecialchars($categoryName) ?></li>
        </ol>
    </div>
</nav>

<!-- ── Category hero banner ───────────────────────────────────────── -->
<div class="relative h-52 overflow-hidden sm:h-64 lg:h-72">
    <img src="<?= htmlspecialchars($catImage) ?>"
         alt="<?= htmlspecialchars($categoryName) ?>"
         class="h-full w-full object-cover"
         onerror="this.src='/assets/images/hero_slide_1.jpg'">
    <div class="absolute inset-0 bg-gradient-to-t from-secondary/80 via-secondary/30 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 px-4 pb-7 sm:px-8">
        <div class="mx-auto max-w-7xl">
            <h1 class="text-2xl font-black text-white sm:text-3xl lg:text-4xl drop-shadow-sm">
                <?= htmlspecialchars($categoryName) ?>
            </h1>
            <?php if ($catDesc !== ''): ?>
                <p class="mt-1.5 text-sm text-white/80 max-w-xl"><?= htmlspecialchars($catDesc) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Product grid ───────────────────────────────────────────────── -->
<section class="bg-slate-50 py-10" aria-labelledby="cat-products-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Count bar -->
        <div class="mb-6 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                <span class="font-bold text-secondary"><?= count($products) ?></span>
                <?= count($products) === 1 ? 'gift' : 'gifts' ?> in <span class="font-semibold text-primary"><?= htmlspecialchars($categoryName) ?></span>
            </p>
            <a href="/shop" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                All products
            </a>
        </div>

        <?php if (!empty($products)): ?>
            <div id="product-grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <?php foreach ($products as $product):
                    require BASE_PATH . '/resources/views/components/base/product-card.php';
                endforeach; ?>
            </div>
        <?php else: ?>
            <div class="rounded-3xl border border-dashed border-slate-200 bg-white py-20 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <p class="text-sm font-semibold text-slate-500">No products in this category yet.</p>
                <a href="/shop" class="mt-3 inline-block text-sm font-bold text-primary hover:underline">Browse all gifts</a>
            </div>
        <?php endif; ?>

    </div>
</section>
