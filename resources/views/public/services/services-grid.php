<?php
declare(strict_types=1);

$services = [
    [
        'icon'       => 'heroicons:gift-solid',
        'iconBg'     => 'bg-pink-50 text-primary',
        'title'      => 'Personalised Gifting',
        'description' => 'Tell us the recipient, occasion, and budget — we curate a thoughtful gift with a personal message written by hand on a premium card.',
    ],
    [
        'icon'       => 'heroicons:cube-solid',
        'iconBg'     => 'bg-violet-50 text-violet-600',
        'title'      => 'Curated Gift Boxes',
        'description' => 'Ready-to-gift collections for birthdays, anniversaries, congratulations, and every meaningful surprise — beautifully assembled and wrapped.',
    ],
    [
        'icon'       => 'heroicons:sparkles-solid',
        'iconBg'     => 'bg-amber-50 text-amber-600',
        'title'      => 'Flowers &amp; Sweet Treats',
        'description' => 'Fresh bouquets, chocolates, and celebration treats prepared for memorable moments, paired to match the mood of your occasion.',
    ],
    [
        'icon'       => 'heroicons:shopping-bag-solid',
        'iconBg'     => 'bg-blue-50 text-blue-600',
        'title'      => 'Gift Combo Delivery',
        'description' => 'Products thoughtfully paired into convenient celebration combos and delivered together in one elegant package.',
    ],
    [
        'icon'       => 'heroicons:building-office-2-solid',
        'iconBg'     => 'bg-emerald-50 text-emerald-600',
        'title'      => 'Corporate Gifting',
        'description' => 'Practical gifting support for teams, clients, events, and seasonal appreciation — with custom branding and bulk order handling.',
    ],
    [
        'icon'       => 'heroicons:chat-bubble-left-ellipsis-solid',
        'iconBg'     => 'bg-rose-50 text-rose-600',
        'title'      => 'Gifting Guidance',
        'description' => 'Not sure what to give? Our friendly team helps you choose the right gift, theme, and packaging that suits your recipient best.',
    ],
];
?>
<section class="mx-auto max-w-7xl px-6 py-16 sm:py-20 lg:px-8">
    <div class="mx-auto max-w-3xl text-center">
        <span class="inline-flex items-center rounded-full bg-white px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-primary shadow-sm ring-1 ring-slate-100">
            What We Offer
        </span>
        <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl">Services Designed Around Your Occasion</h2>
        <p class="mt-4 text-base leading-7 text-slate-500">
            Whether it is a birthday surprise, a wedding hamper, or a corporate appreciation gift — we make gifting effortless from start to doorstep.
        </p>
    </div>

    <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($services as $service): ?>
            <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-7 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-gradient-to-br from-primary/10 to-transparent blur-2xl transition-opacity opacity-0 group-hover:opacity-100" aria-hidden="true"></div>
                <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl <?= $service['iconBg'] ?> transition-transform duration-300 group-hover:scale-110">
                    <iconify-icon icon="<?= $service['icon'] ?>" class="text-2xl"></iconify-icon>
                </div>
                <h3 class="mt-5 text-lg font-bold text-secondary"><?= $service['title'] ?></h3>
                <p class="mt-2 text-sm leading-6 text-slate-500"><?= $service['description'] ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
