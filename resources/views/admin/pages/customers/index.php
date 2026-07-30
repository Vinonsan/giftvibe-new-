<?php $money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2); ?>
<div class="space-y-6">
    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <p class="text-xs font-bold uppercase tracking-wider text-primary">Customer intelligence</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Customers</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Review customer activity, lifetime value, saved addresses, and custom gift interest.</p>
    </section>
    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <?php foreach (($customers ?? []) as $customer): ?>
            <article class="grid gap-4 border-b border-slate-100 p-4 text-sm lg:grid-cols-6 lg:items-center">
                <div class="lg:col-span-2">
                    <strong class="text-slate-950"><?= e(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?: 'Customer') ?></strong>
                    <p class="text-xs text-slate-400"><?= e($customer['email']) ?><?= $customer['phone'] ? ' - ' . e($customer['phone']) : '' ?></p>
                </div>
                <span class="rounded-button bg-slate-50 px-3 py-2 font-bold text-slate-600"><?= (int) $customer['orders_count'] ?> orders</span>
                <span class="rounded-button bg-primary-50 px-3 py-2 font-bold text-primary"><?= e($money($customer['lifetime_value'])) ?></span>
                <span class="rounded-button bg-slate-50 px-3 py-2 font-bold text-slate-600"><?= (int) $customer['address_count'] ?> addresses</span>
                <span class="rounded-button bg-slate-50 px-3 py-2 font-bold text-slate-600"><?= (int) $customer['request_count'] ?> custom requests</span>
            </article>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?><div class="p-8 text-center text-sm text-slate-500">No customers yet.</div><?php endif; ?>
    </section>
</div>
