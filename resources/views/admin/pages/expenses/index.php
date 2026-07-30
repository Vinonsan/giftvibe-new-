<?php
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
?>
<div class="space-y-6">
    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Expense management</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Business expenses</h1>
                <p class="mt-1 text-sm text-slate-500">Total approved/paid expenses in this view: <strong><?= e($money($total ?? 0)) ?></strong></p>
            </div>
            <form method="GET" action="<?= e(url('/admin/expenses')) ?>" class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <select name="period" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="monthly" <?= ($filters['period'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="yearly" <?= ($filters['period'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                </select>
                <select name="month" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($months as $number => $name): ?><option value="<?= $number ?>" <?= (int) ($filters['month'] ?? 0) === $number ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                </select>
                <input name="year" type="number" min="2020" max="2100" value="<?= e($filters['year'] ?? date('Y')) ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All statuses</option>
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white lg:col-span-4">Apply filters</button>
            </form>
        </div>
    </section>

    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <h2 class="text-lg font-extrabold text-slate-950">Add expense</h2>
        <?php if (!empty($error)): ?><div class="mt-3 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(url('/admin/expenses')) ?>" enctype="multipart/form-data" class="mt-4 grid gap-4 md:grid-cols-2">
            <?= csrf_field() ?>
            <input name="title" required class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Expense title">
            <input name="amount" required type="number" min="0.01" step="0.01" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Amount">
            <select name="category" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                <?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?= e($category) ?></option><?php endforeach; ?>
            </select>
            <input name="expense_date" type="date" value="<?= e(date('Y-m-d')) ?>" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
            <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $status === 'approved' ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
            </select>
            <input name="receipt" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
            <textarea name="description" rows="3" class="rounded-button border border-slate-300 px-3 py-2 text-sm md:col-span-2" placeholder="Description or internal notes"></textarea>
            <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white md:col-span-2">Save expense</button>
        </form>
    </section>

    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <?php foreach (($expenses ?? []) as $expense): ?>
            <div class="grid gap-3 border-b border-slate-100 p-4 text-sm lg:grid-cols-6 lg:items-center">
                <div><strong class="text-slate-950"><?= e($expense['title']) ?></strong><p class="text-xs text-slate-400"><?= e($expense['expense_number']) ?></p></div>
                <div><?= e($expense['category']) ?><p class="text-xs text-slate-400"><?= e($expense['expense_date']) ?></p></div>
                <div class="font-bold text-primary"><?= e($money($expense['amount'])) ?></div>
                <div><?= e(trim(($expense['first_name'] ?? '') . ' ' . ($expense['last_name'] ?? '')) ?: 'Admin') ?></div>
                <div>
                    <?php if (!empty($expense['receipt_path'])): ?><a href="<?= e(url('/admin/expenses/' . $expense['id'] . '/receipt')) ?>" target="_blank" rel="noopener" class="font-bold text-primary">Receipt</a><?php else: ?><span class="text-slate-400">No receipt</span><?php endif; ?>
                </div>
                <form method="POST" action="<?= e(url('/admin/expenses/' . $expense['id'] . '/status')) ?>" class="flex gap-2">
                    <?= csrf_field() ?>
                    <select name="status" class="min-w-0 rounded-button border border-slate-300 px-2 py-2 text-xs">
                        <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $expense['status'] === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                    </select>
                    <button class="rounded-button bg-slate-900 px-3 py-2 text-xs font-bold text-white">Save</button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if (empty($expenses)): ?><div class="p-8 text-center text-sm text-slate-500">No expenses found.</div><?php endif; ?>
    </section>
</div>
