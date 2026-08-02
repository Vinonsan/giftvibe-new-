<?php declare(strict_types=1); ?>
<section class="mx-auto max-w-7xl">
    <?php $animatedHeadingEyebrow = 'GiftVibe shop'; $animatedHeadingTitle = $activeName; $animatedHeadingDescription = 'Choose a category and discover the perfect gift.'; require BASE_PATH . '/resources/views/components/base/animated-heading.php'; ?>
    <nav class="mt-8 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none]" aria-label="Product categories">
        <a href="/shop" class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold <?= $categorySlug === '' ? 'bg-primary text-white' : 'border border-slate-200 bg-white text-secondary hover:border-primary' ?>">All gifts</a>
        <?php foreach ($categories as $category): ?><a href="/shop?category=<?= rawurlencode($category['slug']) ?>" class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold <?= $categorySlug === $category['slug'] ? 'bg-primary text-white' : 'border border-slate-200 bg-white text-secondary hover:border-primary' ?>"><?= htmlspecialchars($category['name']) ?></a><?php endforeach; ?>
    </nav>
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        <?php foreach ($products as $product) require BASE_PATH . '/resources/views/components/base/product-card.php'; ?>
    </div>
    <?php if (!$products): ?><div class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center text-sm text-slate-500">No products are available in this category yet.</div><?php endif; ?>
</section>
