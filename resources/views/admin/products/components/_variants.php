<?php declare(strict_types=1); $productVariants = $productVariants ?? []; ?>
<section class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4" data-variants>
    <div class="flex items-center justify-between gap-3">
        <div><h3 class="text-sm font-bold text-secondary">Colour variants</h3><p class="mt-1 text-xs text-slate-500">Optional. Add each available colour, its selling price and stock.</p></div>
        <button type="button" data-add-variant class="shrink-0 rounded-lg border border-primary/20 bg-white px-3 py-2 text-xs font-bold text-primary hover:bg-primary/5">+ Add colour</button>
    </div>
    <div class="mt-4 space-y-3" data-variant-list>
        <?php foreach ($productVariants as $variant): ?>
            <div data-variant-row class="grid gap-2 rounded-xl border border-slate-200 bg-white p-3 sm:grid-cols-[1.2fr_1.2fr_.7fr_.8fr_auto]">
                <input type="hidden" name="variant_id[]" value="<?= (int) $variant['id'] ?>">
                <input name="variant_name[]" value="<?= htmlspecialchars((string) $variant['name']) ?>" placeholder="Variant name" class="<?= $fc ?>">
                <input name="variant_color[]" value="<?= htmlspecialchars((string) ($variant['color_name'] ?? '')) ?>" placeholder="Colour name" class="<?= $fc ?>">
                <input type="color" name="variant_hex[]" value="<?= htmlspecialchars((string) ($variant['color_hex'] ?: '#000000')) ?>" class="h-11 w-full rounded-xl border border-slate-200 bg-white p-1">
                <input type="number" min="0" step="0.01" name="variant_price[]" value="<?= htmlspecialchars((string) ((float) ($editProduct['base_price'] ?? 0) + (float) $variant['price_adjustment'])) ?>" placeholder="Price" class="<?= $fc ?>">
                <div class="flex gap-2"><input type="number" min="0" name="variant_stock[]" value="<?= (int) $variant['stock_quantity'] ?>" placeholder="Stock" class="<?= $fc ?>"><button type="button" data-remove-variant class="px-2 text-xl text-rose-500">&times;</button></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<script>
(function(){var root=document.currentScript.previousElementSibling,list=root.querySelector('[data-variant-list]');root.addEventListener('click',function(e){if(e.target.closest('[data-add-variant]')){var row=document.createElement('div');row.dataset.variantRow='';row.className='grid gap-2 rounded-xl border border-slate-200 bg-white p-3 sm:grid-cols-[1.2fr_1.2fr_.7fr_.8fr_auto]';row.innerHTML='<input type="hidden" name="variant_id[]" value="0"><input name="variant_name[]" placeholder="Variant name (e.g. Red)" class="<?= $fc ?>"><input name="variant_color[]" placeholder="Colour name" class="<?= $fc ?>"><input type="color" name="variant_hex[]" value="#000000" class="h-11 w-full rounded-xl border border-slate-200 bg-white p-1"><input type="number" min="0" step="0.01" name="variant_price[]" placeholder="Selling price" class="<?= $fc ?>"><div class="flex gap-2"><input type="number" min="0" name="variant_stock[]" value="0" placeholder="Stock" class="<?= $fc ?>"><button type="button" data-remove-variant class="px-2 text-xl text-rose-500">&times;</button></div>';list.appendChild(row);}var remove=e.target.closest('[data-remove-variant]');if(remove)remove.closest('[data-variant-row]').remove();});})();
</script>
