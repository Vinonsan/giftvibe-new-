<?php

declare(strict_types=1);

/**
 * Global admin sidebar — included once inside the admin layout.
 */
?>
<aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-secondary text-white lg:flex">
    <div class="flex h-16 items-center border-b border-white/10 px-6">
        <a href="/admin" class="text-lg font-extrabold tracking-tight">
            Gift<span class="text-primary">Vibe</span>
            <span class="ml-1 text-xs font-medium text-slate-400">Admin</span>
        </a>
    </div>

    <nav class="flex-1 space-y-1 px-3 py-4 text-sm font-medium">
        <a href="/admin" class="block rounded-lg bg-primary/20 px-4 py-2.5 text-white">Dashboard</a>
        <a href="#" class="block rounded-lg px-4 py-2.5 text-slate-400 transition hover:bg-white/5 hover:text-white">Products</a>
        <a href="#" class="block rounded-lg px-4 py-2.5 text-slate-400 transition hover:bg-white/5 hover:text-white">Orders</a>
        <a href="#" class="block rounded-lg px-4 py-2.5 text-slate-400 transition hover:bg-white/5 hover:text-white">Customers</a>
        <a href="#" class="block rounded-lg px-4 py-2.5 text-slate-400 transition hover:bg-white/5 hover:text-white">Settings</a>
    </nav>

    <div class="border-t border-white/10 p-4">
        <a href="/" class="text-xs text-slate-400 transition hover:text-white">&larr; Back to website</a>
    </div>
</aside>
