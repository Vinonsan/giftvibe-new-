<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Order management</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Orders</h1>
            </div>
            <form method="GET" action="<?= e(url('/admin/orders')) ?>" class="grid gap-3 sm:grid-cols-4">
                <input name="q" value="<?= e($filters['q'] ?? '') ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Search orders">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All orders</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="parcel_status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All parcels</option>
                    <?php foreach ($deliveryStatuses as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($filters['parcel_status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Parcel</th>
                    <th class="px-4 py-3 text-left">Payment</th>
                    <th class="px-4 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach (($orders ?? []) as $order): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="<?= e(url('/admin/orders/' . $order['id'])) ?>" class="font-bold text-primary"><?= e($order['order_number']) ?></a></td>
                        <td class="px-4 py-3"><?= e($order['customer_name']) ?><br><span class="text-xs text-slate-400"><?= e($order['customer_phone']) ?></span></td>
                        <td class="px-4 py-3"><?= e($label($order['order_status'])) ?></td>
                        <td class="px-4 py-3"><?= e($label($order['parcel_status'] ?: 'pending')) ?><br><span class="text-xs text-slate-400"><?= e($order['courier_tracking_number'] ?: '-') ?></span></td>
                        <td class="px-4 py-3"><?= e($order['payment_method'] ?: 'Manual') ?> - <?= e(ucfirst($order['payment_status'])) ?></td>
                        <td class="px-4 py-3 text-right font-bold"><?= $money($order['grand_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?><tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No orders found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
