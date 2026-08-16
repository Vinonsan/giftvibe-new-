<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
?>
<div class="space-y-4">
    <!-- Item source tabs -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex border-b border-slate-100 bg-slate-50/80 p-1.5 gap-1">
            <button type="button" data-item-tab="catalog" class="item-tab-btn flex-1 rounded-lg bg-white px-3 py-2.5 text-xs font-bold text-primary shadow-sm">Catalog</button>
            <button type="button" data-item-tab="custom" class="item-tab-btn flex-1 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-white/80">Custom item</button>
        </div>

        <!-- Catalog panel -->
        <div id="item-tab-catalog" class="p-4 space-y-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" data-catalog-tab="product" class="catalog-type-btn rounded-full border border-primary bg-primary px-4 py-1.5 text-xs font-bold text-white">Products</button>
                <button type="button" data-catalog-tab="combo" class="catalog-type-btn rounded-full border border-slate-200 px-4 py-1.5 text-xs font-bold text-secondary">Combos</button>
            </div>
            <div><?php
                $inputName = 'catalog_search';
                $inputId = 'catalog-search';
                $inputLabel = '';
                $inputValue = '';
                $inputType = 'search';
                $inputPlaceholder = 'Search by name or SKU…';
                $inputHint = $inputError = '';
                $inputSize = 'lg';
                $inputState = 'default';
                $inputRequired = $inputReadonly = $inputDisabled = false;
                $inputAutocomplete = 'off';
                $inputLeadingIcon = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
                $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
                $inputAttributes = ['aria-label' => 'Search catalog'];
                $inputClass = 'w-full';
                $inputWrapperClass = 'w-full';
                require BASE_PATH . '/resources/views/components/base/input.php';
            ?></div>
            <div id="catalog-list" class="grid max-h-[32rem] gap-2 overflow-y-auto pr-1 sm:grid-cols-2"></div>
        </div>

        <!-- Custom item panel -->
        <div id="item-tab-custom" class="hidden p-4 space-y-3">
            <p class="text-xs text-slate-500">Catalog la illaadha item — name, buying price, selling price enter pannunga.</p>
            <label class="block space-y-1.5">
                <span class="text-sm font-medium text-secondary">Item name</span>
                <input type="text" id="custom-item-name" class="<?= $fc ?>" placeholder="e.g. Custom hamper">
            </label>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium text-secondary">Buying price</span>
                    <input type="number" id="custom-item-cost" class="<?= $fc ?>" min="0" step="0.01" placeholder="0.00">
                </label>
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium text-secondary">Selling price</span>
                    <input type="number" id="custom-item-price" class="<?= $fc ?>" min="0.01" step="0.01" placeholder="0.00">
                </label>
            </div>
            <button type="button" id="custom-item-add-btn"
                class="w-full rounded-xl bg-secondary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary transition">
                Add custom item
            </button>
        </div>
    </div>

    <!-- Selected cart -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-secondary">Order items</h3>
                <p class="text-xs text-slate-500">Qty, buying &amp; selling price edit pannalam.</p>
            </div>
            <span id="cart-count-badge" class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold text-primary">0</span>
        </div>
        <div id="selected-items" class="space-y-3"></div>
        <div id="cart-summary" class="mt-4 hidden grid gap-2 rounded-xl border border-slate-100 bg-slate-50/80 p-3 text-sm sm:grid-cols-3">
            <div><p class="text-[10px] font-bold uppercase text-slate-400">Total cost</p><p id="summary-cost" class="font-bold text-rose-600">LKR 0</p></div>
            <div><p class="text-[10px] font-bold uppercase text-slate-400">Total sell</p><p id="summary-sell" class="font-bold text-emerald-600">LKR 0</p></div>
            <div><p class="text-[10px] font-bold uppercase text-slate-400">Profit</p><p id="summary-profit" class="font-bold text-secondary">LKR 0</p></div>
        </div>
    </div>
</div>
