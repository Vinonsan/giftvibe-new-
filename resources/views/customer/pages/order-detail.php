<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
?>
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <a href="<?= e(url('/customer/orders')) ?>" class="text-sm font-bold text-primary">Back to orders</a>
        <div class="mt-4 rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-wider text-primary">Order</p><h1 class="text-2xl font-black text-slate-950"><?= e($order['order_number']) ?></h1></div>
                <div class="text-sm font-bold text-slate-600"><?= e($label($order['order_status'])) ?> - <?= e(ucfirst($order['payment_status'])) ?></div>
            </div>
            <div class="mt-6 divide-y divide-slate-100">
                <?php foreach ($items as $item): ?>
                    <div class="flex justify-between gap-4 py-3 text-sm"><span><?= e($item['product_name']) ?> x <?= (int) $item['quantity'] ?></span><strong><?= $money($item['total_price']) ?></strong></div>
                <?php endforeach; ?>
            </div>
            <dl class="mt-4 max-w-sm space-y-2 text-sm">
                <div class="flex justify-between"><dt>Subtotal</dt><dd><?= $money($order['subtotal']) ?></dd></div>
                <div class="flex justify-between"><dt>Delivery</dt><dd><?= $money($order['delivery_fee']) ?></dd></div>
                <div class="flex justify-between border-t border-slate-200 pt-2 font-extrabold"><dt>Total</dt><dd><?= $money($order['grand_total']) ?></dd></div>
            </dl>
            <p class="mt-6 text-sm text-slate-500">Deliver to <?= e($order['recipient_name']) ?>, <?= e($order['delivery_address_line_1']) ?>, <?= e($order['delivery_city']) ?>.</p>
        </div>

        <div class="mt-6 rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-primary">Courier tracking</p>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-950"><?= e($delivery ? $label($delivery['delivery_status']) : 'Pending') ?></h2>
                    <p class="mt-2 text-sm text-slate-500">Courier: <?= e($delivery['courier_service_name'] ?? 'Not assigned yet') ?></p>
                    <p class="text-sm text-slate-500">Tracking number: <?= e($delivery['courier_tracking_number'] ?? '-') ?></p>
                    <p class="text-sm text-slate-500">Parcel reference: <?= e($delivery['parcel_reference_number'] ?? '-') ?></p>
                </div>
                <div class="text-sm text-slate-500">
                    <p>Dispatch: <?= e($delivery['dispatch_date'] ?? '-') ?></p>
                    <p>Expected: <?= e($delivery['expected_delivery_date'] ?? '-') ?></p>
                    <p>Delivered: <?= e($delivery['delivered_date'] ?? '-') ?></p>
                </div>
            </div>

            <div class="mt-5 divide-y divide-slate-100">
                <?php foreach (($deliveryHistory ?? []) as $entry): ?>
                    <div class="py-3 text-sm">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <strong class="<?= in_array($entry['status'], ['failed','returned'], true) ? 'text-danger' : 'text-slate-900' ?>"><?= e($label($entry['status'])) ?></strong>
                            <span class="text-xs text-slate-400"><?= e(date('M d, Y h:i A', strtotime($entry['created_at']))) ?></span>
                        </div>
                        <?php if (!empty($entry['note'])): ?><p class="mt-1 text-slate-500"><?= e($entry['note']) ?></p><?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($deliveryHistory)): ?><p class="py-3 text-sm text-slate-500">Tracking updates will appear here after dispatch.</p><?php endif; ?>
            </div>
        </div>
    </div>
</section>
