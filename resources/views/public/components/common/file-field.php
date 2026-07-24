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
 * @var string|null $errorText
 */
$required = $required ?? false;
$disabled = $disabled ?? false;
?>
<div class="space-y-1">
    <label for="<?= e($id) ?>" class="block text-sm font-semibold text-slate-700">
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
        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-button file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary file:hover:bg-primary-100 <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>"
    >
    <?php if (!empty($errorText)): ?>
        <p class="text-xs text-danger" role="alert"><?= e($errorText) ?></p>
    <?php elseif (!empty($helpText)): ?>
        <p class="text-xs text-slate-400"><?= e($helpText) ?></p>
    <?php endif; ?>
</div>