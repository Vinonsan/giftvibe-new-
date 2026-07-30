<?php if (($portal ?? '') === 'admin'): ?>
    <?php if (!empty($pendingPhone)): ?>
        <form method="POST" action="<?= e(url('/admin/login/verify-otp')) ?>" class="space-y-5">
            <?= csrf_field() ?>

            <div class="rounded-card border border-primary-100 bg-primary-50 px-4 py-3 text-sm text-primary-800">
                OTP requested for <?= e($pendingPhone) ?>.
            </div>

            <div>
                <label for="otp" class="mb-1 block text-xs font-semibold text-slate-600">Admin OTP</label>
                <input id="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" name="otp" required autocomplete="one-time-code" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm tracking-[0.35em] outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="000000">
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">Verify OTP</button>
        </form>

        <form method="POST" action="<?= e(url('/admin/login/request-otp')) ?>" class="mt-3">
            <?= csrf_field() ?>
            <input type="hidden" name="phone" value="<?= e($pendingPhone) ?>">
            <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Resend OTP</button>
        </form>
    <?php else: ?>
        <form method="POST" action="<?= e($action ?? url('/admin/login/request-otp')) ?>" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label for="phone" class="mb-1 block text-xs font-semibold text-slate-600">Authorized Admin Phone</label>
                <input id="phone" type="tel" name="phone" required autocomplete="tel" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="0758311995">
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">Send OTP</button>
            <p class="text-center text-xs text-slate-500">Development OTP: 000000</p>
        </form>
    <?php endif; ?>
<?php else: ?>
    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-md px-4 sm:px-6 lg:px-8">
            <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <h1 class="text-2xl font-extrabold text-slate-950">Customer login</h1>
                <p class="mt-1 text-sm text-slate-500">Sign in to manage your wishlist, profile, and delivery addresses.</p>
                <?php if (!empty($error)): ?>
                    <div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= e($action ?? url('/login')) ?>" class="mt-6 space-y-5">
                    <?= csrf_field() ?>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                        <input id="email" type="email" name="email" required autocomplete="email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary-100" placeholder="Enter password">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-800">Sign In</button>
                    <p class="text-center text-xs text-slate-500">New here? <a href="<?= e(url('/signup')) ?>" class="font-bold text-primary hover:text-primary-800">Create an account</a></p>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>
