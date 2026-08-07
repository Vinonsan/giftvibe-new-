<?php declare(strict_types=1); ?>
<section class="mx-auto max-w-7xl py-12 sm:py-16">
    <div class="max-w-3xl"><p class="text-xs font-extrabold uppercase tracking-[.22em] text-primary">GiftVibe LK services</p><h1 class="mt-4 text-4xl font-black tracking-tight text-secondary sm:text-5xl"><?=htmlspecialchars($title)?></h1><p class="mt-5 text-base leading-8 text-slate-600"><?=htmlspecialchars($intro)?></p></div>
    <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ([['Personalised gifting','Share the recipient, occasion and budget. We help curate a thoughtful gift with a personal message.'],['Curated gift boxes','Ready-to-gift collections for birthdays, anniversaries, congratulations and meaningful surprises.'],['Flowers and sweet treats','Fresh bouquets, chocolates and celebration treats prepared for memorable moments.'],['Gift combo delivery','Products paired into convenient celebration combos and delivered together.'],['Corporate gifting','Practical gifting support for teams, clients, events and seasonal appreciation.'],['Gifting guidance','Friendly help choosing an appropriate gift when you are unsure what will suit the recipient.']] as [$heading,$copy]): ?>
        <article class="rounded-3xl border border-primary/10 bg-white p-7 shadow-sm"><h2 class="text-xl font-extrabold text-secondary"><?=htmlspecialchars($heading)?></h2><p class="mt-3 text-sm leading-7 text-slate-600"><?=htmlspecialchars($copy)?></p></article>
        <?php endforeach; ?>
    </div>
    <a href="/contact" class="mt-10 inline-flex rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white">Discuss your gift</a>
    
    <?php require BASE_PATH . '/resources/views/public/home/components/faqs.php'; ?>
</section>
