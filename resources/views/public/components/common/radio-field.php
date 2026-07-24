<?php
/**
 * Variables:
 * @var string $label
 * @var string $name
 * @var string $id
 * @var string $value
 * @var string|null $selectedValue
 * @var bool|null $disabled
 */
$disabled = $disabled ?? false;
$isChecked = (isset($selectedValue) && $value == $selectedValue);
?>
<div class="flex items-center">
    <input 
        type="radio" 
        name="<?= e($name) ?>" 
        id="<?= e($id) ?>" 
        value="<?= e($value) ?>" 
        <?= $isChecked ? 'checked' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>
        class="h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-500 <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>"
    >
    <label for="<?= e($id) ?>" class="ml-3 block text-sm font-medium text-slate-700 <?= $disabled ? 'opacity-50 cursor-not-allowed' : '' ?>">
        <?= e($label) ?>
    </label>
</div>