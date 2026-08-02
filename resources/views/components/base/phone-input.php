<?php

declare(strict_types=1);

/**
 * Phone input component — uses intl-tel-input library for flag dropdowns and validation.
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
 *   $phoneCodeName        string   name of the country-code hidden field (default: country_code).
 *   $phoneCodeValue       string   Selected dial code, e.g. '94' (default: 94 — Sri Lanka).
 *   $phoneCountries       array    List of countries: ['code'=>'US','dial'=>'1','flag'=>'🇺🇸','name'=>'United States'].
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

/* Sensible fallback list of countries if not defined. */
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
];

/* Determine the initial country code matching the dial code. */
$initialCountryCode = 'lk';
foreach ($phoneCountries as $country) {
    if ((string) $country['dial'] === (string) $phoneCodeValue) {
        $initialCountryCode = strtolower($country['code']);
        break;
    }
}

$phoneSizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-3.5 py-2 text-sm',
    'lg' => 'px-4 py-2.5 text-base',
];

$phoneStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-500 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500',
];

$phoneBase = 'rounded-lg border shadow-sm outline-none transition focus:ring-2';
$phoneNumberClasses = trim(implode(' ', [
    $phoneBase,
    'w-full pl-12',
    $phoneSizes[$phoneInputSize],
    $phoneStateClasses[$phoneInputState] ?? $phoneStateClasses['default'],
    $phoneInputClass,
]));

$phoneAttr = '';
foreach ($phoneInputAttributes ?? [] as $attrName => $attrValue) {
    $phoneAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $phoneInputId !== '' ? $phoneInputId : $phoneInputName;
$wrapperId = 'phone-wrapper-' . uniqid();
?>

<!-- Load intl-tel-input assets dynamically if not already present in layouts -->
<script>
if (!document.getElementById('intl-tel-css')) {
    const link = document.createElement('link');
    link.id = 'intl-tel-css';
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.css';
    document.head.appendChild(link);
}
if (typeof window.intlTelInput === 'undefined' && !document.getElementById('intl-tel-js')) {
    const script = document.createElement('script');
    script.id = 'intl-tel-js';
    script.src = 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js';
    document.head.appendChild(script);
}
</script>

<div class="space-y-1.5" id="<?= htmlspecialchars($wrapperId) ?>">
    <?php if ($phoneInputLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $phoneInputLabel) ?>
            <?php if ($phoneInputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <input
            type="tel"
            id="<?= htmlspecialchars($labelId) ?>"
            name="<?= htmlspecialchars($phoneInputName) ?>"
            value="<?= htmlspecialchars((string) $phoneInputValue) ?>"
            class="<?= $phoneNumberClasses ?>"
            <?= $phoneInputRequired ? ' required' : '' ?>
            <?= $phoneInputDisabled || $phoneInputState === 'disabled' ? ' disabled' : '' ?>
            aria-invalid="<?= $phoneInputError !== '' ? 'true' : 'false' ?>"
            <?= $phoneAttr ?>
        >

        <!-- Hidden input to submit the country dial code -->
        <input
            type="hidden"
            name="<?= htmlspecialchars($phoneCodeName) ?>"
            value="<?= htmlspecialchars($phoneCodeValue) ?>"
        >
    </div>

    <?php if ($phoneInputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($phoneInputError) ?></p>
    <?php elseif ($phoneInputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($phoneInputHint) ?></p>
    <?php endif; ?>
</div>

<script>
(function () {
    const wrapper = document.getElementById('<?= $wrapperId ?>');
    if (!wrapper) return;

    const input = wrapper.querySelector('input[type="tel"]');
    const hiddenCode = wrapper.querySelector('input[type="hidden"]');

    function init() {
        if (typeof window.intlTelInput === 'undefined') {
            setTimeout(init, 50);
            return;
        }

        const iti = window.intlTelInput(input, {
            initialCountry: "<?= $initialCountryCode ?>",
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
            autoPlaceholder: "polite",
        });

        function syncCode() {
            const data = iti.getSelectedCountryData();
            if (data && data.dialCode) {
                hiddenCode.value = data.dialCode;
            }
        }

        input.addEventListener('countrychange', syncCode);
        input.addEventListener('input', syncCode);
        
        // Handle initial load sync
        setTimeout(syncCode, 100);
    }
    init();
})();
</script>
