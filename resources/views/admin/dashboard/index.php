<?php

declare(strict_types=1);

/**
 * Admin dashboard section — rendered as a child of the admin layout.
 */
?>
<section class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-secondary">Dashboard</h2>
        <p class="text-sm text-slate-500">Welcome back! Here's what's happening today.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Products</p>
            <p class="mt-2 text-3xl font-extrabold text-secondary">0</p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Orders</p>
            <p class="mt-2 text-3xl font-extrabold text-secondary">0</p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Customers</p>
            <p class="mt-2 text-3xl font-extrabold text-secondary">0</p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Revenue</p>
            <p class="mt-2 text-3xl font-extrabold text-primary">LKR 0.00</p>
        </div>
    </div>

    <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
        <h3 class="text-base font-semibold text-secondary">Recent Orders</h3>
        <p class="mt-2 text-sm text-slate-500">No orders yet. Once you add products and start selling, recent orders will appear here.</p>
    </div>
</section>
