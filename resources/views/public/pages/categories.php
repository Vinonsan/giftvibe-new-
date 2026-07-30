<?php
$imageUrl = static function (?string $path): string {
    if (!$path) {
        return asset('/public/assets/images/hero_gift_box.jpg');
    }
    return str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset('/' . ltrim($path, '/'));
};
?>
<section class="bg-slate-950 py-14 text-white">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-wider text-primary-200">Gift collections</p>
        <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">Shop by occasion, mood, and gift style.</h1>
        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-200">Fast category browsing for an ecommerce gifting experience: open a collection, compare products, and move straight to cart.</p>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach (($categories ?? []) as $category): ?>
                <a href="<?= e(url('/category/' . $category['slug'])) ?>" class="group overflow-hidden rounded-card border border-slate-200 bg-white shadow-card transition hover:-translate-y-0.5 hover:border-primary-200">
                    <div class="relative">
                        <img src="<?= e($imageUrl($category['image_path'] ?? null)) ?>" alt="<?= e($category['name']) ?> gift category" class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
                        <span class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-primary shadow-sm"><?= (int) ($category['products_count'] ?? 0) ?> products</span>
                    </div>
                    <div class="p-5">
                        <h2 class="text-xl font-extrabold text-slate-950"><?= e($category['name']) ?></h2>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500"><?= e($category['description'] ?: 'Published GiftVibe.lk products curated for this category.') ?></p>
                        <span class="mt-4 inline-flex text-sm font-bold text-primary">Open collection</span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?><div class="rounded-card border border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500 sm:col-span-2 lg:col-span-3">Categories will appear after the admin adds active collections.</div><?php endif; ?>
        </div>
    </div>
</section>
