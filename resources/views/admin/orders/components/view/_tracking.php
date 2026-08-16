<?php
declare(strict_types=1);

$redirectTo = $viewBase . '&tab=tracking';
$d = $delivery ?? [];
$currentDeliveryStatus = (string) ($d['delivery_status'] ?? 'pending');

$trackingStages = [
    'pending'          => ['label' => 'Pending', 'icon' => '⏳'],
    'assigned'         => ['label' => 'Assigned', 'icon' => '📋'],
    'dispatched'       => ['label' => 'Dispatched', 'icon' => '📦'],
    'picked_up'        => ['label' => 'Picked up', 'icon' => '🚚'],
    'out_for_delivery' => ['label' => 'Out for delivery', 'icon' => '🛵'],
    'delivered'        => ['label' => 'Delivered', 'icon' => '✓'],
];
?>

<form method="post" action="<?= htmlspecialchars(app_url('/admin/orders')) ?>" class="space-y-6" id="tracking-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="order_id" value="<?= $orderId ?>">
    <input type="hidden" name="decision" value="save_tracking">
    <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
    <input type="hidden" name="delivery_status" id="delivery-status-input" value="<?= htmlspecialchars($currentDeliveryStatus) ?>">

    <?php if ($delivery && !empty($delivery['tracking_code'])): ?>
        <div class="rounded-2xl border border-primary/15 bg-primary/[0.04] p-5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Customer tracking code</p>
            <p class="mt-1 font-mono text-2xl font-black text-secondary"><?= htmlspecialchars((string) $delivery['tracking_code']) ?></p>
            <p class="mt-1 text-xs text-slate-500">Customer sees this code when tracking their parcel.</p>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 p-5">
        <p class="mb-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Parcel stage — customer notified on save</p>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
            <?php foreach ($trackingStages as $key => $stage): ?>
                <?php $active = $currentDeliveryStatus === $key; ?>
                <button type="button"
                    data-tracking-stage="<?= htmlspecialchars($key) ?>"
                    class="tracking-stage-btn rounded-xl border px-2 py-3 text-center text-xs font-bold transition cursor-pointer <?= $active ? 'border-primary bg-primary text-white shadow-sm shadow-primary/20' : 'border-slate-200 bg-white text-secondary hover:border-primary/30 hover:bg-primary/5' ?>">
                    <span class="block text-base"><?= $stage['icon'] ?></span>
                    <span class="mt-1 block leading-tight"><?= htmlspecialchars($stage['label']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Courier service</span>
            <input type="text" name="courier_service_name" value="<?= htmlspecialchars((string) ($d['courier_service_name'] ?? '')) ?>" placeholder="e.g. Domex, Pronto" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Courier tracking number</span>
            <input type="text" name="courier_tracking_number" value="<?= htmlspecialchars((string) ($d['courier_tracking_number'] ?? '')) ?>" placeholder="AWB / tracking ID" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Parcel reference</span>
            <input type="text" name="parcel_reference_number" value="<?= htmlspecialchars((string) ($d['parcel_reference_number'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">GiftVibe tracking code</span>
            <input type="text" name="tracking_code" value="<?= htmlspecialchars((string) ($d['tracking_code'] ?? '')) ?>" placeholder="Auto-generated if empty" class="<?= $fc ?>">
        </label>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Dispatch date</span>
            <input type="date" name="dispatch_date" value="<?= htmlspecialchars((string) ($d['dispatch_date'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Expected delivery</span>
            <input type="date" name="expected_delivery_date" value="<?= htmlspecialchars((string) ($d['expected_delivery_date'] ?? $order['delivery_date'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Delivered date</span>
            <input type="date" name="delivered_date" value="<?= htmlspecialchars((string) ($d['delivered_date'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
    </div>

    <?php if (!empty($deliveryHistory)): ?>
        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
            <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Recent parcel updates (customer notified)</p>
            <ul class="space-y-2 text-sm">
                <?php foreach (array_slice($deliveryHistory, 0, 5) as $dh): ?>
                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-2 last:border-0">
                        <span class="font-semibold text-secondary"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', (string) $dh['status']))) ?></span>
                        <span class="text-xs text-slate-400"><?= htmlspecialchars((string) ($dh['created_at'] ?? '')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="flex justify-end border-t border-slate-100 pt-4">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary transition cursor-pointer">
            Save &amp; notify customer
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('delivery-status-input');
    var buttons = document.querySelectorAll('.tracking-stage-btn');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var stage = btn.getAttribute('data-tracking-stage');
            if (input) input.value = stage;
            buttons.forEach(function (b) {
                b.classList.remove('border-primary', 'bg-primary', 'text-white', 'shadow-sm', 'shadow-primary/20');
                b.classList.add('border-slate-200', 'bg-white', 'text-secondary');
            });
            btn.classList.remove('border-slate-200', 'bg-white', 'text-secondary');
            btn.classList.add('border-primary', 'bg-primary', 'text-white', 'shadow-sm', 'shadow-primary/20');
        });
    });
});
</script>
