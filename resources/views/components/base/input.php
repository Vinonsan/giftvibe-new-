<?php

declare(strict_types=1);

/**
 * Input component — text, email, password, number, search, url, tel, date...
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/input.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $inputType           string   text | email | password | number | search | url | tel (default: text)
 *   $inputName           string   name attribute.
 *   $inputId             string   id attribute (defaults to $inputName).
 *   $inputValue          string   Current value.
 *   $inputPlaceholder    string   Placeholder text.
 *   $inputLabel          string   Label shown above the field.
 *   $inputHint           string   Small helper text under the field.
 *   $inputError          string   Error message (also switches state to "error").
 *   $inputSize           string   sm | md | lg                                (default: md)
 *   $inputState          string   default | error | success | disabled       (default: auto)
 *   $inputRequired       bool     Add a * to the label + required attribute.
 *   $inputAutocomplete   string   autocomplete attribute value.
 *   $inputReadonly       bool     readonly attribute.
 *   $inputDisabled       bool     disabled attribute.
 *   $inputLeadingIcon    string   Inline SVG (or HTML) shown inside the left side.
 *   $inputPrefix         string   Plain text prefix shown inside the left side.
 *   $inputTrailingIcon   string   Inline SVG (or HTML) shown inside the right side.
 *   $inputSuffix         string   Plain text suffix shown inside the right side.
 *   $inputAttributes     array    Extra HTML attributes (['maxlength' => 50, 'step' => '1']).
 *   $inputClass          string   Extra CSS classes on the <input>.
 *   $inputWrapperClass   string   Extra CSS classes on the outer wrapper.
 *
 * -----------------------------------------------------------------------------
 * Examples:
 * -----------------------------------------------------------------------------
 *   $inputName = 'email'; $inputType = 'email'; $inputLabel = 'Email'; $inputPlaceholder = 'you@example.com'; require 'input.php';
 *   $inputName = 'price'; $inputType = 'number'; $inputLabel = 'Price'; $inputPrefix = '$'; $inputError = 'Required'; require 'input.php';
 *   $inputName = 'search'; $inputType = 'search'; $inputLeadingIcon = '<svg ...>'; $inputPlaceholder = 'Search…'; require 'input.php';
 *
 * @var string|null  $inputType
 * @var string|null  $inputName
 * @var string|null  $inputId
 * @var string|null  $inputValue
 * @var string|null  $inputPlaceholder
 * @var string|null  $inputLabel
 * @var string|null  $inputHint
 * @var string|null  $inputError
 * @var string       $inputSize
 * @var string       $inputState
 * @var bool         $inputRequired
 * @var string|null  $inputAutocomplete
 * @var bool         $inputReadonly
 * @var bool         $inputDisabled
 * @var string|null  $inputLeadingIcon
 * @var string|null  $inputPrefix
 * @var string|null  $inputTrailingIcon
 * @var string|null  $inputSuffix
 * @var array        $inputAttributes
 * @var string|null  $inputClass
 * @var string|null  $inputWrapperClass
 */

$inputType  = $inputType  ?? 'text';
$inputSize  = $inputSize  ?? 'md';
$inputName  = $inputName  ?? '';
$inputId    = $inputId    ?? $inputName;
$inputValue = (string) ($inputValue ?? '');

$inputRequired  = $inputRequired  ?? false;
$inputReadonly  = $inputReadonly  ?? false;
$inputDisabled  = $inputDisabled  ?? false;
$inputError     = $inputError     ?? '';
$inputHint      = $inputHint      ?? '';
$inputPrefix    = $inputPrefix    ?? '';
$inputSuffix    = $inputSuffix    ?? '';
$inputClass     = $inputClass     ?? '';
$inputWrapperClass = $inputWrapperClass ?? '';

/* State detection — explicit state wins, otherwise derive from error. */
$inputState = $inputState ?? ($inputError !== '' ? 'error' : 'default');

$inputSizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-3.5 py-2 text-sm',
    'lg' => 'px-4 py-2.5 text-base',
];

$inputStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-500 bg-rose-50/30 text-secondary placeholder:text-slate-400 focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500 placeholder:text-slate-400',
];

/* Pad the input when it has a leading or trailing adornment. */
$inputPadding = '';
if ($inputLeadingIcon !== '' || $inputPrefix !== '') {
    $inputPadding .= ' pl-10';
}
if ($inputTrailingIcon !== '' || $inputSuffix !== '' || $inputType === 'password') {
    $inputPadding .= ' pr-10';
}

$inputBase = 'w-full rounded-lg border shadow-sm outline-none transition focus:ring-2';
$inputClasses = trim(implode(' ', [
    $inputBase,
    $inputSizes[$inputSize],
    $inputStateClasses[$inputState] ?? $inputStateClasses['default'],
    $inputPadding,
    $inputClass,
]));

/* Build extra attributes. */
$inputAttr = '';
foreach ($inputAttributes ?? [] as $attrName => $attrValue) {
    $inputAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $inputId !== '' ? $inputId : $inputName;
?>
<div class="space-y-1.5 <?= htmlspecialchars($inputWrapperClass) ?>">
    <?php if ($inputLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $inputLabel) ?>
            <?php if ($inputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <?php if ($inputLeadingIcon !== ''): ?>
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><?= $inputLeadingIcon ?></span>
        <?php elseif ($inputPrefix !== ''): ?>
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-medium text-slate-500"><?= htmlspecialchars($inputPrefix) ?></span>
        <?php endif; ?>

        <input
            type="<?= htmlspecialchars($inputType) ?>"
            id="<?= htmlspecialchars($labelId) ?>"
            name="<?= htmlspecialchars($inputName) ?>"
            value="<?= htmlspecialchars($inputValue) ?>"
            class="<?= $inputClasses ?>"
            <?= $inputPlaceholder !== '' ? 'placeholder="' . htmlspecialchars($inputPlaceholder) . '"' : '' ?>
            <?= $inputAutocomplete !== '' ? 'autocomplete="' . htmlspecialchars($inputAutocomplete) . '"' : '' ?>
            <?= $inputRequired ? ' required' : '' ?>
            <?= $inputReadonly ? ' readonly' : '' ?>
            <?= $inputDisabled || $inputState === 'disabled' ? ' disabled' : '' ?>
            aria-invalid="<?= $inputError !== '' ? 'true' : 'false' ?>"
            <?= $inputAttr ?>
        >

        <?php if ($inputType === 'password'): ?>
            <button
                type="button"
                data-password-toggle
                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer"
                aria-label="Toggle password visibility"
            >
                <svg data-eye-open class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg data-eye-closed class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.88 9.88a3 3 0 104.24 4.24M10.73 5.08A10.43 10.43 0 0112 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a9.82 9.82 0 01-1.39 2.5m-3.08 1.83A10.03 10.03 0 0112 19.5c-4.638 0-8.573-3.007-9.963-7.178.07-.207.07-.431 0-.639a9.86 9.86 0 012.7-4l-.07-.07M3 3l18 18"/>
                </svg>
            </button>
        <?php elseif ($inputTrailingIcon !== ''): ?>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400"><?= $inputTrailingIcon ?></span>
        <?php elseif ($inputSuffix !== ''): ?>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm font-medium text-slate-500"><?= htmlspecialchars($inputSuffix) ?></span>
        <?php endif; ?>
    </div>

    <?php if ($inputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($inputError) ?></p>
    <?php elseif ($inputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($inputHint) ?></p>
    <?php endif; ?>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.passwordToggle) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('[data-password-toggle]');
        if (!toggle) return;

        var relativeDiv = toggle.closest('.relative');
        if (!relativeDiv) return;

        var input = relativeDiv.querySelector('input');
        if (!input) return;

        var eyeOpen = toggle.querySelector('[data-eye-open]');
        var eyeClosed = toggle.querySelector('[data-eye-closed]');

        if (input.type === 'password') {
            input.type = 'text';
            if (eyeOpen) eyeOpen.classList.add('hidden');
            if (eyeClosed) eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            if (eyeOpen) eyeOpen.classList.remove('hidden');
            if (eyeClosed) eyeClosed.classList.add('hidden');
        }
    });

    window.GiftVibeUI.passwordToggle = true;
})();
</script>

