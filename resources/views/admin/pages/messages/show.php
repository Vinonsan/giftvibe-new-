<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<div class="grid gap-6 lg:grid-cols-12">
    <section class="lg:col-span-5">
        <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Contact message</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-950"><?= e($message['subject'] ?: 'Message') ?></h1>
            <p class="mt-2 text-sm text-slate-500"><?= e($message['name']) ?> - <?= e($message['email']) ?> - <?= e($message['phone']) ?></p>
            <p class="mt-4 text-sm leading-6 text-slate-600"><?= e($message['message']) ?></p>
        </div>
    </section>
    <section class="lg:col-span-7">
        <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Replies</h2>
            <div class="mt-4 space-y-3">
                <?php foreach (($replies ?? []) as $reply): ?>
                    <div class="rounded-card <?= $reply['sender_type'] === 'admin' ? 'bg-primary-50 text-primary-900' : 'bg-slate-50 text-slate-700' ?> p-4 text-sm">
                        <div class="flex justify-between"><strong><?= e($reply['sender_name']) ?></strong><span class="text-xs opacity-70"><?= e(date('M d, Y h:i A', strtotime($reply['created_at']))) ?></span></div>
                        <p class="mt-2"><?= e($reply['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="<?= e(url('/admin/messages/' . $message['id'] . '/reply')) ?>" class="mt-5 space-y-3">
                <?= csrf_field() ?>
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $message['status'] === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <textarea name="message" rows="4" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Write admin reply"></textarea>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Save reply/status</button>
            </form>
        </div>
    </section>
</div>
