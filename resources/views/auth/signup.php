<section class="bg-slate-50 py-12">
    <div class="mx-auto max-w-lg px-4 sm:px-6 lg:px-8">
        <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Customer account</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Create your GiftVibe.lk account</h1>
            <p class="mt-1 text-sm text-slate-500">Save favorite products, manage delivery addresses, and move faster through checkout later.</p>

            <?php if (!empty($error)): ?>
                <div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= e(url('/signup')) ?>" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <?= csrf_field() ?>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">First name</label>
                    <input name="first_name" required class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Last name</label>
                    <input name="last_name" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Email</label>
                    <input name="email" type="email" required class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Phone</label>
                    <input name="phone" type="tel" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Password</label>
                    <input name="password" type="password" required minlength="8" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Confirm password</label>
                    <input name="password_confirmation" type="password" required minlength="8" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <button class="sm:col-span-2 rounded-button bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-700">Create Account</button>
                <p class="sm:col-span-2 text-center text-xs text-slate-500">Already registered? <a href="<?= e(url('/login')) ?>" class="font-bold text-primary hover:text-primary-800">Sign in</a></p>
            </form>
        </div>
    </div>
</section>
