<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-wider text-primary">Custom gifts</p><h1 class="text-2xl font-extrabold text-slate-950">Requests</h1></div>
            <form method="GET" action="<?= e(url('/admin/custom-requests')) ?>" class="flex gap-3">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All</option>
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </div>
    <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <?php foreach (($requests ?? []) as $request): ?>
            <a href="<?= e(url('/admin/custom-requests/' . $request['id'])) ?>" class="grid gap-2 border-b border-slate-100 p-4 text-sm hover:bg-slate-50 sm:grid-cols-4">
                <strong class="text-primary"><?= e($request['request_number']) ?></strong><span><?= e($request['customer_name']) ?></span><span><?= e($label($request['status'])) ?></span><span class="text-slate-400"><?= e(date('M d, Y', strtotime($request['created_at']))) ?></span>
            </a>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?><div class="p-8 text-center text-sm text-slate-500">No requests found.</div><?php endif; ?>
    </div>
</div>
