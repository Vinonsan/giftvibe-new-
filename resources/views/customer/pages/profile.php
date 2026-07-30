<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Customer portal</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-950">My profile</h1>
            <?php if (!empty($message)): ?><div class="mt-4 rounded-card border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"><?= e($message) ?></div><?php endif; ?>
            <?php if (!empty($error)): ?><div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="POST" action="<?= e(url('/customer/profile')) ?>" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <?= csrf_field() ?>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">First name</label>
                    <input name="first_name" value="<?= e(explode(' ', $user['name'] ?? '', 2)[0] ?? '') ?>" required class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Last name</label>
                    <input name="last_name" value="<?= e(explode(' ', $user['name'] ?? '', 2)[1] ?? '') ?>" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Email</label>
                    <input value="<?= e($user['email'] ?? '') ?>" disabled class="w-full rounded-button border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Phone</label>
                    <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                </div>
                <button class="sm:col-span-2 rounded-button bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-700">Save Profile</button>
            </form>
        </div>
    </div>
</section>
