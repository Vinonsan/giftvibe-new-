<?php
declare(strict_types=1);

/**
 * @var array $productOptions
 * @var string $fc
 * @var callable $normalizeImage
 */
?>
<div id="product-options-container" class="space-y-6">
    <?php if (!empty($productOptions)): ?>
        <?php foreach ($productOptions as $oIndex => $opt): ?>
            <div class="option-group relative rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm" data-index="<?= $oIndex ?>">
                <input type="hidden" name="option_id[<?= $oIndex ?>]" value="<?= (int) $opt['id'] ?>">
                
                <button type="button" onclick="this.closest('.option-group').remove()" class="absolute right-4 top-4 text-slate-400 hover:text-rose-500" aria-label="Remove option">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
                
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 pr-10">
                    <div>
                        <label class="block text-xs font-bold text-secondary">Option Name</label>
                        <input type="text" name="option_name[<?= $oIndex ?>]" value="<?= htmlspecialchars((string) $opt['name']) ?>" required class="<?= $fc ?> mt-1.5" placeholder="e.g. Bouquet Variety">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-secondary">Input Type</label>
                        <select name="option_type[<?= $oIndex ?>]" required class="<?= $fc ?> mt-1.5" onchange="toggleImageUploads(this, <?= $oIndex ?>)">
                            <option value="text" <?= $opt['type'] === 'text' ? 'selected' : '' ?>>Text Field</option>
                            <option value="textarea" <?= $opt['type'] === 'textarea' ? 'selected' : '' ?>>Text Area</option>
                            <option value="select" <?= $opt['type'] === 'select' ? 'selected' : '' ?>>Dropdown Select</option>
                            <option value="radio" <?= $opt['type'] === 'radio' ? 'selected' : '' ?>>Radio Buttons</option>
                            <option value="checkbox" <?= $opt['type'] === 'checkbox' ? 'selected' : '' ?>>Checkboxes</option>
                            <option value="image_select" <?= $opt['type'] === 'image_select' ? 'selected' : '' ?>>Image Select (Buttons)</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="flex items-center gap-2 text-sm font-semibold text-secondary cursor-pointer">
                            <input type="checkbox" name="option_required[<?= $oIndex ?>]" value="1" <?= $opt['is_required'] ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                            Required Field
                        </label>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-200 pt-5">
                    <div class="mb-3 flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Option Values</h4>
                        <button type="button" onclick="addOptionValue(this.closest('.option-group'), <?= $oIndex ?>)" class="text-xs font-bold text-primary hover:underline">+ Add Value</button>
                    </div>
                    
                    <div class="option-values-list space-y-3">
                        <?php if (!empty($opt['values'])): ?>
                            <?php foreach ($opt['values'] as $vIndex => $val): ?>
                                <div class="option-value flex items-center gap-3">
                                    <input type="hidden" name="optval_id[<?= $oIndex ?>][]" value="<?= (int) $val['id'] ?>">
                                    <div class="flex-1">
                                        <input type="text" name="optval_label[<?= $oIndex ?>][]" value="<?= htmlspecialchars((string) $val['label']) ?>" placeholder="Label (e.g. Red, Blue, Small)" required class="<?= $fc ?>">
                                    </div>
                                    <div class="w-32">
                                        <input type="number" step="0.01" name="optval_price[<?= $oIndex ?>][]" value="<?= (float) $val['price_adjustment'] ?>" placeholder="+ Price" class="<?= $fc ?>">
                                    </div>
                                    <div class="image-upload-wrapper <?= $opt['type'] === 'image_select' ? '' : 'hidden' ?> w-48">
                                        <input type="file" name="optval_image[<?= $oIndex ?>][]" accept="image/jpeg,image/png,image/webp" class="<?= $fc ?> p-1 text-[11px]">
                                        <?php if (!empty($val['image_path'])): ?>
                                            <a href="<?= htmlspecialchars($normalizeImage((string) $val['image_path'])) ?>" target="_blank" class="mt-1 inline-block text-[10px] text-primary hover:underline">View Current Image</a>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" onclick="this.closest('.option-value').remove()" class="text-slate-400 hover:text-rose-500 p-2" aria-label="Remove value">&times;</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="option-value flex items-center gap-3">
                                <input type="hidden" name="optval_id[<?= $oIndex ?>][]" value="0">
                                <div class="flex-1">
                                    <input type="text" name="optval_label[<?= $oIndex ?>][]" placeholder="Label (e.g. Red, Blue, Small)" required class="<?= $fc ?>">
                                </div>
                                <div class="w-32">
                                    <input type="number" step="0.01" name="optval_price[<?= $oIndex ?>][]" placeholder="+ Price" class="<?= $fc ?>">
                                </div>
                                <div class="image-upload-wrapper <?= $opt['type'] === 'image_select' ? '' : 'hidden' ?> w-48">
                                    <input type="file" name="optval_image[<?= $oIndex ?>][]" accept="image/jpeg,image/png,image/webp" class="<?= $fc ?> p-1 text-[11px]">
                                </div>
                                <button type="button" onclick="this.closest('.option-value').remove()" class="text-slate-400 hover:text-rose-500 p-2" aria-label="Remove value">&times;</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="mt-6 flex justify-center border-t border-slate-200 border-dashed pt-6">
    <button type="button" onclick="addOptionGroup()" class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-primary/30 bg-primary/5 px-6 py-3 text-sm font-bold text-primary transition hover:bg-primary/10 hover:border-primary/50">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Add Custom Option
    </button>
</div>

<template id="option-group-template">
    <div class="option-group relative rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm" data-index="{INDEX}">
        <input type="hidden" name="option_id[{INDEX}]" value="0">
        
        <button type="button" onclick="this.closest('.option-group').remove()" class="absolute right-4 top-4 text-slate-400 hover:text-rose-500" aria-label="Remove option">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </button>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 pr-10">
            <div>
                <label class="block text-xs font-bold text-secondary">Option Name</label>
                <input type="text" name="option_name[{INDEX}]" required class="<?= $fc ?> mt-1.5" placeholder="e.g. Bouquet Variety">
            </div>
            <div>
                <label class="block text-xs font-bold text-secondary">Input Type</label>
                <select name="option_type[{INDEX}]" required class="<?= $fc ?> mt-1.5" onchange="toggleImageUploads(this, {INDEX})">
                    <option value="text">Text Field</option>
                    <option value="textarea">Text Area</option>
                    <option value="select">Dropdown Select</option>
                    <option value="radio">Radio Buttons</option>
                    <option value="checkbox">Checkboxes</option>
                    <option value="image_select">Image Select (Buttons)</option>
                </select>
            </div>
            <div class="flex items-center pt-6">
                <label class="flex items-center gap-2 text-sm font-semibold text-secondary cursor-pointer">
                    <input type="checkbox" name="option_required[{INDEX}]" value="1" class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                    Required Field
                </label>
            </div>
        </div>

        <div class="mt-5 border-t border-slate-200 pt-5">
            <div class="mb-3 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Option Values</h4>
                <button type="button" onclick="addOptionValue(this.closest('.option-group'), {INDEX})" class="text-xs font-bold text-primary hover:underline">+ Add Value</button>
            </div>
            
            <div class="option-values-list space-y-3">
                <div class="option-value flex items-center gap-3">
                    <input type="hidden" name="optval_id[{INDEX}][]" value="0">
                    <div class="flex-1">
                        <input type="text" name="optval_label[{INDEX}][]" placeholder="Label (e.g. Red, Blue, Small)" required class="<?= $fc ?>">
                    </div>
                    <div class="w-32">
                        <input type="number" step="0.01" name="optval_price[{INDEX}][]" placeholder="+ Price" class="<?= $fc ?>">
                    </div>
                    <div class="image-upload-wrapper hidden w-48">
                        <input type="file" name="optval_image[{INDEX}][]" accept="image/jpeg,image/png,image/webp" class="<?= $fc ?> p-1 text-[11px]">
                    </div>
                    <button type="button" onclick="this.closest('.option-value').remove()" class="text-slate-400 hover:text-rose-500 p-2" aria-label="Remove value">&times;</button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="option-value-template">
    <div class="option-value flex items-center gap-3">
        <input type="hidden" name="optval_id[{INDEX}][]" value="0">
        <div class="flex-1">
            <input type="text" name="optval_label[{INDEX}][]" placeholder="Label" required class="<?= $fc ?>">
        </div>
        <div class="w-32">
            <input type="number" step="0.01" name="optval_price[{INDEX}][]" placeholder="+ Price" class="<?= $fc ?>">
        </div>
        <div class="image-upload-wrapper {IMG_HIDDEN} w-48">
            <input type="file" name="optval_image[{INDEX}][]" accept="image/jpeg,image/png,image/webp" class="<?= $fc ?> p-1 text-[11px]">
        </div>
        <button type="button" onclick="this.closest('.option-value').remove()" class="text-slate-400 hover:text-rose-500 p-2" aria-label="Remove value">&times;</button>
    </div>
</template>

<script>
let nextOptionIndex = <?= !empty($productOptions) ? count($productOptions) : 0 ?>;

function addOptionGroup() {
    const tpl = document.getElementById('option-group-template').innerHTML;
    const html = tpl.replace(/{INDEX}/g, nextOptionIndex++);
    const container = document.getElementById('product-options-container');
    container.insertAdjacentHTML('beforeend', html);
}

function addOptionValue(groupElem, index) {
    const isImageSelect = groupElem.querySelector('select[name^="option_type"]').value === 'image_select';
    const tpl = document.getElementById('option-value-template').innerHTML;
    const html = tpl.replace(/{INDEX}/g, index).replace('{IMG_HIDDEN}', isImageSelect ? '' : 'hidden');
    const list = groupElem.querySelector('.option-values-list');
    list.insertAdjacentHTML('beforeend', html);
}

function toggleImageUploads(selectElem, index) {
    const isImageSelect = selectElem.value === 'image_select';
    const group = selectElem.closest('.option-group');
    group.querySelectorAll('.image-upload-wrapper').forEach(wrapper => {
        if (isImageSelect) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    });
}
</script>
