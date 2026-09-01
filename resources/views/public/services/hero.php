<?php
declare(strict_types=1);
?>
<section class="relative overflow-hidden bg-slate-900 px-6 py-24 sm:py-32 lg:px-8">
    <!-- Glow Backdrop Effects -->
    <div class="absolute -top-24 left-1/2 -z-10 -translate-x-1/2 blur-3xl sm:-top-80" aria-hidden="true">
        <div class="aspect-1097/845 w-[68.5625rem] bg-gradient-to-tr from-primary to-violet-600 opacity-20"
             style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <div class="relative mx-auto max-w-3xl text-center">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary ring-1 ring-primary/20">
            <iconify-icon icon="heroicons:sparkles-solid" class="text-sm"></iconify-icon>
            Premium Gifting Services
        </span>
        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-white sm:text-6xl">
            Gifting Made <span class="text-primary font-black">Personal</span> &amp; Effortless
        </h1>
        <p class="mt-6 text-lg leading-8 text-slate-300">
            From custom gift hampers and fresh flower bouquets to corporate gifting and same-day islandwide delivery — GiftVibe designs, curates, and hand-delivers thoughtful presents across Colombo &amp; Sri Lanka.
        </p>
        <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
            <a href="<?= app_url('/shop') ?>" class="inline-flex items-center gap-2 rounded-xl bg-primary px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/25 transition hover:bg-secondary">
                Explore Gifts
                <iconify-icon icon="heroicons:arrow-right-solid" class="text-base"></iconify-icon>
            </a>
            <a href="<?= app_url('/contact') ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-7 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10">
                Discuss Your Gift
            </a>
        </div>
        <div class="mt-12 grid grid-cols-3 gap-6 border-t border-white/10 pt-8">
            <div>
                <span class="block text-2xl font-black text-white sm:text-3xl">25+</span>
                <span class="mt-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Gift Categories</span>
            </div>
            <div>
                <span class="block text-2xl font-black text-white sm:text-3xl">Same-Day</span>
                <span class="mt-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Islandwide Delivery</span>
            </div>
            <div>
                <span class="block text-2xl font-black text-white sm:text-3xl">100%</span>
                <span class="mt-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">Hand-Crafted Care</span>
            </div>
        </div>
    </div>
</section>
