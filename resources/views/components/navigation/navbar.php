<?php

declare(strict_types=1);

/**
 * Global public navigation bar.
 * Included once inside the public layout.
 */
?>
<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="/" class="text-xl font-extrabold tracking-tight text-secondary">
            Gift<span class="text-primary">Vibe</span>
        </a>

        <div class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
            <a href="/" class="hover:text-primary">Home</a>
            <a href="/shop" class="hover:text-primary">Shop</a>
            <a href="/about" class="hover:text-primary">About</a>
            <a href="/contact" class="hover:text-primary">Contact</a>
        </div>

        <a href="/admin" class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
            Get Started
        </a>
    </nav>
</header>
