<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-3xl font-black text-slate-950">Custom gift requests</h1>
            <a href="<?= e(url('/custom-gifts')) ?>" class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">New request</a>
        </div>
        <div class="mt-6 overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
            <?php foreach (($requests ?? []) as $request): ?>
                <a href="<?= e(url('/customer/requests/' . $request['request_number'])) ?>" class="grid gap-2 border-b border-slate-100 p-4 text-sm hover:bg-slate-50 sm:grid-cols-4">
                    <strong class="text-primary"><?= e($request['request_number']) ?></strong>
                    <span><?= e($request['occasion'] ?: 'Custom gift') ?></span>
                    <span><?= e(ucwords($request['status'])) ?></span>
                    <span class="text-slate-400"><?= e(date('M d, Y', strtotime($request['created_at']))) ?></span>
                </a>
            <?php endforeach; ?>
            <?php if (empty($requests)): ?><div class="p-8 text-center text-sm text-slate-500">No custom gift requests yet.</div><?php endif; ?>
        </div>
    </div>
</section>
