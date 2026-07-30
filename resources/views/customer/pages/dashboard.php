<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 rounded-card border border-slate-200 bg-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Customer portal</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-950">Welcome back, <?= e(App\Core\Auth::user()['name'] ?? 'Customer') ?></h1>
                <p class="mt-1 text-sm text-slate-500">Manage your GiftVibe.lk profile, wishlist, and delivery addresses.</p>
            </div>
            <form method="POST" action="<?= e(url('/logout')) ?>">
                <?= csrf_field() ?>
                <button class="rounded-button border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Sign Out</button>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <a href="<?= e(url('/customer/orders')) ?>" class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Orders</span>
                <strong class="mt-2 block text-3xl text-slate-950"><?= (int) ($counts['orders'] ?? 0) ?></strong>
            </a>
            <a href="<?= e(url('/shop')) ?>" class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Wishlist likes</span>
                <strong class="mt-2 block text-3xl text-primary"><?= (int) ($counts['wishlist'] ?? 0) ?></strong>
            </a>
            <a href="<?= e(url('/customer/addresses')) ?>" class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Addresses</span>
                <strong class="mt-2 block text-3xl text-slate-950"><?= (int) ($counts['addresses'] ?? 0) ?></strong>
            </a>
            <a href="<?= e(url('/customer/requests')) ?>" class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Custom requests</span>
                <strong class="mt-2 block text-3xl text-primary"><?= (int) ($counts['requests'] ?? 0) ?></strong>
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="<?= e(url('/customer/profile')) ?>" class="rounded-card border border-slate-200 bg-white p-5 text-sm font-bold text-slate-800 shadow-sm hover:text-primary">Edit profile</a>
            <a href="<?= e(url('/customer/addresses')) ?>" class="rounded-card border border-slate-200 bg-white p-5 text-sm font-bold text-slate-800 shadow-sm hover:text-primary">Manage addresses</a>
            <a href="<?= e(url('/customer/orders')) ?>" class="rounded-card border border-slate-200 bg-white p-5 text-sm font-bold text-slate-800 shadow-sm hover:text-primary">View order history</a>
            <a href="<?= e(url('/customer/requests')) ?>" class="rounded-card border border-slate-200 bg-white p-5 text-sm font-bold text-slate-800 shadow-sm hover:text-primary">Custom gift requests</a>
            <a href="<?= e(url('/cart')) ?>" class="rounded-card border border-slate-200 bg-white p-5 text-sm font-bold text-slate-800 shadow-sm hover:text-primary">Open shopping cart</a>
        </div>
    </div>
</section>
