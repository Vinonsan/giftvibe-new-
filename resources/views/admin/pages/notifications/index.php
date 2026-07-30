<div class="space-y-6">
    <div class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <p class="text-xs font-bold uppercase tracking-wider text-primary">Admin notifications</p>
        <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Notifications</h1>
    </div>
    <div class="grid gap-3">
        <?php foreach (($notifications ?? []) as $notification): ?>
            <article class="rounded-card border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <strong class="text-slate-950"><?= e($notification['title']) ?></strong>
                        <p class="mt-1 text-sm text-slate-600"><?= e($notification['message']) ?></p>
                        <p class="mt-1 text-xs text-slate-400"><?= e($notification['type']) ?> - <?= e($notification['status']) ?> - <?= e(date('M d, Y h:i A', strtotime($notification['created_at']))) ?></p>
                    </div>
                    <?php if ($notification['status'] === 'unread'): ?>
                        <form method="POST" action="<?= e(url('/admin/notifications/' . $notification['id'] . '/read')) ?>"><?= csrf_field() ?><button class="rounded-button border border-slate-300 px-3 py-2 text-xs font-bold">Mark read</button></form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?><div class="rounded-card border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">No notifications yet.</div><?php endif; ?>
    </div>
</div>
