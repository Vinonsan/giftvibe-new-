<?php
declare(strict_types=1);

$redirectTo = $viewBase . '&tab=overview';
?>
<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-primary/15 bg-primary/[0.04] px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Total orders</p>
        <p class="mt-1 text-2xl font-black text-secondary"><?= (int) ($stats['orders_count'] ?? 0) ?></p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lifetime spend</p>
        <p class="mt-1 text-lg font-black text-secondary">LKR <?= number_format((float) ($stats['lifetime_total'] ?? 0), 2) ?></p>
    </div>
    <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Delivered revenue</p>
        <p class="mt-1 text-lg font-black text-emerald-800">LKR <?= number_format((float) ($stats['delivered_total'] ?? 0), 2) ?></p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Last order</p>
        <p class="mt-1 text-sm font-bold text-secondary"><?= trim((string) ($stats['last_order_at'] ?? '')) !== '' ? htmlspecialchars(substr((string) $stats['last_order_at'], 0, 10)) : '—' ?></p>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-slate-200 p-4">
    <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Account status</p>
    <div class="flex flex-wrap gap-2">
        <?php foreach (['active' => 'Activate', 'inactive' => 'Inactive', 'blocked' => 'Block account'] as $statusKey => $label): ?>
            <form method="post" action="/admin/customers" class="inline" <?= $statusKey === 'blocked' ? 'onsubmit="return confirm(\'Block this customer?\')"' : '' ?>>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="customer_id" value="<?= $customerId ?>">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusKey) ?>">
                <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo) ?>">
                <button type="submit" class="rounded-xl border px-4 py-2 text-xs font-bold transition cursor-pointer <?= $status === $statusKey ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-secondary hover:bg-slate-50' ?>"><?= htmlspecialchars($label) ?></button>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php $field('First name', (string) $customer['first_name']); ?>
    <?php $field('Last name', (string) ($customer['last_name'] ?? ''), trim((string) ($customer['last_name'] ?? '')) === ''); ?>
    <?php $field('Email', (string) $customer['email']); ?>
    <?php $field('Primary phone', (string) ($customer['phone'] ?? ''), trim((string) ($customer['phone'] ?? '')) === ''); ?>
    <?php $field('Secondary phone', (string) ($customer['phone_2'] ?? ''), trim((string) ($customer['phone_2'] ?? '')) === ''); ?>
    <?php $field('Account status', ucfirst($status)); ?>
</div>

<hr class="my-6 border-slate-100">

<h3 class="mb-4 text-sm font-bold text-secondary">Registered address</h3>
<div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 text-sm leading-relaxed text-secondary">
    <?php if (trim((string) ($customer['address_line_1'] ?? '')) !== ''): ?>
        <?= htmlspecialchars((string) $customer['address_line_1']) ?><br>
        <?php if (trim((string) ($customer['address_line_2'] ?? '')) !== ''): ?>
            <?= htmlspecialchars((string) $customer['address_line_2']) ?><br>
        <?php endif; ?>
        <?= htmlspecialchars(trim(((string) ($customer['city'] ?? '')) . ', ' . ((string) ($customer['district'] ?? '')))) ?>
    <?php else: ?>
        <span class="italic text-slate-400">No address on file</span>
    <?php endif; ?>
</div>

<?php if ($profile && (trim((string) ($profile['date_of_birth'] ?? '')) !== '' || trim((string) ($profile['gender'] ?? '')) !== '')): ?>
    <hr class="my-6 border-slate-100">
    <div class="grid gap-4 sm:grid-cols-2">
        <?php $field('Date of birth', (string) ($profile['date_of_birth'] ?? ''), trim((string) ($profile['date_of_birth'] ?? '')) === ''); ?>
        <?php $field('Gender', ucfirst((string) ($profile['gender'] ?? '')), trim((string) ($profile['gender'] ?? '')) === ''); ?>
    </div>
<?php endif; ?>

<hr class="my-6 border-slate-100">

<h3 class="mb-4 text-sm font-bold text-secondary">Admin notes</h3>
<?php if ($editMode): ?>
    <form method="post" action="/admin/customers" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="customer_id" value="<?= $customerId ?>">
        <input type="hidden" name="action" value="save_notes">
        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirectTo . '&mode=edit') ?>">
        <textarea name="notes" rows="4" class="<?= $fc ?>" placeholder="Internal notes about this customer…"><?= htmlspecialchars((string) ($profile['notes'] ?? '')) ?></textarea>
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary transition cursor-pointer">Save notes</button>
    </form>
<?php else: ?>
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3 text-sm whitespace-pre-wrap text-secondary">
        <?php if (trim((string) ($profile['notes'] ?? '')) !== ''): ?>
            <?= htmlspecialchars((string) $profile['notes']) ?>
        <?php else: ?>
            <span class="italic text-slate-400">No admin notes yet.</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ((int) ($stats['orders_count'] ?? 0) > 0): ?>
    <div class="mt-6">
        <a href="<?= htmlspecialchars($viewBase . '&tab=orders') ?>" class="text-sm font-bold text-primary hover:underline">View all orders →</a>
    </div>
<?php endif; ?>
