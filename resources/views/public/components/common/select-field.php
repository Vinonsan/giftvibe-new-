<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var array $options Associative array: [['value' => 'val', 'label' => 'Display']]
 * @var string|null $selectedValue
 * @var bool|null $required
 * @var bool|null $disabled
 * @var string|null $helpText
 * @var string|null $errorText
 */
$required = $required ?? false;
$disabled = $disabled ?? false;
$errorId = $id . '-error';
$helpId = $id . '-help';
$describedBy = [];
if (!empty($errorText)) $describedBy[] = $errorId;
if (!empty($helpText)) $describedBy[] = $helpId;
?>
<div class="space-y-1">
    <label for="<?= e($id) ?>" class="block text-sm font-semibold text-slate-700">
        <?= e($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>
    <select 
        name="<?= e($name) ?>" 
        id="<?= e($id) ?>" 
        <?= $required ? 'required' : '' ?> 
        <?= $disabled ? 'disabled' : '' ?>
        aria-invalid="<?= !empty($errorText) ? 'true' : 'false' ?>"
        <?= !empty($describedBy) ? 'aria-describedby="' . implode(' ', $describedBy) . '"' : '' ?>
        class="block w-full rounded-button border px-3 py-2 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 transition-colors <?= !empty($errorText) ? 'border-danger focus:ring-danger-500' : 'border-slate-300 focus:ring-primary-500' ?> <?= $disabled ? 'bg-slate-50 cursor-not-allowed opacity-70' : '' ?>"
    >
        <?php foreach ($options as $option): ?>
            <option value="<?= e($option['value']) ?>" <?= (isset($selectedValue) && $option['value'] == $selectedValue) ? 'selected' : '' ?>>
                <?= e($option['label']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errorText)): ?>
        <p class="text-xs text-danger" id="<?= $errorId ?>" role="alert"><?= e($errorText) ?></p>
    <?php elseif (!empty($helpText)): ?>
        <p class="text-xs text-slate-400" id="<?= $helpId ?>"><?= e($helpText) ?></p>
    <?php endif; ?>
</div>