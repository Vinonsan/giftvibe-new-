<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Order detail</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950"><?= e($order['order_number']) ?></h1>
                <p class="mt-1 text-sm text-slate-500"><?= e($order['customer_name']) ?> - <?= e($order['customer_phone']) ?> - <?= e($order['customer_email']) ?></p>
            </div>
            <a href="<?= e(url('/admin/orders')) ?>" class="rounded-button border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700">Back</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <section class="space-y-6 lg:col-span-8">
            <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950">Items</h2>
                <div class="mt-4 divide-y divide-slate-100">
                    <?php foreach ($items as $item): ?>
                        <div class="grid gap-2 py-3 text-sm sm:grid-cols-[1fr_auto_auto]">
                            <span><?= e($item['product_name']) ?><br><span class="text-xs text-slate-400">SKU <?= e($item['sku']) ?> - Cost <?= $money($item['cost_price']) ?></span></span>
                            <span>x <?= (int) $item['quantity'] ?></span>
                            <strong><?= $money($item['total_price']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <dl class="mt-4 max-w-sm space-y-2 text-sm">
                    <div class="flex justify-between"><dt>Subtotal</dt><dd><?= $money($order['subtotal']) ?></dd></div>
                    <div class="flex justify-between"><dt>Delivery charge</dt><dd><?= $money($order['delivery_fee']) ?></dd></div>
                    <div class="flex justify-between border-t border-slate-200 pt-2 font-extrabold"><dt>Grand total</dt><dd><?= $money($order['grand_total']) ?></dd></div>
                </dl>
            </div>

            <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950">Delivery address</h2>
                <p class="mt-2 text-sm text-slate-600"><?= e($order['recipient_name']) ?> - <?= e($order['recipient_phone']) ?></p>
                <p class="mt-1 text-sm text-slate-500"><?= e($order['delivery_address_line_1']) ?> <?= e($order['delivery_address_line_2']) ?>, <?= e($order['delivery_city']) ?>, <?= e($order['delivery_district']) ?></p>
            </div>

            <form method="POST" action="<?= e(url('/admin/deliveries/' . $order['id'] . '/parcel')) ?>" enctype="multipart/form-data" class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <?= csrf_field() ?>
                <h2 class="text-lg font-extrabold text-slate-950">Courier parcel details</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <input name="courier_service_name" value="<?= e($delivery['courier_service_name'] ?? '') ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Courier service, e.g. DOMEX">
                    <input name="courier_tracking_number" value="<?= e($delivery['courier_tracking_number'] ?? '') ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="DOMEX/courier tracking number">
                    <input name="parcel_reference_number" value="<?= e($delivery['parcel_reference_number'] ?? '') ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Parcel reference number">
                    <input name="delivery_charge" type="number" step="0.01" min="0" value="<?= e($delivery['delivery_charge'] ?? $order['delivery_fee']) ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Delivery charge">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Dispatch date<input name="dispatch_date" type="date" value="<?= e($delivery['dispatch_date'] ?? '') ?>" class="mt-1 w-full rounded-button border border-slate-300 px-3 py-2 text-sm normal-case"></label>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Expected date<input name="expected_delivery_date" type="date" value="<?= e($delivery['expected_delivery_date'] ?? '') ?>" class="mt-1 w-full rounded-button border border-slate-300 px-3 py-2 text-sm normal-case"></label>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Delivered date<input name="delivered_date" type="date" value="<?= e($delivery['delivered_date'] ?? '') ?>" class="mt-1 w-full rounded-button border border-slate-300 px-3 py-2 text-sm normal-case"></label>
                    <select name="delivery_status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <?php foreach ($deliveryStatuses as $status): ?><option value="<?= e($status) ?>" <?= ($delivery['delivery_status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                    </select>
                    <label class="sm:col-span-2 text-xs font-bold uppercase tracking-wider text-slate-500">Courier QR-code upload<input name="qr_code" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="mt-1 block w-full rounded-button border border-slate-300 px-3 py-2 text-sm normal-case"></label>
                    <?php if (!empty($delivery['qr_code_path'])): ?><p class="sm:col-span-2 break-all text-xs text-slate-500">Current QR attachment: <?= e($delivery['qr_code_path']) ?></p><?php endif; ?>
                    <textarea name="notes" rows="3" class="sm:col-span-2 rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Courier or parcel notes"><?= e($delivery['notes'] ?? '') ?></textarea>
                    <input name="history_note" class="sm:col-span-2 rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="History note for this update">
                </div>
                <div class="mt-5 border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-extrabold text-slate-900">Add admin delivery reminder</h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <input name="reminder_at" type="datetime-local" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="reminder_message" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Reminder message">
                    </div>
                </div>
                <button class="mt-4 rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Save parcel details</button>
            </form>

            <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950">Delivery history</h2>
                <div class="mt-4 divide-y divide-slate-100">
                    <?php foreach (($deliveryHistory ?? []) as $entry): ?>
                        <div class="py-3 text-sm">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <strong class="<?= in_array($entry['status'], ['failed','returned'], true) ? 'text-danger' : 'text-slate-900' ?>"><?= e($label($entry['status'])) ?></strong>
                                <span class="text-xs text-slate-400"><?= e(date('M d, Y h:i A', strtotime($entry['created_at']))) ?> by <?= e(trim(($entry['first_name'] ?? '') . ' ' . ($entry['last_name'] ?? '')) ?: 'System') ?></span>
                            </div>
                            <?php if (!empty($entry['note'])): ?><p class="mt-1 text-slate-500"><?= e($entry['note']) ?></p><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <aside class="space-y-6 lg:col-span-4">
            <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950">Sales and profit</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt>Sales</dt><dd class="font-bold"><?= $money($totals['sales']) ?></dd></div>
                    <div class="flex justify-between"><dt>Item cost</dt><dd class="font-bold"><?= $money($totals['cost']) ?></dd></div>
                    <div class="flex justify-between border-t border-slate-200 pt-2"><dt class="font-extrabold">Profit</dt><dd class="font-extrabold text-primary"><?= $money($totals['profit']) ?></dd></div>
                </dl>
            </div>

            <form method="POST" action="<?= e(url('/admin/orders/' . $order['id'] . '/status')) ?>" class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <?= csrf_field() ?>
                <h2 class="text-lg font-extrabold text-slate-950">Order status</h2>
                <select name="order_status" class="mt-4 w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <textarea name="note" rows="3" class="mt-3 w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Status note"></textarea>
                <button class="mt-3 w-full rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Update status</button>
            </form>

            <form method="POST" action="<?= e(url('/admin/deliveries/' . $order['id'] . '/status')) ?>" class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <?= csrf_field() ?>
                <h2 class="text-lg font-extrabold text-slate-950">Quick parcel status</h2>
                <select name="delivery_status" class="mt-4 w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($deliveryStatuses as $status): ?><option value="<?= e($status) ?>" <?= ($delivery['delivery_status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <textarea name="note" rows="3" class="mt-3 w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Status note"></textarea>
                <button class="mt-3 w-full rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Update parcel status</button>
            </form>

            <form method="POST" action="<?= e(url('/admin/orders/' . $order['id'] . '/payment')) ?>" class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <?= csrf_field() ?>
                <h2 class="text-lg font-extrabold text-slate-950">Payment verification</h2>
                <p class="mt-2 text-sm text-slate-500"><?= e($payment['method'] ?? 'Manual') ?> - <?= e(ucfirst($payment['status'] ?? $order['payment_status'])) ?></p>
                <?php if (!empty($payment['receipt_path'])): ?><p class="mt-2 break-all text-xs text-slate-500">Receipt: <?= e($payment['receipt_path']) ?></p><?php endif; ?>
                <select name="payment_status" class="mt-4 w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="paid">Verified paid</option>
                    <option value="failed">Reject payment</option>
                </select>
                <textarea name="verification_notes" rows="3" class="mt-3 w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Verification note"></textarea>
                <button class="mt-3 w-full rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Save payment decision</button>
            </form>
        </aside>
    </div>
</div>
