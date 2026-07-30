<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$imageUrl = static function (?string $path): string {
    if (!$path) return asset('/public/assets/images/hero_gift_box.jpg');
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    return asset('/' . ltrim($path, '/'));
};
?>
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-950">Shopping cart</h1>
        <?php if (!empty($error)): ?><div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="mt-6 grid gap-6 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-8">
                <?php foreach (($items ?? []) as $item): ?>
                    <article class="rounded-card border border-slate-200 bg-white p-4 shadow-card">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[96px_1fr_auto]">
                            <img src="<?= e($imageUrl($item['image_path'])) ?>" alt="<?= e($item['product_name']) ?> cart item image" class="h-24 w-24 rounded-button object-cover">
                            <div>
                                <a href="<?= e(url('/product/' . $item['slug'])) ?>" class="font-extrabold text-slate-950 hover:text-primary"><?= e($item['product_name']) ?></a>
                                <?php if ($item['variant_name']): ?><p class="text-sm text-slate-500"><?= e($item['variant_name']) ?><?= $item['color_name'] ? ' · ' . e($item['color_name']) : '' ?></p><?php endif; ?>
                                <?php if ($item['gift_message']): ?><p class="mt-2 text-xs text-slate-500">Gift message: <?= e($item['gift_message']) ?></p><?php endif; ?>
                                <p class="mt-2 text-sm font-bold text-primary"><?= $money($item['unit_price']) ?></p>
                            </div>
                            <div class="flex flex-col gap-2 sm:items-end">
                                <form method="POST" action="<?= e(url('/cart/update')) ?>" class="flex items-center gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <input type="number" name="quantity" min="1" max="<?= (int) ($item['variant_id'] ? $item['variant_stock'] : $item['product_stock']) ?>" value="<?= (int) $item['quantity'] ?>" class="w-20 rounded-button border border-slate-300 px-2 py-1 text-sm">
                                    <button class="rounded-button border border-slate-300 px-3 py-1 text-xs font-bold">Update</button>
                                </form>
                                <form method="POST" action="<?= e(url('/cart/remove')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <button class="text-xs font-bold text-danger hover:underline">Remove</button>
                                </form>
                                <strong class="text-sm text-slate-900"><?= $money((float) $item['unit_price'] * (int) $item['quantity']) ?></strong>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <div class="rounded-card border border-slate-200 bg-white p-10 text-center shadow-card">
                        <h2 class="text-xl font-extrabold text-slate-950">Your cart is empty</h2>
                        <a href="<?= e(url('/shop')) ?>" class="mt-4 inline-flex rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Shop gifts</a>
                    </div>
                <?php endif; ?>
            </div>
            <aside class="lg:col-span-4">
                <div class="sticky top-24 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                    <h2 class="text-lg font-extrabold text-slate-950">Cart summary</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt>Subtotal</dt><dd class="font-bold"><?= $money($totals['subtotal'] ?? 0) ?></dd></div>
                        <div class="flex justify-between"><dt>Estimated delivery</dt><dd class="font-bold"><?= $money($totals['delivery_fee'] ?? 0) ?></dd></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3 text-base"><dt class="font-extrabold">Total</dt><dd class="font-extrabold text-primary"><?= $money($totals['grand_total'] ?? 0) ?></dd></div>
                    </dl>
                    <a href="<?= e(url('/checkout')) ?>" class="mt-5 block rounded-button bg-primary px-4 py-3 text-center text-sm font-bold text-white hover:bg-primary-700 <?= empty($items) ? 'pointer-events-none opacity-50' : '' ?>">Checkout</a>
                </div>
            </aside>
        </div>
    </div>
</section>
