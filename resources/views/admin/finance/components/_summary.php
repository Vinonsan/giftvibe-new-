<?php
declare(strict_types=1);
/** Finance summary cards — updated by filter via JS. */
?>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Current cash on hand</p>
        <p class="mt-2 text-2xl font-black text-emerald-700">LKR <?= number_format($cashOnHand, 2) ?></p>
        <p class="mt-1 text-xs text-emerald-700/70">Sales + investments − external purchases − expenses</p>
    </div>
    <div class="rounded-2xl border border-amber-100 bg-amber-50/40 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">External product purchases</p>
        <p class="mt-2 text-2xl font-black text-amber-700">LKR <?= number_format($productPurchases, 2) ?></p>
        <p class="mt-1 text-xs text-amber-700/70">Deducted from cash when purchased products are added</p>
    </div>
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5">
        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Total income</p>
        <p id="finance-summary-income" class="mt-2 text-2xl font-black text-secondary">LKR <?= number_format($totalSales, 2) ?></p>
        <p class="mt-1 text-xs text-emerald-700/70">From placed orders excluding cancelled/refunded</p>
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
