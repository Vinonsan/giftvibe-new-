<?php

declare(strict_types=1);

/**
 * Date input component — native HTML5 date picker with a calendar icon.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/date-input.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $dateInputName       string   name attribute.
 *   $dateInputId         string   id attribute (defaults to $dateInputName).
 *   $dateInputValue      string   Value in Y-m-d format, e.g. '2026-07-31'.
 *   $dateInputMin        string   Earliest allowed date (Y-m-d).
 *   $dateInputMax        string   Latest allowed date (Y-m-d).
 *   $dateInputLabel      string   Label shown above the field.
 *   $dateInputHint       string   Helper text under the field.
 *   $dateInputError      string   Error message (switches state to "error").
 *   $dateInputSize       string   sm | md | lg                                (default: md)
 *   $dateInputState      string   default | error | success | disabled
 *   $dateInputRequired   bool     Add * to label + required attribute.
 *   $dateInputDisabled   bool     Disabled.
 *   $dateInputAttributes array    Extra HTML attributes.
 *   $dateInputClass      string   Extra CSS classes.
 *
 * -----------------------------------------------------------------------------
 * Examples:
 * -----------------------------------------------------------------------------
 *   $dateInputName = 'event_date'; $dateInputLabel = 'Event date';
 *   $dateInputMin = date('Y-m-d'); require 'date-input.php';
 *
 *   $dateInputName = 'from'; $dateInputLabel = 'From'; $dateInputValue = '2026-01-01'; require 'date-input.php';
 *
 * @var string|null  $dateInputName
 * @var string|null  $dateInputId
 * @var string|null  $dateInputValue
 * @var string|null  $dateInputMin
 * @var string|null  $dateInputMax
 * @var string|null  $dateInputLabel
 * @var string|null  $dateInputHint
 * @var string|null  $dateInputError
 * @var string       $dateInputSize
 * @var string       $dateInputState
 * @var bool         $dateInputRequired
 * @var bool         $dateInputDisabled
 * @var array        $dateInputAttributes
 * @var string|null  $dateInputClass
 */

$dateInputName     = $dateInputName     ?? '';
$dateInputId       = $dateInputId       ?? $dateInputName;
$dateInputValue    = $dateInputValue    ?? '';
$dateInputMin      = $dateInputMin      ?? '';
$dateInputMax      = $dateInputMax      ?? '';
$dateInputLabel    = $dateInputLabel    ?? '';
$dateInputHint     = $dateInputHint     ?? '';
$dateInputError    = $dateInputError    ?? '';
$dateInputSize     = $dateInputSize     ?? 'md';
$dateInputRequired = $dateInputRequired ?? false;
$dateInputDisabled = $dateInputDisabled ?? false;
$dateInputClass    = $dateInputClass    ?? '';

$dateInputState = $dateInputState ?? ($dateInputError !== '' ? 'error' : 'default');

$dateInputSizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-3.5 py-2 text-sm',
    'lg' => 'px-4 py-2.5 text-base',
];

$dateInputStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-400 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500',
];

$dateInputBase = 'w-full rounded-lg border pl-10 pr-3 shadow-sm outline-none transition focus:ring-2';
$dateInputClasses = trim(implode(' ', [
    $dateInputBase,
    $dateInputSizes[$dateInputSize],
    $dateInputStateClasses[$dateInputState] ?? $dateInputStateClasses['default'],
    $dateInputClass,
]));

$dateInputAttr = '';
foreach ($dateInputAttributes ?? [] as $attrName => $attrValue) {
    $dateInputAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $dateInputId !== '' ? $dateInputId : $dateInputName;
?>
<div class="space-y-1.5">
    <?php if ($dateInputLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $dateInputLabel) ?>
            <?php if ($dateInputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
        </span>

        <input
            type="date"
            id="<?= htmlspecialchars($labelId) ?>"
            name="<?= htmlspecialchars($dateInputName) ?>"
            value="<?= htmlspecialchars((string) $dateInputValue) ?>"
            class="<?= $dateInputClasses ?>"
            <?= $dateInputMin !== '' ? 'min="' . htmlspecialchars($dateInputMin) . '"' : '' ?>
            <?= $dateInputMax !== '' ? 'max="' . htmlspecialchars($dateInputMax) . '"' : '' ?>
            <?= $dateInputRequired ? ' required' : '' ?>
            <?= $dateInputDisabled || $dateInputState === 'disabled' ? ' disabled' : '' ?>
            aria-invalid="<?= $dateInputError !== '' ? 'true' : 'false' ?>"
            <?= $dateInputAttr ?>
        >
    </div>

    <?php if ($dateInputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($dateInputError) ?></p>
    <?php elseif ($dateInputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($dateInputHint) ?></p>
    <?php endif; ?>
</div>
