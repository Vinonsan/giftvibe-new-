<?php
declare(strict_types=1);
/** 6-month income vs expenses chart — real aggregated data. */

$maxVal = max(array_merge(array_column($monthlyReport, 'sales'), [1]));
?>
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-secondary">6-month income vs expenses</h3>
            <p class="mt-0.5 text-xs text-slate-500">Placed orders grouped by month (cancelled/refunded excluded).</p>
        </div>
        <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Last 6 months</span>
    </div>

    <div class="flex h-56 items-end gap-3 border-b border-slate-100 pt-6 sm:gap-6">
        <?php foreach ($monthlyReport as $data):
            $saleHeight = ($data['sales'] / $maxVal) * 100;
            $costHeight = ($data['cost'] / $maxVal) * 100;
            $profitHeight = max(0, ($data['profit'] / $maxVal) * 100);
        ?>
            <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                <div class="flex h-full w-full items-end justify-center gap-1.5">
                    <div style="height: <?= $costHeight ?>%" class="w-2.5 rounded-t-md bg-slate-200/80 transition-all hover:bg-slate-300" title="Expenses: LKR <?= number_format($data['cost'], 2) ?>"></div>
                    <div style="height: <?= $profitHeight ?>%" class="w-2.5 rounded-t-md bg-primary shadow-sm shadow-primary/20 transition-all hover:opacity-90" title="Profit: LKR <?= number_format($data['profit'], 2) ?>"></div>
                    <div style="height: <?= $saleHeight ?>%" class="w-2.5 rounded-t-md bg-secondary transition-all hover:bg-secondary/80" title="Income: LKR <?= number_format($data['sales'], 2) ?>"></div>
                </div>
                <span class="truncate text-[10px] font-bold text-slate-400"><?= htmlspecialchars($data['month']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-center gap-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 sm:gap-6">
        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-slate-200"></span> Expenses</span>
        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-primary"></span> Net profit</span>
        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-secondary"></span> Income</span>
    </div>
</div>
