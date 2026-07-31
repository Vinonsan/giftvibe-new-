<?php

declare(strict_types=1);

/**
 * Phone input component — country-code select + telephone number field.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/phone-input.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $phoneInputName       string   name attribute of the phone number field.
 *   $phoneInputId         string   id (defaults to $phoneInputName).
 *   $phoneInputValue      string   Current phone number value.
 *   $phoneCodeName        string   name of the country-code select (default: country_code).
 *   $phoneCodeValue       string   Selected dial code, e.g. '94' (default: 94 — Sri Lanka).
 *   $phoneCountries       array    List of countries: ['code'=>'US','dial'=>'1','flag'=>'🇺🇸','name'=>'United States'].
 *                                 Uses a sensible default list when omitted.
 *   $phoneInputLabel      string   Label above the field.
 *   $phoneInputHint       string   Helper text.
 *   $phoneInputError      string   Error message (switches state to "error").
 *   $phoneInputSize       string   sm | md | lg                                (default: md)
 *   $phoneInputState      string   default | error | success | disabled
 *   $phoneInputRequired   bool     Add * to label + required attribute.
 *   $phoneInputDisabled   bool     Disabled.
 *   $phoneInputAttributes array    Extra HTML attributes on the tel input.
 *   $phoneInputClass      string   Extra CSS classes.
 *
 * -----------------------------------------------------------------------------
 * Example:
 * -----------------------------------------------------------------------------
 *   $phoneInputName = 'mobile'; $phoneInputLabel = 'Mobile number'; require 'phone-input.php';
 *
 * @var string|null  $phoneInputName
 * @var string|null  $phoneInputId
 * @var string|null  $phoneInputValue
 * @var string       $phoneCodeName
 * @var string       $phoneCodeValue
 * @var array        $phoneCountries
 * @var string|null  $phoneInputLabel
 * @var string|null  $phoneInputHint
 * @var string|null  $phoneInputError
 * @var string       $phoneInputSize
 * @var string       $phoneInputState
 * @var bool         $phoneInputRequired
 * @var bool         $phoneInputDisabled
 * @var array        $phoneInputAttributes
 * @var string|null  $phoneInputClass
 */

$phoneInputName     = $phoneInputName     ?? '';
$phoneInputId       = $phoneInputId       ?? $phoneInputName;
$phoneInputValue    = $phoneInputValue    ?? '';
$phoneCodeName      = $phoneCodeName      ?? 'country_code';
$phoneCodeValue     = $phoneCodeValue     ?? '94';
$phoneInputLabel    = $phoneInputLabel    ?? '';
$phoneInputHint     = $phoneInputHint     ?? '';
$phoneInputError    = $phoneInputError    ?? '';
$phoneInputSize     = $phoneInputSize     ?? 'md';
$phoneInputRequired = $phoneInputRequired ?? false;
$phoneInputDisabled = $phoneInputDisabled ?? false;
$phoneInputClass    = $phoneInputClass    ?? '';

$phoneInputState = $phoneInputState ?? ($phoneInputError !== '' ? 'error' : 'default');

/* A sensible default list of countries (flag · dial code). */
$phoneCountries = $phoneCountries ?? [
    ['code' => 'US', 'dial' => '1',  'flag' => '🇺🇸', 'name' => 'United States'],
    ['code' => 'GB', 'dial' => '44', 'flag' => '🇬🇧', 'name' => 'United Kingdom'],
    ['code' => 'IN', 'dial' => '91', 'flag' => '🇮🇳', 'name' => 'India'],
    ['code' => 'LK', 'dial' => '94', 'flag' => '🇱🇰', 'name' => 'Sri Lanka'],
    ['code' => 'AE', 'dial' => '971','flag' => '🇦🇪', 'name' => 'United Arab Emirates'],
    ['code' => 'SA', 'dial' => '966','flag' => '🇸🇦', 'name' => 'Saudi Arabia'],
    ['code' => 'SG', 'dial' => '65', 'flag' => '🇸🇬', 'name' => 'Singapore'],
    ['code' => 'MY', 'dial' => '60', 'flag' => '🇲🇾', 'name' => 'Malaysia'],
    ['code' => 'AU', 'dial' => '61', 'flag' => '🇦🇺', 'name' => 'Australia'],
    ['code' => 'CA', 'dial' => '1',  'flag' => '🇨🇦', 'name' => 'Canada'],
    ['code' => 'DE', 'dial' => '49', 'flag' => '🇩🇪', 'name' => 'Germany'],
    ['code' => 'FR', 'dial' => '33', 'flag' => '🇫🇷', 'name' => 'France'],
    ['code' => 'IT', 'dial' => '39', 'flag' => '🇮🇹', 'name' => 'Italy'],
    ['code' => 'ES', 'dial' => '34', 'flag' => '🇪🇸', 'name' => 'Spain'],
    ['code' => 'JP', 'dial' => '81', 'flag' => '🇯🇵', 'name' => 'Japan'],
    ['code' => 'KR', 'dial' => '82', 'flag' => '🇰🇷', 'name' => 'South Korea'],
    ['code' => 'CN', 'dial' => '86', 'flag' => '🇨🇳', 'name' => 'China'],
    ['code' => 'BD', 'dial' => '880','flag' => '🇧🇩', 'name' => 'Bangladesh'],
    ['code' => 'NP', 'dial' => '977','flag' => '🇳🇵', 'name' => 'Nepal'],
    ['code' => 'PK', 'dial' => '92', 'flag' => '🇵🇰', 'name' => 'Pakistan'],
];

$phoneSizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-3.5 py-2 text-sm',
    'lg' => 'px-4 py-2.5 text-base',
];

$phoneStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-400 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500',
];

$phoneBase = 'rounded-lg border shadow-sm outline-none transition focus:ring-2';

/* Split the field border between the code select and the number input. */
$phoneCodeClasses = trim(implode(' ', [
    $phoneBase,
    'cursor-pointer border-r-0 bg-slate-50 pl-3 pr-1 font-medium text-secondary focus:ring-0',
    $phoneSizes[$phoneSize ?? $phoneInputSize],
    $phoneStateClasses[$phoneInputState] ?? $phoneStateClasses['default'],
    $phoneInputState === 'disabled' ? ' cursor-not-allowed' : '',
]));

$phoneNumberClasses = trim(implode(' ', [
    $phoneBase,
    'w-full',
    $phoneSizes[$phoneInputSize],
    $phoneStateClasses[$phoneInputState] ?? $phoneStateClasses['default'],
    $phoneInputClass,
]));

$phoneAttr = '';
foreach ($phoneInputAttributes ?? [] as $attrName => $attrValue) {
    $phoneAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $phoneInputId !== '' ? $phoneInputId : $phoneInputName;
?>
<div class="space-y-1.5">
    <?php if ($phoneInputLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $phoneInputLabel) ?>
            <?php if ($phoneInputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="flex">
        <select
            name="<?= htmlspecialchars($phoneCodeName) ?>"
            class="<?= $phoneCodeClasses ?>"
            <?= $phoneInputDisabled || $phoneInputState === 'disabled' ? ' disabled' : '' ?>
            aria-label="Country code"
        >
            <?php foreach ($phoneCountries as $country): ?>
                <option value="<?= htmlspecialchars((string) ($country['dial'] ?? '')) ?>"
                    <?= (string) ($country['dial'] ?? '') === (string) $phoneCodeValue ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string) ($country['flag'] ?? '')) ?> +<?= htmlspecialchars((string) ($country['dial'] ?? '')) ?> <?= htmlspecialchars((string) ($country['code'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="relative w-full">
            <input
                type="tel"
                id="<?= htmlspecialchars($labelId) ?>"
                name="<?= htmlspecialchars($phoneInputName) ?>"
                value="<?= htmlspecialchars((string) $phoneInputValue) ?>"
                placeholder="7XX XXX XXX"
                class="<?= $phoneNumberClasses ?>"
                <?= $phoneInputRequired ? ' required' : '' ?>
                <?= $phoneInputDisabled || $phoneInputState === 'disabled' ? ' disabled' : '' ?>
                aria-invalid="<?= $phoneInputError !== '' ? 'true' : 'false' ?>"
                <?= $phoneAttr ?>
            >
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                </svg>
            </span>
        </div>
    </div>

    <?php if ($phoneInputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($phoneInputError) ?></p>
    <?php elseif ($phoneInputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($phoneInputHint) ?></p>
    <?php endif; ?>
</div>
