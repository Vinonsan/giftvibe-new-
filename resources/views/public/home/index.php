<?php

declare(strict_types=1);

/**
 * Home page section — rendered as a child of the public layout.
 *
 * @var string $title
 * @var string $message
 */
?>
<section class="bg-gradient-to-br from-indigo-50 via-white to-pink-50">
    <div class="mx-auto flex min-h-[70vh] max-w-7xl flex-col items-center justify-center px-6 text-center">
        <span class="rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-primary">
            Gift Vibe MVC
        </span>

        <h1 class="mt-6 text-5xl font-extrabold tracking-tight text-secondary sm:text-6xl">
            <?= htmlspecialchars($message) ?>
        </h1>

        <p class="mt-5 max-w-xl text-lg text-slate-500">
            Your Core PHP MVC app is running with layouts, global components, and Tailwind CSS v4.
        </p>

        <p class="mt-3 text-sm text-slate-400">
            Controller &rarr; public-layout (navbar + footer) &rarr; home/index
        </p>

        <div class="mt-8 flex items-center gap-3">
            <a href="/admin" class="rounded-full bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:opacity-90">
                Open Admin
            </a>
            <a href="/shop" class="rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-secondary transition hover:border-primary hover:text-primary">
                Browse Shop
            </a>
        </div>
    </div>
</section>
