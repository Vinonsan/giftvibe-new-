<?php
declare(strict_types=1);

/**
 * @var array       $order
 * @var array       $orderItems
 * @var array|null  $payment
 * @var array|null  $delivery
 * @var array       $statusHistory
 * @var string      $tab
 * @var array       $tabs
 * @var array|null  $flash
 * @var string      $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$orderId = (int) $order['id'];
$viewBase = '/admin/orders/view?id=' . $orderId;

$tabLabels = [
    'overview' => 'Overview',
    'items'    => 'Items',
    'payment'  => 'Payment & Actions',
    'tracking' => 'Tracking',
    'history'  => 'History',
];

$orderStatus = (string) ($order['order_status'] ?? 'pending');
$paymentStatus = (string) ($order['payment_status'] ?? 'pending');
$isPending = $orderStatus === 'pending';

$paymentMeta = json_decode((string) ($payment['raw_response_json'] ?? ''), true) ?: [];

$statusBadge = static function (string $status, string $type = 'order'): string {
    $label = ucfirst(str_replace('_', ' ', $status));
    if ($type === 'payment') {
        $class = match ($status) {
            'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'failed', 'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-100',
            default => 'bg-amber-50 text-amber-700 ring-amber-100',
        };
    } else {
        $class = match ($status) {
            'delivered', 'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'cancelled', 'refunded' => 'bg-rose-50 text-rose-700 ring-rose-100',
            default => 'bg-amber-50 text-amber-700 ring-amber-100',
        };
    }
    return '<span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ' . $class . '">' . htmlspecialchars($label) . '</span>';
};

$field = static function (string $label, string $value, bool $empty = false): void {
    ?>
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($label) ?></p>
        <p class="mt-1 text-sm font-semibold <?= $empty ? 'text-slate-400 italic' : 'text-secondary' ?>">
            <?= $empty ? '—' : htmlspecialchars($value) ?>
        </p>
    </div>
    <?php
};

$normalizeImage = static function (?string $path): string {
    $path = (string) $path;
    if ($path === '') return '';
    if (str_starts_with($path, 'public/')) return '/' . substr($path, 7);
    if (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) return '/' . $path;
    return $path;
};

$receiptPath = $normalizeImage($payment['receipt_path'] ?? '');
?>

<div class="space-y-5">
    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-secondary"><?= htmlspecialchars((string) $order['order_number']) ?></h1>
                <?= $statusBadge($orderStatus) ?>
                <?= $statusBadge($paymentStatus, 'payment') ?>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                <?= htmlspecialchars((string) $order['customer_name']) ?> ·
                LKR <?= number_format((float) $order['grand_total'], 2) ?> ·
                <?= htmlspecialchars((string) ($order['created_at'] ?? '')) ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if (in_array($orderStatus, ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'], true)): ?>
                <a href="<?= htmlspecialchars(app_url('/admin/orders/receipt?id=' . $orderId)) ?>" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75"/></svg>
                    Print receipt
                </a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(app_url('/admin/orders')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back to orders
            </a>
        </div>
    </div>

    <?php if ($isPending): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 px-5 py-4 text-sm text-amber-900">
            <strong class="font-bold">Action required:</strong> This order is pending verification. Go to the
            <a href="<?= htmlspecialchars($viewBase . '&tab=payment') ?>" class="font-bold text-primary underline">Payment &amp; Actions</a>
            tab to confirm or reject.
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-3 pt-3" aria-label="Order sections">
            <?php foreach ($tabLabels as $key => $label): ?>
                <a href="<?= htmlspecialchars($viewBase . '&tab=' . $key) ?>"
                   class="shrink-0 rounded-t-xl px-4 py-2.5 text-sm font-bold transition <?= $tab === $key ? 'border border-b-white border-slate-200 bg-white text-primary -mb-px' : 'text-slate-500 hover:bg-slate-50 hover:text-secondary' ?>"
                   aria-current="<?= $tab === $key ? 'page' : 'false' ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="p-5 sm:p-6">
            <?php if ($tab === 'overview'): require __DIR__ . '/components/view/_progress.php'; endif; ?>

            <h2 class="mb-5 text-base font-bold text-secondary"><?= htmlspecialchars($tabLabels[$tab] ?? 'Details') ?></h2>

            <?php
            match ($tab) {
                'items'    => require __DIR__ . '/components/view/_items.php',
                'payment'  => require __DIR__ . '/components/view/_payment.php',
                'tracking' => require __DIR__ . '/components/view/_tracking.php',
                'history'  => require __DIR__ . '/components/view/_history.php',
                default    => require __DIR__ . '/components/view/_overview.php',
            };
            ?>
        </div>
    </div>
</div>

<?php if ($receiptPath !== ''): require __DIR__ . '/components/view/_receipt-modal.php'; endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-modal-open="order-receipt-modal"]');
        if (!trigger) return;
        var src = trigger.getAttribute('data-receipt-src') || '';
        var img = document.getElementById('order-modal-receipt-img');
        var link = document.getElementById('order-modal-receipt-link');
        var orderInput = document.getElementById('order-modal-receipt-order-id');
        if (img) img.src = src;
        if (link) link.href = src;
        if (orderInput) orderInput.value = trigger.getAttribute('data-order-id') || '';
    });
});
</script>
