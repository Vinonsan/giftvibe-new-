<?php $products = $products ?? []; if (!$products) return; ?>
<section class="bg-slate-50 py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <?php
        $animatedHeadingEyebrow = 'Made to delight'; $animatedHeadingTitle = 'Popular gifts';
        $animatedHeadingDescription = 'Customer favourites for birthdays, celebrations and thoughtful surprises.';
        require BASE_PATH . '/resources/views/components/base/animated-heading.php';
        ?>
        <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            <?php foreach ($products as $product) require BASE_PATH . '/resources/views/components/base/product-card.php'; ?>
        </div>
        <div class="mt-10 text-center"><a href="/shop" class="inline-flex rounded-full bg-secondary px-6 py-3 text-sm font-bold text-white transition hover:bg-primary">View all products</a></div>
    </div>
</section>
