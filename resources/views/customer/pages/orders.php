<?php $money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2); ?>
<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-950">My orders</h1>
        <div class="mt-6 overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
            <?php foreach (($orders ?? []) as $order): ?>
                <a href="<?= e(url('/customer/orders/' . $order['order_number'])) ?>" class="grid gap-2 border-b border-slate-100 p-4 text-sm hover:bg-slate-50 sm:grid-cols-5">
                    <strong class="text-slate-950"><?= e($order['order_number']) ?></strong>
                    <span><?= e(ucwords(str_replace('_', ' ', $order['order_status']))) ?></span>
                    <span><?= e(ucfirst($order['payment_status'])) ?></span>
                    <span><?= $money($order['grand_total']) ?></span>
                    <span class="text-slate-500"><?= e(date('M d, Y', strtotime($order['created_at']))) ?></span>
                </a>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><div class="p-8 text-center text-sm text-slate-500">No orders yet.</div><?php endif; ?>
        </div>
    </div>
</section>
