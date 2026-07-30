<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-wider text-primary">Moderation</p><h1 class="text-2xl font-extrabold text-slate-950">Product reviews</h1></div>
            <form method="GET" action="<?= e(url('/admin/reviews')) ?>" class="flex gap-3">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All</option>
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </div>
    <div class="grid gap-4">
        <?php foreach (($reviews ?? []) as $review): ?>
            <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e($review['product_name']) ?></p>
                        <h2 class="text-lg font-extrabold text-slate-950"><?= e($review['title'] ?: 'Review') ?> <span class="text-primary"><?= (int) $review['rating'] ?>/5</span></h2>
                        <p class="mt-2 text-sm text-slate-600"><?= e($review['body']) ?></p>
                        <p class="mt-2 text-xs text-slate-400"><?= e(trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')) ?: 'Customer') ?> - <?= e($label($review['status'])) ?></p>
                    </div>
                    <form method="POST" action="<?= e(url('/admin/reviews/' . $review['id'] . '/status')) ?>" class="flex gap-2">
                        <?= csrf_field() ?>
                        <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                            <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $review['status'] === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                        </select>
                        <button class="rounded-button bg-primary px-3 py-2 text-sm font-bold text-white">Save</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?><div class="rounded-card border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">No reviews found.</div><?php endif; ?>
    </div>
</div>
