<?php
declare(strict_types=1);
/** Finance summary cards — updated by filter via JS. */
?>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Total income</p>
        <p id="finance-summary-income" class="mt-2 text-2xl font-black text-secondary">LKR <?= number_format($totalSales, 2) ?></p>
        <p class="mt-1 text-xs text-emerald-700/70">From filtered delivered orders</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total expenses</p>
        <p id="finance-summary-expense" class="mt-2 text-2xl font-black text-secondary">LKR <?= number_format($totalCost, 2) ?></p>
        <p class="mt-1 text-xs text-slate-500">Product cost (filtered)</p>
    </div>
    <div class="rounded-2xl border border-primary/15 bg-primary/[0.04] p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Net profit</p>
        <p id="finance-summary-profit" class="mt-2 text-2xl font-black text-primary">LKR <?= number_format($totalProfit, 2) ?></p>
        <p id="finance-summary-margin" class="mt-1 text-xs text-primary/70"><?= number_format($margin, 1) ?>% margin</p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Orders</p>
        <p id="finance-summary-count" class="mt-2 text-2xl font-black text-secondary"><?= (int) $deliveredCount ?></p>
        <p class="mt-1 text-sm font-semibold text-slate-600">Matching current filter</p>
    </div>
</div>
