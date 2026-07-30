<?php $money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2); ?>
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-950">Checkout</h1>
        <?php if (!empty($error)): ?><div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="POST" action="<?= e(url('/checkout')) ?>" enctype="multipart/form-data" class="mt-6 grid gap-6 lg:grid-cols-12">
            <?= csrf_field() ?>
            <div class="space-y-6 lg:col-span-8">
                <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                    <h2 class="text-lg font-extrabold text-slate-950">Delivery address</h2>
                    <div class="mt-4 grid gap-3">
                        <?php foreach (($addresses ?? []) as $address): ?>
                            <label class="flex gap-3 rounded-card border border-slate-200 p-4">
                                <input type="radio" name="address_id" value="<?= (int) $address['id'] ?>" <?= $address['is_default'] ? 'checked' : '' ?> required>
                                <span class="text-sm"><strong><?= e($address['label']) ?></strong><br><?= e($address['recipient_name']) ?>, <?= e($address['address_line_1']) ?>, <?= e($address['city']) ?>, <?= e($address['district']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($addresses)): ?><a href="<?= e(url('/customer/addresses')) ?>" class="mt-4 inline-flex rounded-button bg-primary px-4 py-2 text-sm font-bold text-white">Add an address</a><?php endif; ?>
                </section>
                <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                    <h2 class="text-lg font-extrabold text-slate-950">Payment</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <label class="rounded-card border border-slate-200 p-4 text-sm"><input type="radio" name="payment_method" value="cod" checked> <strong>Cash on delivery</strong><br><span class="text-slate-500">Pay when the gift is delivered.</span></label>
                        <label class="rounded-card border border-slate-200 p-4 text-sm"><input type="radio" name="payment_method" value="bank_transfer"> <strong>Bank transfer</strong><br><span class="text-slate-500">Upload receipt for admin verification.</span></label>
                    </div>
                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Payment receipt</label>
                        <input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" class="block w-full rounded-button border border-slate-300 bg-white px-3 py-2 text-sm">
                    </div>
                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-600">Delivery date</label>
                        <input type="date" name="delivery_date" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <textarea name="customer_notes" rows="3" class="mt-4 w-full rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Delivery notes"></textarea>
                </section>
            </div>
            <aside class="lg:col-span-4">
                <div class="sticky top-24 rounded-card border border-slate-200 bg-white p-5 shadow-card">
                    <h2 class="text-lg font-extrabold text-slate-950">Order summary</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt>Subtotal</dt><dd class="font-bold"><?= $money($totals['subtotal'] ?? 0) ?></dd></div>
                        <div class="flex justify-between"><dt>Delivery charge</dt><dd class="font-bold"><?= $money($totals['delivery_fee'] ?? 0) ?></dd></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3 text-base"><dt class="font-extrabold">Grand total</dt><dd class="font-extrabold text-primary"><?= $money($totals['grand_total'] ?? 0) ?></dd></div>
                    </dl>
                    <button class="mt-5 w-full rounded-button bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-700" <?= empty($addresses) ? 'disabled' : '' ?>>Place Order</button>
                </div>
            </aside>
        </form>
    </div>
</section>
