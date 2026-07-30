<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-wider text-primary">Inbox</p><h1 class="text-2xl font-extrabold text-slate-950">Contact messages</h1></div>
            <form method="GET" action="<?= e(url('/admin/messages')) ?>" class="flex gap-3">
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All</option>
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </div>
    <div class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <?php foreach (($messages ?? []) as $message): ?>
            <a href="<?= e(url('/admin/messages/' . $message['id'])) ?>" class="block border-b border-slate-100 p-4 hover:bg-slate-50">
                <div class="flex justify-between gap-4"><strong class="text-primary"><?= e($message['subject'] ?: 'Contact message') ?></strong><span class="text-xs text-slate-400"><?= e($label($message['status'])) ?></span></div>
                <p class="mt-1 text-sm text-slate-600"><?= e($message['name']) ?> - <?= e($message['email']) ?></p>
            </a>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><div class="p-8 text-center text-sm text-slate-500">No messages found.</div><?php endif; ?>
    </div>
</div>
