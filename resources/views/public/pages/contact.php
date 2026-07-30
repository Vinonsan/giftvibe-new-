<section class="bg-white py-12">
    <div class="mx-auto grid max-w-container grid-cols-1 gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-5">
            <div class="overflow-hidden rounded-card border border-slate-200 bg-slate-100 shadow-card">
                <img src="<?= e(asset('/public/assets/images/hero_gift_box.jpg')) ?>" alt="GiftVibe.lk support for gift delivery orders" class="h-72 w-full object-cover lg:h-96">
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">Order support</span>
                    <p class="mt-1 text-sm text-slate-600">Delivery changes, payment checks, and order questions.</p>
                </div>
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">Custom gifts</span>
                    <p class="mt-1 text-sm text-slate-600">Share ideas, budgets, references, and delivery dates.</p>
                </div>
                <div class="rounded-card border border-slate-200 bg-slate-50 p-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">Business orders</span>
                    <p class="mt-1 text-sm text-slate-600">Bulk gifting and recurring corporate requests.</p>
                </div>
            </div>
        </div>
        <div class="lg:col-span-7">
            <p class="text-xs font-bold uppercase tracking-wider text-primary">Contact</p>
            <h1 class="mt-2 text-4xl font-black text-slate-950">Talk to GiftVibe.lk</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">Send order questions, gifting ideas, or support requests. Your message goes into the admin inbox with status tracking and replies.</p>
            <form method="POST" action="<?= e(url('/contact')) ?>" class="mt-6 rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <?= csrf_field() ?>
                <?php if (!empty($message)): ?><div class="mb-4 rounded-card border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"><?= e($message) ?></div><?php endif; ?>
                <?php if (!empty($error)): ?><div class="mb-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <input name="name" required class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Name">
                    <input name="email" type="email" required class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Email">
                    <input name="phone" class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Phone">
                    <input name="subject" class="rounded-button border border-slate-300 px-3 py-3 text-sm" placeholder="Subject">
                    <textarea name="message" required rows="6" class="rounded-button border border-slate-300 px-3 py-3 text-sm sm:col-span-2" placeholder="How can we help?"></textarea>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-button bg-primary px-5 py-3 text-sm font-bold text-white">Send message</button>
                    <a href="<?= e(url('/custom-gifts')) ?>" class="rounded-button border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Request a custom gift</a>
                </div>
            </form>
        </div>
    </div>
</section>
