<?php
/**
 * Variables:
 * @var float $price
 * @var float|null $oldPrice
 * @var string|null $currency
 */
$currency = $currency ?? 'LKR';
?>
<div class="flex items-baseline gap-2">
    <span class="text-base font-extrabold text-primary"><?= e($currency) ?> <?= number_format($price, 0) ?></span>
    <?php if (isset($oldPrice) && $oldPrice > $price): ?>
        <span class="text-xs text-slate-400 line-through"><?= e($currency) ?> <?= number_format($oldPrice, 0) ?></span>
    <?php endif; ?>
</div>