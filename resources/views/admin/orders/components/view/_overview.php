<?php
declare(strict_types=1);
?>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php $field('Order Source', ucfirst((string) ($order['order_source'] ?? 'website'))); ?>
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

<hr class="my-6 border-slate-100">
<details class="rounded-xl border border-slate-200 bg-white p-4">
    <summary class="cursor-pointer text-sm font-bold text-primary">Edit order details</summary>
    <form method="post" action="<?= app_url('/admin/orders/view?id=' . $orderId) ?>" class="mt-4 grid gap-3 sm:grid-cols-2">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= $orderId ?>"><input type="hidden" name="decision" value="edit_details">
        <?php foreach ([['customer_name','Customer name'],['customer_phone','Customer phone'],['customer_email','Customer email'],['recipient_name','Recipient name'],['recipient_phone','Recipient phone'],['delivery_address_line_1','Address line 1'],['delivery_address_line_2','Address line 2'],['delivery_city','City'],['delivery_district','District']] as [$key,$label]): ?>
            <label class="block"><span class="mb-1 block text-xs font-bold text-slate-500"><?= $label ?></span><input name="<?= $key ?>" value="<?= htmlspecialchars((string)($order[$key] ?? ''), ENT_QUOTES) ?>" class="<?= $fc ?>"></label>
        <?php endforeach; ?>
        <label class="block"><span class="mb-1 block text-xs font-bold text-slate-500">Delivery date</span><input type="date" name="delivery_date" value="<?= htmlspecialchars((string)($order['delivery_date'] ?? '')) ?>" class="<?= $fc ?>"></label>
        <label class="block"><span class="mb-1 block text-xs font-bold text-slate-500">Discount (LKR)</span><input type="number" min="0" step="0.01" name="discount_total" value="<?= (float)($order['discount_total'] ?? 0) ?>" class="<?= $fc ?>"></label>
        <label class="block"><span class="mb-1 block text-xs font-bold text-slate-500">Order source</span><select name="order_source" class="<?= $fc ?>"><?php foreach (['website'=>'Website','admin'=>'Admin / walk-in','whatsapp'=>'WhatsApp','phone'=>'Phone'] as $v=>$l): ?><option value="<?= $v ?>" <?= ($order['order_source'] ?? 'website')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
        <label class="block sm:col-span-2"><span class="mb-1 block text-xs font-bold text-slate-500">Customer notes</span><textarea name="customer_notes" rows="2" class="<?= $fc ?>"><?= htmlspecialchars((string)($order['customer_notes'] ?? '')) ?></textarea></label>
        <button class="rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white sm:col-span-2">Save order changes</button>
    </form>
</details>
