<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var bool|null $required
 * @var bool|null $disabled
 * @var string|null $accept
 * @var string|null $helpText
 */
$required = $required ?? false;
$disabled = $disabled ?? false;
?>
<div class="space-y-1">
    <label for="<?= e($id) ?>" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
        <?= e($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>
    <input 
        type="file" 
        name="<?= e($name) ?>" 
        id="<?= e($id) ?>" 
        <?= $required ? 'required' : '' ?> 
        <?= $disabled ? 'disabled' : '' ?>
        <?= !empty($accept) ? 'accept="'.e($accept).'"' : '' ?>
        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-button file:border file:border-slate-300 file:text-xs file:font-semibold file:bg-slate-50 file:text-slate-700 file:hover:bg-slate-100 <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>"
    >
    <?php if (!empty($helpText)): ?>
        <p class="text-2xs text-slate-400"><?= e($helpText) ?></p>
    <?php endif; ?>
</div>