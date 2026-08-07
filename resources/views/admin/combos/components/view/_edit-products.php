<?php
declare(strict_types=1);

$productCostMap = [];
$productSellingMap = [];
foreach ($products as $product) {
    $productCostMap[(string) $product['id']] = (float) ($product['cost_price'] ?? 0);
    $productSellingMap[(string) $product['id']] = (float) ($product['base_price'] ?? 0);
}
?>
<div class="space-y-4">
    <?php
        $selectName = 'product_ids[]';
        $selectId = 'combo-products';
        $selectOptions = [];
        foreach ($products as $product) {
            $selectOptions[(string)$product['id']] = $product['name'] . ' · ' . $product['sku'];
        }
        $selectValue = array_map('strval', $selectedProductIds);
        $selectPlaceholder = 'Select products for this combo';
        $selectLabel = 'Products';
        $selectHint = 'Select all products included in this combo.';
        $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = true;
        $selectRequired = true;
        $selectDisabled = false;
        $selectAttributes = [];
        $selectClass = '';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?>

    <hr class="border-slate-100">

    <div class="space-y-4">
        <h4 class="text-sm font-bold text-secondary">Pricing Calculation</h4>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Total Buying Price</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="combo-view-total-cost" class="text-base font-bold text-blue-700">LKR 0.00</span>
                </div>
                <p class="mt-0.5 text-[10px] text-blue-600/70">Sum of product cost prices</p>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Total Cost (Incl. Packing)</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="combo-view-total-final-cost" class="text-base font-bold text-amber-800">LKR 0.00</span>
                </div>
                <p class="mt-0.5 text-[10px] text-amber-700/70">Buying price + other costs</p>
            </div>

            <div class="rounded-xl border border-purple-100 bg-purple-50/50 p-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Total Base Price</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="combo-view-total-selling" class="text-base font-bold text-purple-700">LKR 0.00</span>
                </div>
                <p class="mt-0.5 text-[10px] text-purple-600/70">Sum of product selling prices</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold text-secondary">Other Cost (Box, packing, etc)</span>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm font-semibold text-slate-400">LKR</span>
                    <input id="field-other-cost" type="number" min="0" step="0.01" name="other_cost" value="<?= htmlspecialchars((string) ($editCombo['other_cost'] ?? '0.00')) ?>" class="<?= $fc ?> pl-11">
                </div>
                <p class="mt-1 text-[10px] text-slate-500">Added to combo selling price and total cost automatically.</p>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold text-secondary">Selling Price (Combo Price) <span class="text-primary">*</span></span>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-sm font-semibold text-slate-400">LKR</span>
                    <input id="field-price" required type="number" min="0" step="0.01" name="price" value="<?= htmlspecialchars((string) ($editCombo['price'] ?? '')) ?>" class="<?= $fc ?> pl-11">
                </div>
                <p class="mt-1 text-[10px] text-slate-500">Auto-filled from base price + packing. You can edit manually.</p>
            </label>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Calculated Profit</h4>
            <div id="combo-view-profit-display" class="mt-1 text-2xl font-black text-slate-300">LKR 0.00</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var productPrices = <?= json_encode($productCostMap) ?>;
    var productSellingPrices = <?= json_encode($productSellingMap) ?>;

    function getSelectedTotals() {
        var totalCost = 0;
        var totalSelling = 0;
        document.querySelectorAll('[data-custom-select][data-name="product_ids[]"] [data-select-option][data-selected="true"]').forEach(function (el) {
            var pid = el.getAttribute('data-value');
            totalCost += parseFloat(productPrices[pid]) || 0;
            totalSelling += parseFloat(productSellingPrices[pid]) || 0;
        });
        return { totalCost: totalCost, totalSelling: totalSelling };
    }

    function syncComboPrice() {
        var totals = getSelectedTotals();
        var otherEl = document.getElementById('field-other-cost');
        var otherCost = otherEl ? (parseFloat(otherEl.value) || 0) : 0;
        var sellingEl = document.getElementById('field-price');
        if (sellingEl) {
            sellingEl.value = (totals.totalSelling + otherCost).toFixed(2);
        }
    }

    function updatePricing() {
        var totals = getSelectedTotals();
        var totalCost = totals.totalCost;
        var totalSelling = totals.totalSelling;

        var costDisplay = document.getElementById('combo-view-total-cost');
        if (costDisplay) costDisplay.textContent = 'LKR ' + totalCost.toFixed(2);

        var sellingDisplay = document.getElementById('combo-view-total-selling');
        if (sellingDisplay) sellingDisplay.textContent = 'LKR ' + totalSelling.toFixed(2);

        var otherEl = document.getElementById('field-other-cost');
        var otherCost = otherEl ? (parseFloat(otherEl.value) || 0) : 0;
        var finalCost = totalCost + otherCost;

        var finalCostDisplay = document.getElementById('combo-view-total-final-cost');
        if (finalCostDisplay) finalCostDisplay.textContent = 'LKR ' + finalCost.toFixed(2);

        var sellingEl = document.getElementById('field-price');
        var sellingPrice = sellingEl ? (parseFloat(sellingEl.value) || 0) : 0;
        var profit = sellingPrice - finalCost;

        var profitDisplay = document.getElementById('combo-view-profit-display');
        if (profitDisplay) {
            profitDisplay.textContent = 'LKR ' + profit.toFixed(2);
            profitDisplay.className = 'mt-1 text-2xl font-black ' + (profit >= 0 ? 'text-emerald-600' : 'text-rose-500');
        }
    }

    document.getElementById('field-price')?.addEventListener('input', updatePricing);
    document.getElementById('field-other-cost')?.addEventListener('input', function () {
        syncComboPrice();
        updatePricing();
    });
    document.addEventListener('select:change', function (e) {
        if (e.target.dataset.name === 'product_ids[]') {
            syncComboPrice();
            updatePricing();
        }
    });

    setTimeout(updatePricing, 100);
});
</script>
