<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var string|null $value
 * @var int|null $rows
 * @var string|null $placeholder
 * @var bool|null $required
 * @var bool|null $disabled
 * @var string|null $helpText
 * @var string|null $errorText
 */
$required = $required ?? false;
$disabled = $disabled ?? false;
$rows = $rows ?? 4;
?>
<div class="space-y-1">
    <label for="<?= e($id) ?>" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
        <?= e($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>
    <textarea 
        name="<?= e($name) ?>" 
        id="<?= e($id) ?>" 
        rows="<?= (int)$rows ?>"
        placeholder="<?= e($placeholder ?? '') ?>"
        <?= $required ? 'required' : '' ?> 
        <?= $disabled ? 'disabled' : '' ?>
        class="block w-full rounded-button border px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-1 transition-colors <?= !empty($errorText) ? 'border-danger focus:border-danger focus:ring-danger' : 'border-slate-300 focus:border-primary-500 focus:ring-primary-500' ?> <?= $disabled ? 'bg-slate-50 cursor-not-allowed opacity-70' : 'bg-white' ?>"
    ><?= e($value ?? '') ?></textarea>
    <?php if (!empty($errorText)): ?>
        <p class="text-xs text-danger font-semibold" role="alert"><?= e($errorText) ?></p>
    <?php elseif (!empty($helpText)): ?>
        <p class="text-2xs text-slate-400"><?= e($helpText) ?></p>
    <?php endif; ?>
</div>