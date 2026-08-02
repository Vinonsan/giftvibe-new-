<?php
declare(strict_types=1);
$product = $product ?? [];
$productName = (string) ($product['name'] ?? 'Gift');
$productImage = (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg');
if (str_starts_with($productImage, 'public/')) $productImage = '/' . substr($productImage, 7);
if ($productImage !== '' && !str_starts_with($productImage, '/') && !str_starts_with($productImage, 'http')) $productImage = '/' . $productImage;
$basePrice = (float) ($product['base_price'] ?? 0);
$salePrice = isset($product['sale_price']) && $product['sale_price'] !== null ? (float) $product['sale_price'] : null;
$hasSale = $salePrice !== null && $salePrice > 0 && $salePrice < $basePrice;
$productHref = (string) ($productHref ?? ('/shop?product=' . rawurlencode((string) ($product['slug'] ?? ''))));
?>
<article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-primary/25 hover:shadow-xl">
    <a href="<?= htmlspecialchars($productHref) ?>" class="relative block aspect-square overflow-hidden bg-slate-100">
        <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($productName) ?>" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
        <?php if ($hasSale): ?><span class="absolute left-3 top-3 rounded-full bg-rose-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Sale</span><?php endif; ?>
        <?php if (!empty($product['is_featured'])): ?><span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-bold text-primary shadow-sm">Featured</span><?php endif; ?>
    </a>
    <div class="p-4">
        <p class="truncate text-xs font-semibold uppercase tracking-wider text-primary/65"><?= htmlspecialchars((string) ($product['category_names'] ?? 'GiftVibe collection')) ?></p>
        <a href="<?= htmlspecialchars($productHref) ?>" class="mt-1.5 block line-clamp-2 min-h-12 font-bold leading-6 text-secondary transition hover:text-primary"><?= htmlspecialchars($productName) ?></a>
        <div class="mt-3 flex items-end justify-between gap-3">
            <div><?php if ($hasSale): ?><span class="block text-xs text-slate-400 line-through">LKR <?= number_format($basePrice, 2) ?></span><?php endif; ?><span class="font-extrabold text-secondary">LKR <?= number_format($hasSale ? $salePrice : $basePrice, 2) ?></span></div>
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white transition group-hover:bg-secondary" aria-hidden="true">+</span>
        </div>
    </div>
</article>
