<?php
declare(strict_types=1);
$coverage = [
    ['city' => 'Colombo', 'zone' => 'Metro &amp; Suburbs', 'eta' => 'Same-day'],
    ['city' => 'Jaffna', 'zone' => 'Northern Province', 'eta' => 'Express'],
    ['city' => 'Kandy', 'zone' => 'Central Province', 'eta' => 'Next-day'],
    ['city' => 'Galle', 'zone' => 'Southern Province', 'eta' => 'Next-day'],
];
$benefits = [
    ['icon' => 'heroicons:shield-check-solid', 'title' => 'Fresh &amp; Quality Assured', 'copy' => 'Every flower, chocolate, and keepsake is quality-checked before it is packed and dispatched.'],
    ['icon' => 'heroicons:clock-solid', 'title' => 'On-Time Surprises', 'copy' => 'We coordinate delivery windows carefully so your gift arrives right when it matters.'],
    ['icon' => 'heroicons:heart-solid', 'title' => 'Hand-Crafted Personal Touch', 'copy' => 'Handwritten cards, custom ribbons, and made-to-order themes on every single order.'],
    ['icon' => 'heroicons:phone-solid', 'title' => 'Friendly Support', 'copy' => 'Real humans on WhatsApp and phone to help you plan, adjust, and track your gift.'],
];
?>
<section class="mx-auto max-w-7xl px-6 py-16 sm:py-20 lg:px-8">
    <div class="grid grid-cols-1 gap-14 lg:grid-cols-2 lg:items-center">
        <!-- SEO-rich copy -->
        <div>
            <span class="inline-flex items-center rounded-full bg-white px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-primary shadow-sm ring-1 ring-slate-100">
                Why GiftVibe
            </span>
            <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl">
                Premium Gift Hampers &amp; Same-Day Flower Delivery in Sri Lanka
            </h2>
            <div class="mt-6 space-y-4 text-sm leading-7 text-slate-500 sm:text-base">
                <p>
                    GiftVibe is Sri Lanka&rsquo;s trusted destination for <strong class="text-secondary">custom gift hampers</strong>, birthday gift boxes,
                    anniversary combos, and <strong class="text-secondary">same-day flower delivery in Colombo</strong>. We combine premium local products,
                    fresh blooms, and thoughtful packaging to turn every present into an experience.
                </p>
                <p>
                    Our services cover everything from <strong class="text-secondary">corporate gifting</strong> and bulk seasonal hampers to intimate
                    personal surprises. With express delivery across Colombo, Jaffna, Kandy, and Galle — plus islandwide courier coverage —
                    we make sure your thoughtfulness always arrives on time.
                </p>
                <p>
                    Need help choosing? Our gifting experts guide you through themes, budgets, and recipient preferences so you can
                    order with complete confidence — wherever you are in the world.
                </p>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <?php foreach ($benefits as $benefit): ?>
                    <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <iconify-icon icon="<?= $benefit['icon'] ?>" class="text-xl"></iconify-icon>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-secondary"><?= $benefit['title'] ?></h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500"><?= $benefit['copy'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Delivery coverage card -->
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-lg sm:p-10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-secondary">Delivery Coverage</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                    <iconify-icon icon="heroicons:truck-solid" class="text-sm"></iconify-icon>
                    Islandwide
                </span>
            </div>
            <p class="mt-2 text-sm text-slate-500">Express routes to major cities, with islandwide courier service for every order.</p>

            <div class="mt-7 space-y-3">
                <?php foreach ($coverage as $area): ?>
                    <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50/60 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary shadow-sm ring-1 ring-slate-100">
                                <iconify-icon icon="heroicons:map-pin-solid" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-secondary"><?= $area['city'] ?></p>
                                <p class="text-xs text-slate-500"><?= $area['zone'] ?></p>
                            </div>
                        </div>
                        <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary"><?= $area['eta'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-7 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 p-6 text-white">
                <div class="flex items-start gap-4">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/20 text-primary">
                        <iconify-icon icon="heroicons:globe-alt-solid" class="text-2xl"></iconify-icon>
                    </span>
                    <div>
                        <h4 class="text-base font-bold">Ordering from abroad?</h4>
                        <p class="mt-1 text-xs leading-5 text-slate-300">Send gifts to loved ones in Sri Lanka from anywhere in the world. We handle curation, payment, and delivery on your behalf.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
