<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$shortMoney = static function ($amount): string {
    $value = (float) $amount;
    if (abs($value) >= 1000000) {
        return 'LKR ' . number_format($value / 1000000, 1) . 'M';
    }
    if (abs($value) >= 1000) {
        return 'LKR ' . number_format($value / 1000, 0) . 'K';
    }
    return 'LKR ' . number_format($value, 0);
};
$months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
$periodStart = date('M d, Y', strtotime($filters['startDate'] ?? 'now'));
$periodEnd = date('M d, Y', strtotime(($filters['endDate'] ?? 'now') . ' -1 second'));
$selectedYear = (int) ($filters['year'] ?? date('Y'));
$selectedMonth = (int) ($filters['month'] ?? date('n'));
$series = $monthlySeries ?? [];
$maxMoney = max(1, ...array_map(static fn ($row) => max((float) $row['sales'], (float) $row['expenses'], (float) $row['net_profit']), $series));
$maxOrders = max(1, ...array_map(static fn ($row) => (int) $row['orders'], $series));
$point = static function (array $series, string $key, float $max, int $width = 520, int $height = 160): string {
    $points = [];
    $count = max(1, count($series) - 1);
    foreach ($series as $index => $row) {
        $x = 20 + (($width - 40) / $count) * $index;
        $value = max(0, (float) ($row[$key] ?? 0));
        $y = 15 + ($height - 30) - (($value / $max) * ($height - 30));
        $points[] = number_format($x, 2, '.', '') . ',' . number_format($y, 2, '.', '');
    }
    return implode(' ', $points);
};
$pct = static function ($current, $previous): array {
    $current = (float) $current;
    $previous = (float) $previous;
    if ($previous <= 0) {
        return [$current > 0 ? '+100.0%' : '0.0%', $current >= $previous];
    }
    $change = (($current - $previous) / $previous) * 100;
    return [($change >= 0 ? '+' : '') . number_format($change, 1) . '%', $change >= 0];
};
$metricCards = [
    ['title' => 'Total Sales', 'value' => $summary['sales'] ?? 0, 'previous' => $previousSummary['sales'] ?? 0, 'icon' => 'currency-dollar', 'tone' => 'violet'],
    ['title' => 'Total Expenses', 'value' => $summary['total_expenses'] ?? 0, 'previous' => $previousSummary['total_expenses'] ?? 0, 'icon' => 'banknotes', 'tone' => 'emerald'],
    ['title' => 'Gross Profit', 'value' => $summary['gross_profit'] ?? 0, 'previous' => $previousSummary['gross_profit'] ?? 0, 'icon' => 'chart-bar', 'tone' => 'sky'],
    ['title' => 'Net Profit', 'value' => $summary['net_profit'] ?? 0, 'previous' => $previousSummary['net_profit'] ?? 0, 'icon' => 'archive-box', 'tone' => 'orange'],
];
$toneClasses = [
    'violet' => 'border-violet-200 bg-violet-50/40 text-violet-700',
    'emerald' => 'border-emerald-200 bg-emerald-50/50 text-emerald-700',
    'sky' => 'border-sky-200 bg-sky-50/50 text-sky-700',
    'orange' => 'border-orange-200 bg-orange-50/50 text-orange-700',
    'red' => 'border-red-200 bg-red-50/50 text-red-600',
];
?>
<div class="space-y-6">
    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-950">Reports</h1>
            <span class="sr-only">Profit report</span>
            <p class="mt-1 text-base font-medium text-slate-500">Track your business performance</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" action="<?= e(url('/admin/reports')) ?>" class="flex flex-wrap gap-3">
                <select name="period" class="min-h-11 rounded-button border border-slate-200 bg-white px-4 text-sm font-bold text-slate-900 shadow-card">
                    <option value="monthly" <?= ($filters['period'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="yearly" <?= ($filters['period'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                </select>
                <select name="month" class="min-h-11 rounded-button border border-slate-200 bg-white px-4 text-sm font-bold text-slate-900 shadow-card">
                    <?php foreach ($months as $number => $name): ?><option value="<?= $number ?>" <?= $selectedMonth === $number ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                </select>
                <input name="year" type="number" min="2020" max="2100" value="<?= e((string) $selectedYear) ?>" class="min-h-11 w-28 rounded-button border border-slate-200 bg-white px-4 text-sm font-bold text-slate-900 shadow-card">
                <button class="min-h-11 rounded-button bg-primary px-5 text-sm font-bold text-white shadow-card">Run</button>
            </form>
            <button type="button" class="inline-flex min-h-11 items-center gap-2 rounded-button bg-indigo-600 px-5 text-sm font-bold text-white shadow-card">
                <?php component('admin/components/common/icon', ['name' => 'archive-box', 'size' => 'sm']); ?>
                Export Report
            </button>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($metricCards as $card): ?>
            <?php [$change, $positive] = $pct($card['value'], $card['previous']); ?>
            <article class="rounded-card border bg-white p-5 shadow-card <?= e($toneClasses[$card['tone']]) ?>">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/75">
                        <?php component('admin/components/common/icon', ['name' => $card['icon'], 'size' => 'lg']); ?>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-extrabold text-slate-700"><?= e($card['title']) ?></p>
                        <p class="mt-2 break-words text-2xl font-extrabold text-slate-950"><?= e($money($card['value'])) ?></p>
                        <p class="mt-2 text-sm font-bold <?= $positive ? 'text-emerald-600' : 'text-red-600' ?>"><?= e($change) ?></p>
                        <p class="mt-1 text-xs font-medium text-slate-500">vs previous period</p>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-extrabold text-slate-950">Profit Overview</h2>
                <span class="rounded-button border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600"><?= e((string) $selectedYear) ?></span>
            </div>
            <div class="mt-5 flex flex-wrap gap-5 text-xs font-bold text-slate-500">
                <span class="inline-flex items-center gap-2"><span class="h-0.5 w-7 bg-indigo-600"></span>Sales</span>
                <span class="inline-flex items-center gap-2"><span class="h-0.5 w-7 bg-red-500"></span>Expenses</span>
                <span class="inline-flex items-center gap-2"><span class="h-0.5 w-7 bg-emerald-500"></span>Net Profit</span>
            </div>
            <div class="mt-5 overflow-hidden rounded-button bg-slate-50 p-3">
                <svg viewBox="0 0 520 190" class="h-64 w-full" role="img" aria-label="Profit overview chart">
                    <?php for ($line = 0; $line <= 4; $line++): $y = 15 + ($line * 35); ?>
                        <line x1="20" y1="<?= $y ?>" x2="500" y2="<?= $y ?>" stroke="#e2e8f0" stroke-width="1" />
                    <?php endfor; ?>
                    <polyline points="<?= e($point($series, 'sales', $maxMoney)) ?>" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="<?= e($point($series, 'expenses', $maxMoney)) ?>" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <polyline points="<?= e($point($series, 'net_profit', $maxMoney)) ?>" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <?php foreach ($series as $index => $row): $x = 20 + ((480 / 11) * $index); ?>
                        <text x="<?= number_format($x, 1) ?>" y="184" text-anchor="middle" fill="#475569" font-size="11"><?= e($row['label']) ?></text>
                    <?php endforeach; ?>
                </svg>
            </div>
        </article>

        <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-extrabold text-slate-950">Orders Overview</h2>
                <span class="rounded-button border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600">Monthly</span>
            </div>
            <div class="mt-5 overflow-hidden rounded-button bg-slate-50 p-3">
                <svg viewBox="0 0 520 190" class="h-64 w-full" role="img" aria-label="Orders overview chart">
                    <?php for ($line = 0; $line <= 4; $line++): $y = 15 + ($line * 35); ?>
                        <line x1="20" y1="<?= $y ?>" x2="500" y2="<?= $y ?>" stroke="#e2e8f0" stroke-width="1" />
                    <?php endfor; ?>
                    <polygon points="20,160 <?= e($point($series, 'orders', $maxOrders, 520, 160)) ?> 500,160" fill="#ddd6fe" opacity="0.65" />
                    <polyline points="<?= e($point($series, 'orders', $maxOrders, 520, 160)) ?>" fill="none" stroke="#6d28d9" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <?php foreach ($series as $index => $row): $x = 20 + ((480 / 11) * $index); ?>
                        <text x="<?= number_format($x, 1) ?>" y="184" text-anchor="middle" fill="#475569" font-size="11"><?= e($row['label']) ?></text>
                    <?php endforeach; ?>
                </svg>
            </div>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Orders Summary</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <?php
                    $orderCards = [
                        ['title' => 'Total Orders', 'value' => $orderSummary['total'] ?? 0, 'tone' => 'violet', 'icon' => 'shopping-bag'],
                        ['title' => 'Delivered Orders', 'value' => $orderSummary['delivered'] ?? 0, 'tone' => 'sky', 'icon' => 'check'],
                        ['title' => 'Processing Orders', 'value' => $orderSummary['processing'] ?? 0, 'tone' => 'orange', 'icon' => 'archive-box'],
                        ['title' => 'Cancelled Orders', 'value' => $orderSummary['cancelled'] ?? 0, 'tone' => 'red', 'icon' => 'close'],
                    ];
                ?>
                <?php foreach ($orderCards as $card): ?>
                    <div class="rounded-card border p-4 <?= e($toneClasses[$card['tone']]) ?>">
                        <div class="flex h-10 w-10 items-center justify-center rounded-button bg-white/75">
                            <?php component('admin/components/common/icon', ['name' => $card['icon'], 'size' => 'sm']); ?>
                        </div>
                        <p class="mt-5 text-2xl font-extrabold text-slate-950"><?= (int) $card['value'] ?></p>
                        <p class="mt-1 text-sm font-semibold text-slate-700"><?= e($card['title']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Orders by Month</h2>
            <div class="mt-6 flex h-56 items-end gap-3 rounded-button bg-slate-50 px-4 pb-8 pt-4">
                <?php foreach ($series as $row): ?>
                    <?php $height = max(6, ((int) $row['orders'] / $maxOrders) * 100); ?>
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <div class="w-full max-w-7 rounded-t-button bg-violet-300" style="height: <?= e(number_format($height, 2, '.', '')) ?>%"></div>
                        <span class="text-[11px] font-semibold text-slate-500"><?= e($row['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
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
