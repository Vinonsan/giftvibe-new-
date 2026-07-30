<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Courier and parcels</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Deliveries</h1>
                <p class="mt-1 text-sm text-slate-500">Track courier numbers, parcel references, reminders, failed deliveries, and returns.</p>
            </div>
            <form method="GET" action="<?= e(url('/admin/deliveries')) ?>" class="grid gap-3 sm:grid-cols-3">
                <input name="q" value="<?= e($filters['q'] ?? '') ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Order, tracking, parcel">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All parcel statuses</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        <?php foreach ($counts as $status => $total): ?>
            <a href="<?= e($status === 'due_reminders' ? url('/admin/deliveries') : url('/admin/deliveries?status=' . $status)) ?>" class="rounded-card border border-slate-200 bg-white p-4 shadow-sm hover:border-primary-200">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e($label($status)) ?></span>
                <strong class="mt-1 block text-2xl text-slate-950"><?= (int) $total ?></strong>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($reminders)): ?>
        <section class="rounded-card border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-amber-900">Open delivery reminders</h2>
            <div class="mt-3 grid gap-3">
                <?php foreach ($reminders as $reminder): ?>
                    <div class="flex flex-col gap-3 rounded-button bg-white p-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <span><strong><?= e($reminder['order_number']) ?></strong> - <?= e($reminder['message']) ?> <span class="text-slate-400"><?= e(date('M d, Y h:i A', strtotime($reminder['reminder_at']))) ?></span></span>
                        <form method="POST" action="<?= e(url('/admin/deliveries/reminders/' . $reminder['id'] . '/complete')) ?>">
                            <?= csrf_field() ?>
                            <button class="rounded-button border border-amber-300 px-3 py-1 text-xs font-bold text-amber-900">Done</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Courier</th>
                    <th class="px-4 py-3 text-left">Parcel</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Dates</th>
                    <th class="px-4 py-3 text-right">Charge</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach (($deliveries ?? []) as $delivery): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="<?= e(url('/admin/orders/' . $delivery['order_id'])) ?>" class="font-bold text-primary"><?= e($delivery['order_number']) ?></a><br><span class="text-xs text-slate-400"><?= e($delivery['recipient_name']) ?>, <?= e($delivery['delivery_city']) ?></span></td>
                        <td class="px-4 py-3"><?= e($delivery['courier_service_name'] ?: 'Not assigned') ?><br><span class="text-xs text-slate-400"><?= e($delivery['courier_tracking_number'] ?: '-') ?></span></td>
                        <td class="px-4 py-3"><?= e($delivery['parcel_reference_number'] ?: '-') ?></td>
                        <td class="px-4 py-3 font-semibold <?= in_array($delivery['delivery_status'], ['failed','returned'], true) ? 'text-danger' : 'text-slate-700' ?>"><?= e($label($delivery['delivery_status'])) ?></td>
                        <td class="px-4 py-3 text-xs text-slate-500">Dispatch: <?= e($delivery['dispatch_date'] ?: '-') ?><br>Expected: <?= e($delivery['expected_delivery_date'] ?: '-') ?><br>Delivered: <?= e($delivery['delivered_date'] ?: '-') ?></td>
                        <td class="px-4 py-3 text-right font-bold"><?= $money($delivery['delivery_charge']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($deliveries)): ?><tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No parcel records found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
