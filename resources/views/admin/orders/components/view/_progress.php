<?php
declare(strict_types=1);

/** @var string $orderStatus */
/** @var string|null $deliveryStatus */

$orderSteps = [
    'pending'           => ['label' => 'Pending', 'desc' => 'Awaiting payment verification'],
    'confirmed'         => ['label' => 'Confirmed', 'desc' => 'Order accepted'],
    'processing'        => ['label' => 'Processing', 'desc' => 'Preparing items'],
    'ready'             => ['label' => 'Ready', 'desc' => 'Ready for dispatch'],
    'out_for_delivery'  => ['label' => 'Out for delivery', 'desc' => 'With courier'],
    'delivered'         => ['label' => 'Delivered', 'desc' => 'Complete — added to finance'],
];

$cancelled = $orderStatus === 'cancelled';
$stepKeys = array_keys($orderSteps);
$currentIndex = 0;
if ($cancelled) {
    $currentIndex = -1;
} elseif ($orderStatus === 'delivered') {
    $currentIndex = count($stepKeys) - 1;
} else {
    $idx = array_search($orderStatus, $stepKeys, true);
    $currentIndex = $idx !== false ? $idx : 0;
}
?>

<div class="mb-6 overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50/50 p-4 sm:p-5">
    <p class="mb-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Order progress</p>
    <?php if ($cancelled): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">This order was cancelled.</div>
    <?php else: ?>
        <div class="flex min-w-[640px] items-start gap-0">
            <?php foreach ($orderSteps as $key => $step): ?>
                <?php
                    $idx = array_search($key, $stepKeys, true);
                    $done = $idx !== false && $idx < $currentIndex;
                    $active = $key === $orderStatus || ($orderStatus === 'delivered' && $key === 'delivered');
                ?>
                <div class="flex flex-1 flex-col items-center text-center">
                    <div class="flex w-full items-center">
                        <?php if ($idx > 0): ?>
                            <span class="h-0.5 flex-1 <?= $done || $active ? 'bg-primary' : 'bg-slate-200' ?>"></span>
                        <?php else: ?>
                            <span class="flex-1"></span>
                        <?php endif; ?>
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold ring-4 ring-slate-50/50 <?= $done ? 'bg-primary text-white' : ($active ? 'bg-primary text-white ring-primary/20' : 'bg-white text-slate-400 ring-slate-200') ?>">
                            <?= $done ? '✓' : ($idx + 1) ?>
                        </span>
                        <?php if ($idx < count($orderSteps) - 1): ?>
                            <span class="h-0.5 flex-1 <?= $done ? 'bg-primary' : 'bg-slate-200' ?>"></span>
                        <?php else: ?>
                            <span class="flex-1"></span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-2 text-xs font-bold <?= $active ? 'text-primary' : ($done ? 'text-secondary' : 'text-slate-400') ?>"><?= htmlspecialchars($step['label']) ?></p>
                    <p class="mt-0.5 hidden text-[10px] text-slate-400 sm:block"><?= htmlspecialchars($step['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
