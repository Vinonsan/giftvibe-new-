<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var string|null $currentImageURL
 * @var string|null $helpText
 */
?>
<div class="space-y-2" data-image-preview-container>
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider"><?= e($label) ?></label>
    <div class="flex items-center gap-4">
        <!-- Preview -->
        <div class="w-16 h-16 rounded-card border border-slate-200 overflow-hidden bg-slate-50 flex items-center justify-center">
            <img src="<?= e($currentImageURL ?? '') ?>" alt="Preview" class="w-full h-full object-cover <?= empty($currentImageURL) ? 'hidden' : '' ?>" data-image-preview>
            <?php if (empty($currentImageURL)): ?>
                <div class="text-slate-300 flex items-center justify-center w-full h-full" data-image-placeholder>
                    <?php component('admin/components/common/icon', ['name' => 'upload', 'size' => 'sm']); ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Input -->
        <div class="flex-grow">
            <input 
                type="file" 
                name="<?= e($name) ?>" 
                id="<?= e($id) ?>" 
                accept="image/*"
                data-image-preview-input
                class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-button file:border file:border-slate-300 file:text-2xs file:font-bold file:bg-white file:text-slate-700 file:hover:bg-slate-50 cursor-pointer"
            >
            <?php if (!empty($helpText)): ?>
                <p class="text-2xs text-slate-400 mt-1"><?= e($helpText) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>