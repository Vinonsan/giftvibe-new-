<?php
declare(strict_types=1);
?>
<div class="space-y-5">

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Business Expenses</h1>
            <p class="mt-1 text-sm text-slate-500">Record non-product operating costs such as packing, delivery, marketing, rent and salary. These reduce cash on hand.</p>
        </div>
        <button type="button" data-drawer-open="expense-drawer"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary transition cursor-pointer shadow-sm shadow-primary/20">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add expense
        </button>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Current cash on hand</p>
            <p class="mt-2 text-2xl font-black text-emerald-700">LKR <?= number_format((float) $cashOnHand, 2) ?></p>
            <p class="mt-1 text-xs text-emerald-700/70">Sales + investments − external product purchases − expenses</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total recorded</p>
            <p class="mt-2 text-2xl font-black text-secondary">LKR <?= number_format($totalAmount, 2) ?></p>
            <p class="mt-1 text-xs text-slate-500"><?= count($expenses) ?> entries</p>
        </div>
        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Approved (counts in dashboard)</p>
            <p class="mt-2 text-2xl font-black text-rose-700">LKR <?= number_format($approvedTotal, 2) ?></p>
            <p class="mt-1 text-xs text-rose-700/70">Deducted from cash on hand</p>
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

<?php
$deleteModalId = 'expense-delete-modal';
$deleteFormAction = '/admin/expenses';
$deleteItemLabel = 'expense';
$deleteCsrfToken = $csrfToken;
require BASE_PATH . '/resources/views/components/modal/delete-confirm.php';
?>
