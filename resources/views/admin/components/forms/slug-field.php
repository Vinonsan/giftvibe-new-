<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var string|null $value
 * @var string $sourceId Input ID to generate slug from
 */
?>
<div class="space-y-1">
    <label for="<?= e($id) ?>" class="block text-xs font-bold text-slate-700 uppercase tracking-wider"><?= e($label) ?></label>
    <div class="flex rounded-button shadow-sm">
        <span class="inline-flex items-center rounded-l-button border border-r-0 border-slate-300 bg-slate-50 px-3 text-slate-400 text-xs select-none">/products/</span>
        <input 
            type="text" 
            name="<?= e($name) ?>" 
            id="<?= e($id) ?>" 
            value="<?= e($value ?? '') ?>" 
            readonly
            class="block w-full min-w-0 flex-1 rounded-none rounded-r-button border border-slate-300 px-3 py-2 text-sm text-slate-500 bg-slate-50 focus:outline-none"
        >
    </div>
</div>