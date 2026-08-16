<?php
declare(strict_types=1);
?>
<div class="space-y-4">
    <?php if ($orderItems): ?>
        <?php foreach ($orderItems as $item): ?>
            <?php
                $img = (string) ($item['product_image'] ?? '/assets/images/hero_slide_1.jpg');
                if (str_starts_with($img, 'public/')) $img = '/' . substr($img, 7);
                $img = app_url($img);
            ?>
            <div class="flex gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <img src="<?= htmlspecialchars($img) ?>" alt="" class="h-20 w-24 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-secondary"><?= htmlspecialchars((string) $item['product_name']) ?></h3>
                            <p class="mt-0.5 font-mono text-xs text-slate-500">SKU: <?= htmlspecialchars((string) ($item['sku'] ?? '—')) ?></p>
                            <?php $chosenColour = trim((string) ($item['variant_color'] ?? $item['variant_name'] ?? '')); if ($chosenColour !== ''): ?>
                                <p class="mt-1 inline-flex rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">Selected colour: <?= htmlspecialchars($chosenColour) ?></p>
                            <?php endif; ?>
                            <?php if (trim((string) ($item['short_description'] ?? '')) !== ''): ?>
                                <p class="mt-1 text-sm text-slate-500 line-clamp-2"><?= htmlspecialchars((string) $item['short_description']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-secondary">LKR <?= number_format((float) $item['total_price'], 2) ?></p>
                            <p class="text-xs text-slate-400"><?= (int) $item['quantity'] ?> × LKR <?= number_format((float) $item['unit_price'], 2) ?></p>
                        </div>
                    </div>
                    <?php if (trim((string) ($item['gift_message'] ?? '')) !== ''): ?>
                        <div class="mt-3 rounded-xl border border-primary/10 bg-primary/[0.03] px-3 py-2 text-xs text-secondary">
                            <span class="font-bold text-primary">Gift message:</span>
                            <?= htmlspecialchars((string) $item['gift_message']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-500">Subtotal</span>
                <span class="font-bold text-secondary">LKR <?= number_format((float) ($order['subtotal'] ?? 0), 2) ?></span>
            </div>
            <?php if ((float) ($order['discount_total'] ?? 0) > 0): ?>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-500">Discount</span>
                    <span class="font-bold text-emerald-600">− LKR <?= number_format((float) $order['discount_total'], 2) ?></span>
                </div>
            <?php endif; ?>
            <?php if ((float) ($order['delivery_fee'] ?? 0) > 0): ?>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-500">Delivery</span>
                    <span class="font-bold text-secondary">LKR <?= number_format((float) $order['delivery_fee'], 2) ?></span>
                </div>
            <?php endif; ?>
            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                <span class="font-bold text-secondary">Grand Total</span>
                <span class="text-xl font-black text-primary">LKR <?= number_format((float) $order['grand_total'], 2) ?></span>
            </div>
        </div>
    <?php else: ?>
        <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center italic text-slate-400">No items in this order.</p>
    <?php endif; ?>
</div>
