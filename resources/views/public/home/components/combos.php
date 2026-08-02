<?php
$combos = array_slice($combos ?? [], 0, 4);
if (!$combos) return;
?>
<section class="bg-white">
    <div class="mx-auto max-w-7xl py-12">
        <div class="grid items-stretch gap-5 md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.5fr)] lg:gap-7">
            <a href="/combos" class="group relative min-h-[430px] overflow-hidden rounded-3xl bg-secondary md:min-h-full">
                <img src="/assets/images/combo-showcase-giftvibe.png" alt="GiftVibe curated gift combos" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="absolute inset-0 bg-linear-to-b from-secondary/90 via-secondary/45 to-black/65"></div>
                <div class="relative flex h-full min-h-[430px] flex-col items-start p-7 text-white sm:p-9 md:min-h-full lg:p-10">
                    <span class="inline-flex rounded-md bg-rose-600 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white shadow-lg shadow-black/15">More joy together</span>
                    <h2 class="mt-4 max-w-sm text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Curated combos for unforgettable celebrations</h2>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-white/75">Beautiful products paired together as one thoughtful, ready-to-gift collection.</p>
                    <span class="mt-auto inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-secondary shadow-lg transition group-hover:bg-primary group-hover:text-white">Show all combos <span aria-hidden="true">&rarr;</span></span>
                </div>
            </a>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($combos as $combo) require BASE_PATH . '/resources/views/components/base/combo-card.php'; ?>
            </div>
        </div>
    </div>
</section>
