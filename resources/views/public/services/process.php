<?php
declare(strict_types=1);
$steps = [
    [
        'number' => '01',
        'color'  => 'border-primary text-primary',
        'title'  => 'Tell Us Your Occasion',
        'description' => 'Share the recipient, occasion, and budget — online or over WhatsApp. We listen carefully to what matters most.',
    ],
    [
        'number' => '02',
        'color'  => 'border-violet-500 text-violet-500',
        'title'  => 'We Curate &amp; Personalise',
        'description' => 'Our team selects premium items, flowers, chocolates, and keepsakes matched perfectly to your theme and taste.',
    ],
    [
        'number' => '03',
        'color'  => 'border-blue-500 text-blue-500',
        'title'  => 'Elegant Packaging &amp; Message',
        'description' => 'Every gift is wrapped in premium materials with ribbons and tissue, and your personal message is handwritten on a quality card.',
    ],
    [
        'number' => '04',
        'color'  => 'border-emerald-500 text-emerald-500',
        'title'  => 'Surprise Delivery',
        'description' => 'Our couriers deliver to the recipient&rsquo;s doorstep across Colombo, Jaffna, and islandwide — timed for the perfect surprise.',
    ],
];
?>
<section class="bg-slate-50/60 py-16 sm:py-20 border-y border-slate-100">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center rounded-full bg-white px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-primary shadow-sm ring-1 ring-slate-100">
                How We Serve You
            </span>
            <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl">From Your Idea to Their Doorstep</h2>
            <p class="mt-4 text-base text-slate-500">A simple, transparent process designed to make every gift feel effortless and unforgettable.</p>
        </div>

        <div class="relative mt-12 grid grid-cols-1 gap-8 md:grid-cols-4">
            <div class="absolute left-10 right-10 top-7 z-0 hidden h-0.5 bg-slate-200 md:block" aria-hidden="true"></div>
            <?php foreach ($steps as $step): ?>
                <div class="relative z-10 flex flex-col items-center text-center md:items-start md:text-left">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl border-2 bg-white font-black shadow-sm <?= $step['color'] ?>"><?= $step['number'] ?></span>
                    <h3 class="mt-4 text-base font-bold text-secondary"><?= $step['title'] ?></h3>
                    <p class="mt-2 text-xs leading-5 text-slate-500"><?= $step['description'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
