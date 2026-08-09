<?php declare(strict_types=1); ?>
<div class="space-y-4">
    <fieldset><legend class="mb-2 text-xs font-bold text-secondary">Product source <span class="text-primary">*</span></legend><div class="grid gap-3 sm:grid-cols-2"><label class="rounded-xl border border-slate-200 p-4"><input required type="radio" name="procurement_type" value="handcrafted" <?= ($editProduct['procurement_type'] ?? 'handcrafted')==='handcrafted'?'checked':'' ?> class="mr-2 accent-primary"><strong>Handcrafted</strong><span class="mt-1 block text-xs text-slate-500">No cash deduction.</span></label><label class="rounded-xl border border-slate-200 p-4"><input required type="radio" name="procurement_type" value="purchased" <?= ($editProduct['procurement_type'] ?? '')==='purchased'?'checked':'' ?> class="mr-2 accent-primary"><strong>Purchased externally</strong><span class="mt-1 block text-xs text-slate-500">Cost × stock deducted from cash.</span></label></div></fieldset>
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Selling price (LKR) <span class="text-primary">*</span></span>
            <input id="selling-price" required type="number" min="0" step="0.01" name="selling_price" value="<?= htmlspecialchars((string) ($editProduct['base_price'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Cost / buying price (LKR) <span class="text-primary">*</span></span>
            <input id="buying-price" required type="number" min="0" step="0.01" name="buying_price" value="<?= htmlspecialchars((string) ($editProduct['cost_price'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
    </div>
    <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Profit per unit</p>
            <p id="profit-display" class="mt-0.5 text-2xl font-black text-emerald-600">LKR 0.00</p>
        </div>
    </div>
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Stock quantity</span>
        <input type="number" min="0" name="stock_quantity" value="<?= (int) ($editProduct['stock_quantity'] ?? 0) ?>" class="<?= $fc ?>">
    </label>
    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-secondary">
            Featured product
            <input type="checkbox" name="is_featured" value="1" <?= !empty($editProduct['is_featured']) ? 'checked' : '' ?> class="h-4 w-4 accent-primary">
        </label>
        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-secondary">
            Active / Visible
            <input type="checkbox" name="status" value="active" <?= ($editProduct['status'] ?? '') === 'active' ? 'checked' : '' ?> class="h-4 w-4 accent-primary">
        </label>
    </div>
</div>
