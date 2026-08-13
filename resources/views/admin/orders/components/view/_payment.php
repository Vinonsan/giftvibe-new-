<?php
declare(strict_types=1);

$redirectTo = $viewBase . '&tab=payment';
$canAct = !in_array($orderStatus, ['cancelled', 'delivered'], true);

$workflowButtons = [
    'processing'       => ['label' => 'Mark Processing', 'color' => 'border-slate-200 bg-white text-secondary hover:bg-slate-50'],
    'ready'            => ['label' => 'Mark Ready', 'color' => 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100'],
    'out_for_delivery' => ['label' => 'Out for Delivery', 'color' => 'border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100'],
    'delivered'        => ['label' => 'Mark Delivered ✓', 'color' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'],
];
?>

<?php if ($canAct): ?>
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick actions</p>

        <?php if ($isPending): ?>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <form method="post" action="/admin/orders" class="flex-1 min-w-[140px]">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                    <input type="hidden" name="delivery_days" value="3">
                    <button type="submit" name="decision" value="confirm" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-secondary transition cursor-pointer">
                        ✓ Accept order
                    </button>
                </form>
                <form method="post" action="/admin/orders" class="flex-1 min-w-[140px]" onsubmit="return confirm('Reject this order?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                    <button type="submit" name="decision" value="cancel" class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-100 transition cursor-pointer">
                        ✕ Reject order
                    </button>
                </form>
                <form method="post" action="/admin/orders" class="flex-[2] min-w-[200px]">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                    <button type="submit" name="decision" value="payment_pending" class="flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800 hover:bg-amber-100 transition cursor-pointer">
                        ⏳ Payment pending — ask for receipt
                    </button>
                </form>
            </div>
        <?php else: ?>
            <?php
                $nextSteps = match ($orderStatus) {
                    'confirmed' => ['processing', 'ready', 'out_for_delivery', 'delivered'],
                    'processing' => ['ready', 'out_for_delivery', 'delivered'],
                    'ready' => ['out_for_delivery', 'delivered'],
                    'out_for_delivery' => ['delivered'],
                    default => [],
                };
            ?>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <?php foreach ($nextSteps as $status): ?>
                    <?php $btn = $workflowButtons[$status] ?? null; if (!$btn) continue; ?>
                    <form method="post" action="/admin/orders" class="flex-1 min-w-[130px]" <?= $status === 'delivered' ? 'onsubmit="return confirm(\'Mark as delivered? Revenue will be added to finance.\')"' : '' ?>>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                        <input type="hidden" name="decision" value="advance_status">
                        <input type="hidden" name="order_status" value="<?= htmlspecialchars($status) ?>">
                        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                        <button type="submit" class="w-full rounded-xl border px-3 py-2.5 text-xs font-bold transition cursor-pointer <?= $btn['color'] ?>"><?= htmlspecialchars($btn['label']) ?></button>
                    </form>
                <?php endforeach; ?>

                <?php if ($paymentStatus !== 'paid'): ?>
                    <form method="post" action="/admin/orders" class="flex-1 min-w-[130px]">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                        <button type="submit" name="decision" value="mark_paid" class="w-full rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition cursor-pointer">✓ Mark paid</button>
                    </form>
                    <form method="post" action="/admin/orders" class="flex-1 min-w-[130px]">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                        <button type="submit" name="decision" value="payment_pending" class="w-full rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-bold text-amber-800 hover:bg-amber-100 transition cursor-pointer">⏳ Payment pending</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/_progress.php'; ?>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php $field('Payment Method', ucfirst((string) ($payment['method'] ?? 'N/A'))); ?>
    <?php $field('Payment Option', ucfirst((string) ($paymentMeta['payment_option'] ?? 'N/A'))); ?>
    <?php $field('Amount', 'LKR ' . number_format((float) ($payment['amount'] ?? $order['grand_total']), 2)); ?>
    <?php $field('Balance Due', 'LKR ' . number_format((float) ($paymentMeta['balance_due'] ?? 0), 2)); ?>
    <?php $field('Payment Status', ucfirst($paymentStatus)); ?>
    <?php $field('Order Status', ucfirst(str_replace('_', ' ', $orderStatus))); ?>
</div>

<?php if ($canAct): ?>
<form method="post" action="/admin/orders" class="mt-5 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-end">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= $orderId ?>"><input type="hidden" name="decision" value="update_payment"><input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
    <label class="flex-1"><span class="mb-1 block text-xs font-bold text-slate-500">Total amount received</span><input type="number" min="0" max="<?= (float)$order['grand_total'] ?>" step="0.01" name="payment_amount" value="<?= (float)($payment['amount'] ?? 0) ?>" class="<?= $fc ?>"></label>
    <label class="flex-1"><span class="mb-1 block text-xs font-bold text-slate-500">Status</span><select name="payment_status" class="<?= $fc ?>"><option value="pending">Pending / partial</option><option value="paid">Paid in full</option><option value="failed">Failed</option></select></label>
    <button class="rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white">Update payment</button>
</form>
<?php endif; ?>

<div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/40 p-5">
    <h3 class="mb-3 text-sm font-bold text-secondary">Payment Receipt</h3>
    <?php if ($receiptPath !== ''): ?>
        <button type="button" data-modal-open="order-receipt-modal" data-receipt-src="<?= htmlspecialchars($receiptPath) ?>" data-order-id="<?= $orderId ?>"
            class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-white px-4 py-2.5 text-sm font-bold text-primary hover:bg-primary/5 transition cursor-pointer">
            View receipt image
        </button>
    <?php else: ?>
        <p class="text-sm font-semibold text-rose-600">No receipt uploaded yet.</p>
    <?php endif; ?>
</div>

<?php if ($canAct && ($isPending || $paymentStatus === 'pending')): ?>
    <hr class="my-8 border-slate-100">
    <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5">
        <h3 class="mb-1 text-sm font-bold text-secondary">Message to customer</h3>
        <p class="mb-4 text-sm text-slate-500">Customize the SMS sent when you mark payment as pending (e.g. ask them to pay and upload receipt).</p>
        <form method="post" action="/admin/orders" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="order_id" value="<?= $orderId ?>">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
            <input type="hidden" name="decision" value="payment_pending">
            <textarea name="customer_message" rows="3" class="<?= $fc ?>" placeholder="Please complete your payment and upload your bank transfer receipt for order <?= htmlspecialchars((string) $order['order_number']) ?>."><?= htmlspecialchars((string) ($order['admin_notes'] ?? '')) ?></textarea>
            <?php if ($isPending): ?>
                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold text-secondary">Delivery days (for accept)</span>
                    <input type="number" name="delivery_days" min="1" max="30" value="3" class="<?= $fc ?> max-w-[120px]">
                </label>
            <?php endif; ?>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-amber-100 px-5 py-2.5 text-sm font-bold text-amber-900 hover:bg-amber-200 transition cursor-pointer">
                Send payment pending message
            </button>
        </form>
    </div>
<?php elseif ($orderStatus === 'delivered'): ?>
    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50/60 px-4 py-3 text-sm text-emerald-800">
        <strong>Order complete.</strong> Revenue from this order is recorded in Finance.
    </div>
<?php elseif ($orderStatus === 'cancelled'): ?>
    <div class="mt-6 rounded-xl border border-rose-100 bg-rose-50/60 px-4 py-3 text-sm text-rose-700">This order was rejected. No further actions available.</div>
<?php endif; ?>
