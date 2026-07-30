<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<div class="grid gap-6 lg:grid-cols-12">
    <section class="lg:col-span-5">
        <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Custom request</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-950"><?= e($request['request_number']) ?></h1>
            <p class="mt-2 text-sm text-slate-500"><?= e($request['customer_name']) ?> - <?= e($request['customer_email']) ?> - <?= e($request['customer_phone']) ?></p>
            <p class="mt-4 text-sm leading-6 text-slate-600"><?= e($request['message']) ?></p>
            <?php if (!empty($images)): ?><div class="mt-4 grid grid-cols-3 gap-3"><?php foreach ($images as $image): ?><img src="<?= e(asset('/' . $image['image_path'])) ?>" alt="Reference image for <?= e($request['request_number']) ?>" class="aspect-square rounded-button object-cover"><?php endforeach; ?></div><?php endif; ?>
        </div>
    </section>
    <section class="lg:col-span-7">
        <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Chat replies</h2>
            <div class="mt-4 space-y-3">
                <?php foreach (($replies ?? []) as $reply): ?>
                    <div class="rounded-card <?= $reply['sender_type'] === 'admin' ? 'bg-primary-50 text-primary-900' : 'bg-slate-50 text-slate-700' ?> p-4 text-sm">
                        <div class="flex justify-between"><strong><?= e($reply['sender_name']) ?></strong><span class="text-xs opacity-70"><?= e(date('M d, Y h:i A', strtotime($reply['created_at']))) ?></span></div>
                        <p class="mt-2"><?= e($reply['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" action="<?= e(url('/admin/custom-requests/' . $request['id'] . '/reply')) ?>" class="mt-5 space-y-3">
                <?= csrf_field() ?>
                <select name="status" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <?php foreach ($statuses as $status): ?><option value="<?= e($status) ?>" <?= $request['status'] === $status ? 'selected' : '' ?>><?= e($label($status)) ?></option><?php endforeach; ?>
                </select>
                <textarea name="admin_notes" rows="2" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Internal admin notes"><?= e($request['admin_notes'] ?? '') ?></textarea>
                <textarea name="message" rows="4" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Write reply to customer"></textarea>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Save reply/status</button>
            </form>
        </div>
    </section>
</div>
