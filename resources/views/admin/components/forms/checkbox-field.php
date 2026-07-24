<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var bool|null $checked
 * @var bool|null $disabled
 * @var string|null $helpText
 */
$checked = $checked ?? false;
$disabled = $disabled ?? false;
?>
<div class="flex items-start">
    <div class="flex h-5 items-center">
        <input 
            type="checkbox" 
            name="<?= e($name) ?>" 
            id="<?= e($id) ?>" 
            value="1"
            <?= $checked ? 'checked' : '' ?>
            <?= $disabled ? 'disabled' : '' ?>
            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>"
        >
    </div>
    <div class="ml-3 text-sm">
        <label for="<?= e($id) ?>" class="font-bold text-slate-700 text-xs uppercase tracking-wider <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>">
            <?= e($label) ?>
        </label>
        <?php if (!empty($helpText)): ?>
            <p class="text-2xs text-slate-400"><?= e($helpText) ?></p>
        <?php endif; ?>
    </div>
</div>