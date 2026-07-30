<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
?>
<div class="space-y-6">
    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Monthly and yearly reports</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Profit report</h1>
                <p class="mt-1 text-sm text-slate-500"><?= e(date('M d, Y', strtotime($filters['startDate'] ?? 'now'))) ?> to <?= e(date('M d, Y', strtotime(($filters['endDate'] ?? 'now') . ' -1 second'))) ?></p>
            </div>
            <form method="GET" action="<?= e(url('/admin/reports')) ?>" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <select name="period" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="monthly" <?= ($filters['period'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="yearly" <?= ($filters['period'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                </select>
                <select name="month" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($months as $number => $name): ?><option value="<?= $number ?>" <?= (int) ($filters['month'] ?? 0) === $number ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                </select>
                <input name="year" type="number" min="2020" max="2100" value="<?= e($filters['year'] ?? date('Y')) ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Run</button>
            </form>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Gross Sales', 'value' => $money($summary['sales'] ?? 0), 'description' => number_format((int) ($summary['orders'] ?? 0)) . ' orders', 'icon' => 'banknotes']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Product Cost', 'value' => $money($summary['product_cost'] ?? 0), 'description' => 'Historical item cost', 'icon' => 'archive-box']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Gross Profit', 'value' => $money($summary['gross_profit'] ?? 0), 'description' => 'Sales minus product cost', 'icon' => 'chart-bar']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Total Expenses', 'value' => $money($summary['total_expenses'] ?? 0), 'description' => 'Approved and paid expenses', 'icon' => 'currency-dollar']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Net Profit', 'value' => $money($summary['net_profit'] ?? 0), 'description' => 'Gross profit minus expenses', 'icon' => 'chart-bar']); ?>
    </section>

    <section class="grid gap-6 lg:grid-cols-12">
        <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card lg:col-span-8">
            <div class="border-b border-slate-100 p-4"><h2 class="text-lg font-extrabold text-slate-950">Product profit</h2></div>
            <?php foreach (($products ?? []) as $product): ?>
                <div class="grid gap-2 border-b border-slate-100 p-4 text-sm sm:grid-cols-5">
                    <strong class="sm:col-span-2"><?= e($product['product_name']) ?></strong>
                    <span><?= (int) $product['quantity_sold'] ?> sold</span>
                    <span><?= e($money($product['revenue'])) ?></span>
                    <span class="font-bold text-primary"><?= e($money($product['product_profit'])) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?><div class="p-8 text-center text-sm text-slate-500">No product profit data for this period.</div><?php endif; ?>
        </div>
        <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card lg:col-span-4">
            <div class="border-b border-slate-100 p-4"><h2 class="text-lg font-extrabold text-slate-950">Expenses by category</h2></div>
            <?php foreach (($expenseRows ?? []) as $row): ?>
                <div class="flex justify-between border-b border-slate-100 p-4 text-sm"><span><?= e($row['category']) ?></span><strong><?= e($money($row['total'])) ?></strong></div>
            <?php endforeach; ?>
            <?php if (empty($expenseRows)): ?><div class="p-8 text-center text-sm text-slate-500">No approved expenses for this period.</div><?php endif; ?>
        </div>
    </section>
</div>
