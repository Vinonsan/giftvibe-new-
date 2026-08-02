<?php

declare(strict_types=1);

$footerGroups = [
    'Explore' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Shop', 'href' => '/shop'],
        ['label' => 'Categories', 'href' => '/categories'],
    ],
    'Company' => [
        ['label' => 'About us', 'href' => '/about'],
        ['label' => 'Contact', 'href' => '/contact'],
        ['label' => 'Privacy', 'href' => '/privacy'],
    ],
];
?>
<footer class="border-t border-slate-200 bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.25fr]">
            <div>
                <a href="/" class="inline-flex items-center gap-3" aria-label="GiftVibe home">
                    <img src="/assets/images/giftvibe-mark.svg" alt="" class="h-11 w-11 rounded-xl ring-1 ring-white/10">
                    <span class="text-xl font-extrabold tracking-tight">Gift<span class="text-slate-300">Vibe</span></span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-6 text-slate-400">
                    Thoughtful gifts for every person, moment and celebration—all in one place.
                </p>
            </div>

            <?php foreach ($footerGroups as $heading => $links): ?>
                <div>
                    <h2 class="text-sm font-semibold text-white"><?= htmlspecialchars($heading) ?></h2>
                    <ul class="mt-4 space-y-3">
                        <?php foreach ($links as $link): ?>
                            <li><a href="<?= htmlspecialchars($link['href']) ?>" class="text-sm text-slate-400 transition hover:text-white"><?= htmlspecialchars($link['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>

            <div>
                <h2 class="text-sm font-semibold text-white">Need help?</h2>
                <p class="mt-4 text-sm leading-6 text-slate-400">Questions about an order or choosing the right gift? We’re here to help.</p>
                <a href="/contact" class="mt-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                    Contact support
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; <?php require BASE_PATH . '/resources/views/components/shared/date.php'; ?> GiftVibe. All rights reserved.</p>
            <div class="flex items-center gap-5">
                <a href="/terms" class="transition hover:text-white">Terms</a>
                <a href="/privacy" class="transition hover:text-white">Privacy</a>
                <a href="/contact" class="transition hover:text-white">Support</a>
            </div>
        </div>
    </div>
</footer>
