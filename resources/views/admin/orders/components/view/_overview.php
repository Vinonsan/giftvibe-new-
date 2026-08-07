<?php
declare(strict_types=1);
?>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php $field('Customer Name', (string) $order['customer_name']); ?>
    <?php $field('Customer Phone', (string) $order['customer_phone']); ?>
    <?php $field('Customer Email', (string) $order['customer_email']); ?>
    <?php $field('Recipient Name', (string) $order['recipient_name']); ?>
    <?php $field('Recipient Phone', (string) $order['recipient_phone']); ?>
    <?php $field('Delivery Date', (string) ($order['delivery_date'] ?? ''), trim((string) ($order['delivery_date'] ?? '')) === ''); ?>
</div>

<hr class="my-6 border-slate-100">

<h3 class="mb-4 text-sm font-bold text-secondary">Delivery Address</h3>
<div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 text-sm leading-relaxed text-secondary">
    <?= htmlspecialchars((string) $order['delivery_address_line_1']) ?><br>
    <?php if (trim((string) ($order['delivery_address_line_2'] ?? '')) !== ''): ?>
        <?= htmlspecialchars((string) $order['delivery_address_line_2']) ?><br>
    <?php endif; ?>
    <?= htmlspecialchars((string) $order['delivery_city']) ?>,
    <?= htmlspecialchars((string) ($order['delivery_district'] ?? '')) ?>
    <?php if (trim((string) ($order['delivery_postal_code'] ?? '')) !== ''): ?>
        · <?= htmlspecialchars((string) $order['delivery_postal_code']) ?>
    <?php endif; ?>
</div>

<?php if (trim((string) ($order['customer_notes'] ?? '')) !== ''): ?>
    <div class="mt-4">
        <h3 class="mb-2 text-sm font-bold text-secondary">Customer Notes</h3>
        <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 text-sm text-secondary whitespace-pre-wrap"><?= htmlspecialchars((string) $order['customer_notes']) ?></div>
    </div>
<?php endif; ?>

<hr class="my-6 border-slate-100">

<h3 class="mb-4 text-sm font-bold text-secondary">Order Summary</h3>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php $field('Subtotal', 'LKR ' . number_format((float) ($order['subtotal'] ?? 0), 2)); ?>
    <?php $field('Discount', 'LKR ' . number_format((float) ($order['discount_total'] ?? 0), 2)); ?>
    <?php $field('Delivery Fee', 'LKR ' . number_format((float) ($order['delivery_fee'] ?? 0), 2)); ?>
    <?php $field('Grand Total', 'LKR ' . number_format((float) $order['grand_total'], 2)); ?>
</div>

<?php if ($delivery): ?>
    <hr class="my-6 border-slate-100">
    <h3 class="mb-4 text-sm font-bold text-secondary">Tracking Snapshot</h3>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php $field('Tracking Code', (string) ($delivery['tracking_code'] ?? ''), trim((string) ($delivery['tracking_code'] ?? '')) === ''); ?>
        <?php $field('Courier', (string) ($delivery['courier_service_name'] ?? ''), trim((string) ($delivery['courier_service_name'] ?? '')) === ''); ?>
        <?php $field('Courier Tracking #', (string) ($delivery['courier_tracking_number'] ?? ''), trim((string) ($delivery['courier_tracking_number'] ?? '')) === ''); ?>
    </div>
    <a href="<?= htmlspecialchars($viewBase . '&tab=tracking') ?>" class="mt-3 inline-flex text-sm font-bold text-primary hover:underline">Manage tracking →</a>
<?php endif; ?>
