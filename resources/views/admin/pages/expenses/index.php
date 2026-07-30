<?php
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
?>
<div class="space-y-6">
    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Expense management</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Business expenses</h1>
                <p class="mt-1 text-sm text-slate-500">Total expenses in this view: <strong><?= e($money($total ?? 0)) ?></strong></p>
                <?php if (!empty($error)): ?><div class="mt-3 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
            </div>
            <div class="flex justify-end lg:-mt-1 lg:self-center">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-button border border-primary-100 bg-primary-50 px-4 text-sm font-bold text-primary-900 transition hover:bg-primary-900 hover:text-white" data-payback-toggle>
                        Pending paybacks
                    </button>
                    <button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-button bg-primary px-5 text-sm font-bold text-white shadow-card transition hover:bg-primary-800" data-expense-add>
                        <span class="text-lg leading-none">+</span>
                        Add expense
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section>
        <form method="GET" action="<?= e(url('/admin/expenses')) ?>" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <select name="period" class="min-h-11 rounded-button border border-slate-300 px-3 text-sm font-semibold">
                <option value="monthly" <?= ($filters['period'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="yearly" <?= ($filters['period'] ?? '') === 'yearly' ? 'selected' : '' ?>>Yearly</option>
            </select>
            <select name="month" class="min-h-11 rounded-button border border-slate-300 px-3 text-sm font-semibold">
                <?php foreach ($months as $number => $name): ?><option value="<?= $number ?>" <?= (int) ($filters['month'] ?? 0) === $number ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
            </select>
            <input name="year" type="number" min="2020" max="2100" value="<?= e($filters['year'] ?? date('Y')) ?>" class="min-h-11 rounded-button border border-slate-300 px-3 text-sm font-semibold">
            <button class="rounded-button bg-slate-900 px-4 py-2 text-sm font-bold text-white">Apply filters</button>
        </form>
    </section>

    <div class="fixed inset-0 z-[9998] hidden bg-primary-950/50 opacity-0 backdrop-blur-sm transition-opacity duration-200" data-expense-drawer-overlay></div>
    <aside class="fixed inset-y-0 right-0 z-[9999] flex h-screen h-dvh w-full translate-x-full flex-col border-l border-primary-900/10 bg-white shadow-[0_24px_80px_-30px_rgba(12,43,78,0.45)] transition-transform duration-300 ease-out sm:max-w-[38rem]" data-expense-drawer aria-hidden="true">
        <div class="border-b border-primary-900/10 px-5 py-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Finance</p>
                    <h2 class="mt-1 text-xl font-extrabold tracking-tight text-primary-900">Add expense</h2>
                </div>
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-500 shadow-card transition hover:text-primary-900" data-expense-drawer-close aria-label="Close expense drawer">
                    <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
                </button>
            </div>
        </div>
        <form method="POST" action="<?= e(url('/admin/expenses')) ?>" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col" data-expense-form>
        <div class="flex-1 space-y-5 overflow-y-auto px-5 py-6">
            <?= csrf_field() ?>
            <div class="rounded-card border border-slate-200 bg-slate-50 p-3">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Expense type</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="cursor-pointer rounded-button border border-primary-200 bg-white p-3 text-sm font-bold text-primary-900 ring-2 ring-primary-100" data-expense-scope-card>
                        <input type="radio" name="expense_scope" value="general" checked class="sr-only" data-expense-scope>
                        General expense
                        <span class="mt-1 block text-xs font-medium text-slate-500">Common business cost</span>
                    </label>
                    <label class="cursor-pointer rounded-button border border-slate-200 bg-white p-3 text-sm font-bold text-slate-700" data-expense-scope-card>
                        <input type="radio" name="expense_scope" value="order" class="sr-only" data-expense-scope>
                        Specific order
                        <span class="mt-1 block text-xs font-medium text-slate-500">Cost belongs to one order</span>
                    </label>
                </div>
            </div>

            <div class="hidden space-y-2" data-order-expense-field>
                <label for="expense-order-id" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Select order</label>
                <select id="expense-order-id" name="order_id" class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10">
                    <option value="">Choose order</option>
                    <?php foreach (($orders ?? []) as $order): ?>
                        <option value="<?= e((string) $order['id']) ?>">
                            <?= e($order['order_number'] . ' - ' . $order['customer_name'] . ' - ' . $money($order['grand_total'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Heading</label>
                <input name="title" required class="min-h-12 w-full rounded-button border border-slate-300 px-3 text-sm font-semibold" placeholder="Expense heading">
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Description</label>
                <textarea name="description" rows="4" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="What was this expense for?"></textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <input name="amount" required type="number" min="0.01" step="0.01" class="min-h-12 rounded-button border border-slate-300 px-3 text-sm font-semibold" placeholder="Amount">
                <input name="expense_date" type="date" value="<?= e(date('Y-m-d')) ?>" class="min-h-12 rounded-button border border-slate-300 px-3 text-sm font-semibold">
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Expense category</label>
                <select name="category" class="min-h-12 w-full rounded-button border border-slate-300 px-3 text-sm font-semibold" data-expense-category>
                    <?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?= e($category) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="hidden space-y-2" data-category-other-field>
                <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Other expense name</label>
                <input name="category_other" class="min-h-12 w-full rounded-button border border-slate-300 px-3 text-sm font-semibold" placeholder="Type the expense type">
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Bill / receipt</label>
                <input name="receipt" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="min-h-12 w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div class="rounded-card border border-slate-200 bg-slate-50 p-3">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Who paid?</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="cursor-pointer rounded-button border border-primary-200 bg-white p-3 text-sm font-bold text-primary-900 ring-2 ring-primary-100" data-paid-source-card>
                        <input type="radio" name="paid_source" value="company_cash" checked class="sr-only" data-paid-source>
                        Company cash
                        <span class="mt-1 block text-xs font-medium text-slate-500">Paid directly from company money</span>
                    </label>
                    <label class="cursor-pointer rounded-button border border-slate-200 bg-white p-3 text-sm font-bold text-slate-700" data-paid-source-card>
                        <input type="radio" name="paid_source" value="admin" class="sr-only" data-paid-source>
                        Admin / person paid
                        <span class="mt-1 block text-xs font-medium text-slate-500">Needs reimbursement tracking</span>
                    </label>
                </div>
            </div>

            <div class="hidden space-y-4" data-admin-paid-fields>
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Paid by</label>
                    <select name="paid_by_admin_id" class="min-h-12 w-full rounded-button border border-slate-300 px-3 text-sm font-semibold">
                        <option value="">Select admin / person</option>
                        <?php foreach (($admins ?? []) as $admin): ?>
                            <option value="<?= e((string) $admin['id']) ?>"><?= e(trim($admin['first_name'] . ' ' . ($admin['last_name'] ?? '')) . ' - ' . $admin['email']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Reimbursement</label>
                    <select name="reimbursement_status" class="min-h-12 w-full rounded-button border border-slate-300 px-3 text-sm font-semibold">
                        <option value="pending">Not paid back yet</option>
                        <option value="reimbursed">Paid back</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-end border-t border-primary-900/10 bg-white px-5 py-4 shadow-[0_-18px_54px_-44px_rgba(12,43,78,0.35)]">
            <button class="inline-flex min-h-10 min-w-28 items-center justify-center rounded-button bg-primary px-5 text-sm font-bold text-white">Save</button>
        </div>
        </form>
    </aside>

    <div class="fixed inset-0 z-[9998] hidden bg-primary-950/50 opacity-0 backdrop-blur-sm transition-opacity duration-200" data-payback-drawer-overlay></div>
    <aside class="fixed inset-y-0 right-0 z-[9999] flex h-screen h-dvh w-full translate-x-full flex-col border-l border-primary-900/10 bg-white shadow-[0_24px_80px_-30px_rgba(12,43,78,0.45)] transition-transform duration-300 ease-out sm:max-w-[38rem]" data-payback-drawer aria-hidden="true">
        <div class="border-b border-primary-900/10 px-5 py-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Finance</p>
                    <h2 class="mt-1 text-xl font-extrabold tracking-tight text-primary-900">Pending paybacks</h2>
                </div>
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-500 shadow-card transition hover:text-primary-900" data-payback-drawer-close aria-label="Close payback drawer">
                    <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-6">
            <?php if (!empty($paybackSummary)): ?>
                <div class="space-y-3">
                    <?php foreach ($paybackSummary as $payback): ?>
                        <div class="rounded-card border border-slate-200 bg-white p-4 shadow-card">
                            <p class="text-sm font-extrabold text-slate-950"><?= e($payback['name']) ?></p>
                            <p class="mt-2 text-2xl font-extrabold text-primary"><?= e($money($payback['amount'])) ?></p>
                            <p class="mt-1 text-xs font-semibold text-slate-500"><?= (int) $payback['count'] ?> unpaid expense<?= (int) $payback['count'] === 1 ? '' : 's' ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="rounded-button border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">No pending admin/person paybacks in this view.</p>
            <?php endif; ?>
        </div>
    </aside>

    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
       

        <div class="hidden grid-cols-6 gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500 lg:grid">
            <div>Expense</div>
            <div>Category / Date</div>
            <div>Amount</div>
            <div>Paid by</div>
            <div>Bill</div>
            <div>Payback</div>
        </div>
        <?php foreach (($expenses ?? []) as $expense): ?>
            <div class="grid gap-3 border-b border-slate-100 p-4 text-sm lg:grid-cols-6 lg:items-center">
                <div>
                    <strong class="text-slate-950"><?= e($expense['title']) ?></strong>
                    <p class="text-xs text-slate-400"><?= e($expense['expense_number']) ?></p>
                    <?php if (!empty($expense['order_number'])): ?>
                        <a href="<?= e(url('/admin/orders/' . $expense['order_id'])) ?>" class="mt-1 inline-flex rounded-button border border-primary-100 bg-primary-50 px-2 py-1 text-xs font-bold text-primary-900">
                            <?= e($expense['order_number']) ?> - <?= e($expense['order_customer_name'] ?? 'Customer') ?>
                        </a>
                    <?php else: ?>
                        <span class="mt-1 inline-flex rounded-button border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-bold text-slate-500">General</span>
                    <?php endif; ?>
                </div>
                <div><?= e($expense['category']) ?><p class="text-xs text-slate-400"><?= e($expense['expense_date']) ?></p></div>
                <div class="font-bold text-primary"><?= e($money($expense['amount'])) ?></div>
                <div>
                    <?php if (($expense['paid_source'] ?? 'company_cash') === 'admin'): ?>
                        <span class="font-bold text-slate-950"><?= e(trim(($expense['paid_first_name'] ?? '') . ' ' . ($expense['paid_last_name'] ?? '')) ?: 'Admin') ?></span>
                        <p class="text-xs text-slate-400">Paid by person</p>
                    <?php else: ?>
                        <span class="font-bold text-slate-950">Company cash</span>
                        <p class="text-xs text-slate-400">No reimbursement</p>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!empty($expense['receipt_path'])): ?><a href="<?= e(url('/admin/expenses/' . $expense['id'] . '/receipt')) ?>" target="_blank" rel="noopener" class="font-bold text-primary">Receipt</a><?php else: ?><span class="text-slate-400">No receipt</span><?php endif; ?>
                </div>
                <div>
                    <?php if (($expense['paid_source'] ?? 'company_cash') === 'admin'): ?>
                        <form method="POST" action="<?= e(url('/admin/expenses/' . $expense['id'] . '/reimbursement')) ?>" class="flex gap-2">
                            <?= csrf_field() ?>
                            <select name="reimbursement_status" class="min-w-0 rounded-button border border-slate-300 px-2 py-2 text-xs">
                                <option value="pending" <?= ($expense['reimbursement_status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Not paid back</option>
                                <option value="reimbursed" <?= ($expense['reimbursement_status'] ?? '') === 'reimbursed' ? 'selected' : '' ?>>Paid back</option>
                            </select>
                            <button class="rounded-button bg-slate-900 px-3 py-2 text-xs font-bold text-white">Update</button>
                        </form>
                    <?php else: ?>
                        <span class="rounded-button border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-bold text-slate-500">Company paid</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($expenses)): ?><div class="p-8 text-center text-sm text-slate-500">No expenses found.</div><?php endif; ?>
    </section>
</div>

<script>
(() => {
    const drawer = document.querySelector('[data-expense-drawer]');
    const overlay = document.querySelector('[data-expense-drawer-overlay]');
    const addButton = document.querySelector('[data-expense-add]');
    const closeButtons = document.querySelectorAll('[data-expense-drawer-close]');
    const orderField = document.querySelector('[data-order-expense-field]');
    const scopeInputs = document.querySelectorAll('[data-expense-scope]');
    const scopeCards = document.querySelectorAll('[data-expense-scope-card]');
    const category = document.querySelector('[data-expense-category]');
    const categoryOtherField = document.querySelector('[data-category-other-field]');
    const paidSourceInputs = document.querySelectorAll('[data-paid-source]');
    const paidSourceCards = document.querySelectorAll('[data-paid-source-card]');
    const adminPaidFields = document.querySelector('[data-admin-paid-fields]');
    const paybackToggle = document.querySelector('[data-payback-toggle]');
    const paybackDrawer = document.querySelector('[data-payback-drawer]');
    const paybackOverlay = document.querySelector('[data-payback-drawer-overlay]');
    const paybackCloseButtons = document.querySelectorAll('[data-payback-drawer-close]');

    if (!drawer || !overlay || !addButton) return;
    document.body.append(overlay, drawer);
    if (paybackDrawer && paybackOverlay) {
        document.body.append(paybackOverlay, paybackDrawer);
    }

    const setOpen = (open) => {
        overlay.classList.toggle('hidden', !open);
        requestAnimationFrame(() => {
            overlay.classList.toggle('opacity-0', !open);
            overlay.classList.toggle('opacity-100', open);
        });
        drawer.classList.toggle('translate-x-full', !open);
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        document.body.classList.toggle('overflow-hidden', open);
    };

    const setPaybackOpen = (open) => {
        if (!paybackDrawer || !paybackOverlay) return;
        paybackOverlay.classList.toggle('hidden', !open);
        requestAnimationFrame(() => {
            paybackOverlay.classList.toggle('opacity-0', !open);
            paybackOverlay.classList.toggle('opacity-100', open);
        });
        paybackDrawer.classList.toggle('translate-x-full', !open);
        paybackDrawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        document.body.classList.toggle('overflow-hidden', open);
    };

    const updateScope = () => {
        const selected = document.querySelector('[data-expense-scope]:checked')?.value || 'general';
        orderField?.classList.toggle('hidden', selected !== 'order');
        scopeCards.forEach((card) => {
            const active = card.querySelector('[data-expense-scope]')?.value === selected;
            card.classList.toggle('border-primary-200', active);
            card.classList.toggle('text-primary-900', active);
            card.classList.toggle('ring-2', active);
            card.classList.toggle('ring-primary-100', active);
            card.classList.toggle('border-slate-200', !active);
            card.classList.toggle('text-slate-700', !active);
        });
    };

    const updateCategory = () => {
        categoryOtherField?.classList.toggle('hidden', category?.value !== 'Other');
    };

    const updatePaidSource = () => {
        const selected = document.querySelector('[data-paid-source]:checked')?.value || 'company_cash';
        adminPaidFields?.classList.toggle('hidden', selected !== 'admin');
        paidSourceCards.forEach((card) => {
            const active = card.querySelector('[data-paid-source]')?.value === selected;
            card.classList.toggle('border-primary-200', active);
            card.classList.toggle('text-primary-900', active);
            card.classList.toggle('ring-2', active);
            card.classList.toggle('ring-primary-100', active);
            card.classList.toggle('border-slate-200', !active);
            card.classList.toggle('text-slate-700', !active);
        });
    };

    addButton.addEventListener('click', () => setOpen(true));
    closeButtons.forEach((button) => button.addEventListener('click', () => setOpen(false)));
    overlay.addEventListener('click', () => setOpen(false));
    scopeInputs.forEach((input) => input.addEventListener('change', updateScope));
    category?.addEventListener('change', updateCategory);
    paidSourceInputs.forEach((input) => input.addEventListener('change', updatePaidSource));
    paybackToggle?.addEventListener('click', () => setPaybackOpen(true));
    paybackCloseButtons.forEach((button) => button.addEventListener('click', () => setPaybackOpen(false)));
    paybackOverlay?.addEventListener('click', () => setPaybackOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') setOpen(false);
        if (event.key === 'Escape' && paybackDrawer?.getAttribute('aria-hidden') === 'false') setPaybackOpen(false);
    });
    updateScope();
    updateCategory();
    updatePaidSource();
})();
</script>
