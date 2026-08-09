<?php
declare(strict_types=1);
?>
<div class="space-y-5">

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Inventory</h1>
            <p class="mt-1 text-sm text-slate-500">Products sync automatically with buying price, selling price and live stock. Confirmed sales reduce stock once.</p>
        </div>
        <button type="button" data-drawer-open="inventory-item-drawer"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary transition shadow-sm shadow-primary/20">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add stock item
        </button>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total units in stock</p>
            <p class="mt-2 text-2xl font-black text-secondary"><?= number_format((int) $totalUnits) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Items tracked</p>
            <p class="mt-2 text-2xl font-black text-secondary"><?= count($items) ?></p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Low stock alerts</p>
            <p class="mt-2 text-2xl font-black text-amber-700"><?= (int) $lowStockCount ?></p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_filter.php'; ?>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_datatable.php'; ?>
    </div>

</div>

<?php require __DIR__ . '/components/_drawer.php'; ?>
<?php require __DIR__ . '/components/_adjust-drawer.php'; ?>

<?php
$deleteModalId = 'inventory-delete-modal';
$deleteFormAction = '/admin/inventory';
$deleteItemLabel = 'stock item';
$deleteCsrfToken = $csrfToken;
require BASE_PATH . '/resources/views/components/modal/delete-confirm.php';
?>
