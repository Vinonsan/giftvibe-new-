<?php
declare(strict_types=1);

/** @var array $stats */

$money = static fn(float $n): string => 'LKR ' . number_format($n, 2);

$orderBadge = static function (string $status): string {
    $class = match ($status) {
        'delivered', 'confirmed' => 'bg-emerald-50 text-emerald-700',
        'cancelled', 'refunded' => 'bg-rose-50 text-rose-700',
        default => 'bg-amber-50 text-amber-700',
    };
    return '<span class="rounded-full px-2 py-0.5 text-[10px] font-bold ' . $class . '">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $status))) . '</span>';
};
?>
<section class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-secondary">Dashboard</h2>
        <p class="text-sm text-slate-500">Business overview — cash, profit, and recent activity from orders and expenses.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Cash on hand</p>
            <p class="mt-2 text-2xl font-black text-secondary"><?= $money((float) $stats['cashOnHand']) ?></p>
            <p class="mt-1 text-xs text-emerald-700/70">Delivered income − business expenses</p>
        </div>
        <div class="rounded-2xl border border-primary/15 bg-primary/[0.04] p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Net profit</p>
            <p class="mt-2 text-2xl font-black text-primary"><?= $money((float) $stats['netProfit']) ?></p>
            <p class="mt-1 text-xs text-primary/70"><?= number_format((float) $stats['margin'], 1) ?>% margin · after all costs</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total revenue</p>
            <p class="mt-2 text-2xl font-black text-secondary"><?= $money((float) $stats['totalIncome']) ?></p>
            <p class="mt-1 text-xs text-slate-500"><?= (int) $stats['deliveredCount'] ?> delivered orders</p>
        </div>
        <div class="rounded-2xl border border-rose-100 bg-rose-50/40 p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Business expenses</p>
            <p class="mt-2 text-2xl font-black text-rose-700"><?= $money((float) $stats['businessExpenses']) ?></p>
            <p class="mt-1 text-xs text-rose-700/70"><?= (int) $stats['expenseCount'] ?> entries · <a href="/admin/expenses" class="font-semibold underline">Manage</a></p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Products</p>
            <p class="mt-1 text-2xl font-extrabold text-secondary"><?= (int) $stats['productCount'] ?></p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Customers</p>
            <p class="mt-1 text-2xl font-extrabold text-secondary"><?= (int) $stats['customerCount'] ?></p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Pending orders</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-600"><?= (int) $stats['pendingOrders'] ?></p>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Gross profit</p>
            <p class="mt-1 text-2xl font-extrabold text-secondary"><?= $money((float) $stats['grossProfit']) ?></p>
            <p class="text-xs text-slate-400">Revenue − product cost</p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-secondary">Recent orders</h3>
                <a href="/admin/orders" class="text-xs font-semibold text-primary hover:underline">View all</a>
            </div>
            <?php if (empty($stats['recentOrders'])): ?>
                <p class="text-sm text-slate-400">No orders yet.</p>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($stats['recentOrders'] as $order): ?>
                        <a href="/admin/orders/view?id=<?= (int) $order['id'] ?>" class="flex items-center justify-between gap-3 py-3 transition hover:bg-slate-50/80 -mx-2 px-2 rounded-lg">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-secondary"><?= htmlspecialchars((string) $order['order_number']) ?></p>
                                <p class="truncate text-xs text-slate-500"><?= htmlspecialchars((string) $order['customer_name']) ?></p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-secondary"><?= $money((float) $order['grand_total']) ?></p>
                                <?= $orderBadge((string) $order['order_status']) ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-secondary">Recent expenses</h3>
                <a href="/admin/expenses" class="text-xs font-semibold text-primary hover:underline">View all</a>
            </div>
            <?php if (empty($stats['recentExpenses'])): ?>
                <p class="text-sm text-slate-400">No expenses yet. <a href="/admin/expenses" class="font-semibold text-primary hover:underline">Add product purchase</a></p>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($stats['recentExpenses'] as $expense): ?>
                        <div class="flex items-center justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-secondary"><?= htmlspecialchars((string) $expense['title']) ?></p>
                                <p class="truncate text-xs text-slate-500"><?= htmlspecialchars((string) $expense['expense_date']) ?> · <?= htmlspecialchars((string) $expense['expense_number']) ?></p>
                            </div>
                            <p class="shrink-0 text-sm font-bold text-rose-600"><?= $money((float) $expense['amount']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="/admin/orders/create" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary transition">Create order</a>
        <a href="/admin/expenses" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Add expense</a>
        <a href="/admin/finance" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Financial reports</a>
    </div>
</section>
