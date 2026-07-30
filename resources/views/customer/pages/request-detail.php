<?php $label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status)); ?>
<section class="bg-slate-50 py-10">
    <div class="mx-auto grid max-w-container grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-5">
            <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Custom request</p>
                <h1 class="mt-2 text-2xl font-black text-slate-950"><?= e($request['request_number']) ?></h1>
                <p class="mt-2 text-sm text-slate-500">Status: <strong><?= e($label($request['status'])) ?></strong></p>
                <p class="mt-4 text-sm leading-6 text-slate-600"><?= e($request['message']) ?></p>
                <?php if (!empty($images)): ?>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <?php foreach ($images as $image): ?><img src="<?= e(asset('/' . $image['image_path'])) ?>" alt="Reference image for custom gift request <?= e($request['request_number']) ?>" class="aspect-square rounded-button object-cover"><?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="lg:col-span-7">
            <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950">Conversation</h2>
                <div class="mt-4 space-y-3">
                    <?php foreach (($replies ?? []) as $reply): ?>
                        <div class="rounded-card <?= $reply['sender_type'] === 'admin' ? 'bg-primary-50 text-primary-900' : 'bg-slate-50 text-slate-700' ?> p-4 text-sm">
                            <div class="flex justify-between gap-3"><strong><?= e($reply['sender_name']) ?></strong><span class="text-xs opacity-70"><?= e(date('M d, Y h:i A', strtotime($reply['created_at']))) ?></span></div>
                            <p class="mt-2"><?= e($reply['message']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($replies)): ?><p class="text-sm text-slate-500">No replies yet.</p><?php endif; ?>
                </div>
                <form method="POST" action="<?= e(url('/customer/requests/' . $request['request_number'] . '/reply')) ?>" class="mt-5">
                    <?= csrf_field() ?>
                    <textarea name="message" required rows="4" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Write a reply"></textarea>
                    <button class="mt-3 rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Send reply</button>
                </form>
            </div>
        </div>
    </div>
</section>
