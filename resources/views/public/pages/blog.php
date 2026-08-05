<?php declare(strict_types=1); ?>
<section class="mx-auto max-w-7xl py-12 sm:py-16">
    <div class="max-w-3xl"><p class="text-xs font-extrabold uppercase tracking-[.22em] text-primary">GiftVibe journal</p><h1 class="mt-4 text-4xl font-black tracking-tight text-secondary sm:text-5xl"><?=htmlspecialchars($title)?></h1><p class="mt-5 text-base leading-8 text-slate-600"><?=htmlspecialchars($intro)?></p></div>
    <div class="mt-12 grid gap-6 md:grid-cols-3">
        <?php foreach ([['How to choose a meaningful birthday gift','Think about the recipient’s interests, your shared memories and something they can enjoy beyond the celebration.','Birthday gifts'],['What to include in a thoughtful gift box','Combine one memorable item, a favourite treat and a personal note for a balanced gift that feels intentional.','Gift boxes'],['Flowers, chocolates or a combo?','Flowers create an immediate emotional moment, chocolates add indulgence, while a combo brings several experiences together.','Gift ideas']] as [$heading,$copy,$category]): ?>
        <article class="flex flex-col rounded-3xl border border-slate-200 bg-white p-7 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-primary"><?=htmlspecialchars($category)?></p><h2 class="mt-3 text-xl font-extrabold leading-tight text-secondary"><?=htmlspecialchars($heading)?></h2><p class="mt-4 text-sm leading-7 text-slate-600"><?=htmlspecialchars($copy)?></p><a href="/shop" class="mt-6 text-sm font-bold text-primary">Explore related gifts &rarr;</a></article>
        <?php endforeach; ?>
    </div>
</section>
