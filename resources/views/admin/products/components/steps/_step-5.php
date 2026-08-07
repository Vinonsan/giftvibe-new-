    <div id="gv-step-5" data-step-panel="5" class="space-y-4 hidden">

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold text-secondary">
                    Selling price (LKR) <span class="text-primary">*</span>
                </span>
                <input id="selling-price" required type="number" min="0" step="0.01"
                       name="selling_price"
                       value="<?= htmlspecialchars((string)($editProduct['base_price'] ?? '')) ?>"
                       class="<?= $fc ?>">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold text-secondary">
                    Cost / buying price (LKR) <span class="text-primary">*</span>
                </span>
                <input id="buying-price" required type="number" min="0" step="0.01"
                       name="buying_price"
                       value="<?= htmlspecialchars((string)($editProduct['cost_price'] ?? '')) ?>"
                       class="<?= $fc ?>">
            </label>
        </div>

        <!-- Live profit banner -->
        <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Profit per unit</p>
                <p id="profit-display" class="mt-0.5 text-2xl font-black text-emerald-600">LKR 0.00</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-200 text-emerald-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
        </div>

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Stock quantity</span>
            <input type="number" min="0" name="stock_quantity"
                   value="<?= (int)($editProduct['stock_quantity'] ?? 0) ?>"
                   class="<?= $fc ?>">
        </label>

        <div class="grid gap-3 sm:grid-cols-2">
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-secondary hover:border-primary/30 transition">
                Featured product
                <input type="checkbox" name="is_featured" value="1"
                       <?= !empty($editProduct['is_featured']) ? 'checked' : '' ?>
                       class="h-4 w-4 accent-primary">
            </label>
            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-secondary hover:border-primary/30 transition">
                Active / Visible
                <input type="checkbox" name="status" value="active"
                       <?= !$editProduct || $editProduct['status'] === 'active' ? 'checked' : '' ?>
                       class="h-4 w-4 accent-primary">
            </label>
        </div>
    </div>
