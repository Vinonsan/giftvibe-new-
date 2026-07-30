<?php
$summary = $summary ?? [];
$filters = $filters ?? ['period' => 'monthly', 'month' => (int) date('n'), 'year' => (int) date('Y')];
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];
?>
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Admin Dashboard</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">GiftVibe.lk Performance</h1>
            <p class="mt-1 text-sm text-slate-500">Track sales, operations, customers, and stock health from live database summaries.</p>
        </div>

        <form method="GET" action="<?= e(url('/admin/dashboard')) ?>" class="grid grid-cols-1 gap-3 sm:grid-cols-4 lg:w-auto">
            <div>
                <label for="period" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Period</label>
                <select id="period" name="period" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="monthly" <?= ($filters['period'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="yearly" <?= ($filters['period'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                </select>
            </div>
            <div>
                <label for="month" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Month</label>
                <select id="month" name="month" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <?php foreach ($months as $number => $name): ?>
                        <option value="<?= $number ?>" <?= (int) ($filters['month'] ?? 0) === $number ? 'selected' : '' ?>><?= e($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="year" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Year</label>
                <input id="year" name="year" type="number" min="2020" max="2100" value="<?= e($filters['year'] ?? date('Y')) ?>" class="w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
            </div>
            <button type="submit" class="self-end rounded-button bg-primary px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Sales', 'value' => $money($summary['sales'] ?? 0), 'description' => 'Revenue in selected period', 'icon' => 'banknotes']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Orders', 'value' => number_format((int) ($summary['orders'] ?? 0)), 'description' => 'Orders created in selected period', 'icon' => 'shopping-bag']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Gross Profit', 'value' => $money($summary['gross_profit'] ?? 0), 'description' => 'Sales minus product cost', 'icon' => 'chart-bar']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Total Expenses', 'value' => $money($summary['total_expenses'] ?? 0), 'description' => 'Approved business expenses', 'icon' => 'currency-dollar']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Net Profit', 'value' => $money($summary['net_profit'] ?? 0), 'description' => 'Gross profit minus expenses', 'icon' => 'chart-bar']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Customers', 'value' => number_format((int) ($summary['customers'] ?? 0)), 'description' => number_format((int) ($summary['ordering_customers'] ?? 0)) . ' ordered in this period', 'icon' => 'users']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Low Stock', 'value' => number_format((int) ($summary['low_stock'] ?? 0)), 'description' => 'Products at or below threshold', 'icon' => 'archive-box']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Out of Stock', 'value' => number_format((int) ($summary['out_of_stock'] ?? 0)), 'description' => 'Products unavailable to sell', 'icon' => 'warning']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Reviews', 'value' => number_format((int) ($summary['pending_reviews'] ?? 0)), 'description' => 'Pending approval', 'icon' => 'star']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Messages', 'value' => number_format((int) ($summary['messages'] ?? 0)), 'description' => 'Open contact messages', 'icon' => 'mail']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Gift Requests', 'value' => number_format((int) ($summary['custom_requests'] ?? 0)), 'description' => 'Active custom requests', 'icon' => 'gift']); ?>
        <?php component('admin/components/dashboard/stat-card', ['title' => 'Notifications', 'value' => number_format((int) ($summary['notifications'] ?? 0)), 'description' => 'Unread admin alerts', 'icon' => 'bell']); ?>
    </div>

    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Parcel status</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-950">Delivery dashboard counts</h2>
            </div>
            <a href="<?= e(url('/admin/deliveries')) ?>" class="text-sm font-bold text-primary">Manage deliveries</a>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-4 lg:grid-cols-5">
            <?php foreach (($summary['parcel_counts'] ?? []) as $status => $total): ?>
                <a href="<?= e(url('/admin/deliveries?status=' . $status)) ?>" class="rounded-button border border-slate-200 bg-slate-50 p-4 hover:border-primary-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e(ucwords(str_replace('_', ' ', $status))) ?></span>
                    <strong class="mt-1 block text-2xl text-slate-950"><?= (int) $total ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
