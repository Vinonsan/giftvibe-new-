<section class="relative overflow-hidden bg-slate-950 text-white">
    <img src="<?= e(asset('/public/assets/images/hero_gift_box.jpg')) ?>" alt="Customised GiftVibe.lk gift request inspiration" class="absolute inset-0 h-full w-full object-cover opacity-35">
    <div class="absolute inset-0 bg-slate-950/70"></div>
    <div class="relative mx-auto grid min-h-[440px] max-w-container gap-8 px-4 py-14 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="self-center lg:col-span-7">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-200">Customised gifts</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">Build a gift around your idea, budget, and reference images.</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-200">Tell us the occasion, recipient, budget, and delivery date. Upload references and continue the discussion in your customer request thread.</p>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto grid max-w-container gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <aside class="lg:col-span-4">
            <div class="rounded-card border border-slate-200 bg-slate-50 p-5">
                <h2 class="text-lg font-extrabold text-slate-950">How it works</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <p><strong class="text-slate-950">1. Share the idea.</strong> Add occasion, recipient, budget, and references.</p>
                    <p><strong class="text-slate-950">2. Admin reviews.</strong> The team replies with options or a quote.</p>
                    <p><strong class="text-slate-950">3. Track status.</strong> Follow new, reviewing, quoted, approved, and completed states.</p>
                </div>
            </div>
            <?php if (!App\Core\Auth::isCustomer()): ?>
                <div class="mt-4 rounded-card border border-primary-100 bg-primary-50 p-5 text-sm text-primary-900">
                    Login is required when submitting so your request, images, and replies stay attached to your customer portal.
                </div>
            <?php endif; ?>
        </aside>
        <form method="POST" action="<?= e(url('/custom-gifts')) ?>" enctype="multipart/form-data" class="rounded-card border border-slate-200 bg-white p-6 shadow-card lg:col-span-8">
            <?= csrf_field() ?>
            <h2 class="text-2xl font-extrabold text-slate-950">Request a personalised gift</h2>
            <?php if (!empty($error)): ?><div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <input name="occasion" class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Occasion">
                <input name="recipient" class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Recipient">
                <input name="budget" type="number" step="0.01" min="0" class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Budget">
                <input name="delivery_date" type="date" class="rounded-button border border-slate-300 px-3 py-3 text-sm">
                <textarea name="message" required rows="6" class="rounded-button border border-slate-300 px-3 py-3 text-sm sm:col-span-2" placeholder="Describe the gift you want us to create"></textarea>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 sm:col-span-2">Reference images<input name="reference_images[]" type="file" multiple accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full rounded-button border border-slate-300 px-3 py-3 text-sm normal-case"></label>
            </div>
            <button class="mt-5 rounded-button bg-primary px-5 py-3 text-sm font-bold text-white">Submit request</button>
        </form>
    </div>
</section>
