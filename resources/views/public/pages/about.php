<?php
$statCards = [
    ['label' => 'Active gifts', 'value' => (int) ($stats['products'] ?? 0)],
    ['label' => 'Gift categories', 'value' => (int) ($stats['categories'] ?? 0)],
    ['label' => 'Delivered orders', 'value' => (int) ($stats['delivered_orders'] ?? 0)],
    ['label' => 'Approved reviews', 'value' => (int) ($stats['approved_reviews'] ?? 0)],
];
?>
<section class="relative overflow-hidden bg-slate-950 text-white">
    <img src="<?= e(asset('/public/assets/images/hero_gift_box.jpg')) ?>" alt="GiftVibe.lk curated gift boxes and flowers" class="absolute inset-0 h-full w-full object-cover opacity-35">
    <div class="absolute inset-0 bg-slate-950/70"></div>
    <div class="relative mx-auto grid min-h-[520px] max-w-container grid-cols-1 gap-8 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:px-8">
        <div class="self-center lg:col-span-7">
            <p class="text-xs font-bold uppercase tracking-wider text-primary-200">GiftVibe.lk story</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">A modern gifting studio for Sri Lanka&apos;s most thoughtful moments.</h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200">We combine curated product collections, custom gift requests, reference images, delivery tracking, and real customer reviews so gifting feels personal without becoming complicated.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="<?= e(url('/shop')) ?>" class="rounded-button bg-primary px-5 py-3 text-sm font-bold text-white">Explore gifts</a>
                <a href="<?= e(url('/custom-gifts')) ?>" class="rounded-button border border-white/30 px-5 py-3 text-sm font-bold text-white hover:bg-white/10">Custom request</a>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($statCards as $card): ?>
                <div class="rounded-card border border-slate-200 bg-slate-50 p-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e($card['label']) ?></span>
                    <strong class="mt-2 block text-3xl text-primary"><?= number_format($card['value']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="mx-auto grid max-w-container gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
        <article class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Curated gifts</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Published gifts are organized by categories, price, stock, offers, and ratings so customers can choose confidently.</p>
        </article>
        <article class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Custom requests</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Customers can describe a gift idea, upload references, and continue the conversation with admins in one thread.</p>
        </article>
        <article class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
            <h2 class="text-lg font-extrabold text-slate-950">Delivery trust</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Delivered-order highlights and approved reviews help new shoppers see the real activity behind the store.</p>
        </article>
    </div>
</section>
