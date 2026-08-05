<?php
$products = array_slice($products ?? [], 0, 4);
if (!$products) return;
?>
<section class="bg-white ">
    <div class="mx-auto max-w-7xl">
        <div class="grid items-stretch gap-5 md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.5fr)] lg:gap-7">
            <a href="/shop" class="group relative min-h-[430px] overflow-hidden rounded-3xl bg-secondary md:min-h-full">
                <img src="/assets/images/product-showcase-giftvibe.png" alt="GiftVibe premium gift collection" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-secondary/90 via-secondary/45 to-black/65"></div>
                <div class="relative flex h-full min-h-[430px] flex-col items-start p-7 text-white sm:p-9 md:min-h-full lg:p-10">
                    <span class="inline-flex rounded-md bg-rose-600 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white shadow-lg shadow-black/15">Made to delight</span>
                    <h2 class="mt-4 max-w-sm text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Popular gifts for every special moment</h2>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-white/75">Discover thoughtful favourites, beautifully curated for birthdays, celebrations and meaningful surprises.</p>
                    <span class="mt-auto inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-secondary shadow-lg transition group-hover:bg-primary group-hover:text-white">Show all products <span aria-hidden="true">&rarr;</span></span>
                </div>
            </a>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($products as $product) require BASE_PATH . '/resources/views/components/base/product-card.php'; ?>
            </div>
        </div>
    </div>
</section>
