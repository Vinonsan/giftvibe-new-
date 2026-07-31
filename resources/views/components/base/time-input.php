<?php

declare(strict_types=1);

/**
 * Time input component — uses Flatpickr library for a custom styled time picker.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/time-input.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $timeInputName       string   name attribute.
 *   $timeInputId         string   id attribute (defaults to $timeInputName).
 *   $timeInputValue      string   Value in H:i format, e.g. '14:30'.
 *   $timeInputLabel      string   Label shown above the field.
 *   $timeInputHint       string   Helper text under the field.
 *   $timeInputError      string   Error message (switches state to "error").
 *   $timeInputSize       string   sm | md | lg                                (default: md)
 *   $timeInputState      string   default | error | success | disabled
 *   $timeInputRequired   bool     Add * to label + required attribute.
 *   $timeInputDisabled   bool     Disabled.
 *   $timeInputAttributes array    Extra HTML attributes.
 *   $timeInputClass      string   Extra CSS classes.
 *
 * @var string|null  $timeInputName
 * @var string|null  $timeInputId
 * @var string|null  $timeInputValue
 * @var string|null  $timeInputLabel
 * @var string|null  $timeInputHint
 * @var string|null  $timeInputError
 * @var string       $timeInputSize
 * @var string       $timeInputState
 * @var bool         $timeInputRequired
 * @var bool         $timeInputDisabled
 * @var array        $timeInputAttributes
 * @var string|null  $timeInputClass
 */

$timeInputName     = $timeInputName     ?? '';
$timeInputId       = $timeInputId       ?? $timeInputName;
$timeInputValue    = $timeInputValue    ?? '';
$timeInputLabel    = $timeInputLabel    ?? '';
$timeInputHint     = $timeInputHint     ?? '';
$timeInputError    = $timeInputError    ?? '';
$timeInputSize     = $timeInputSize     ?? 'md';
$timeInputRequired = $timeInputRequired ?? false;
$timeInputDisabled = $timeInputDisabled ?? false;
$timeInputClass    = $timeInputClass    ?? '';

$timeInputState = $timeInputState ?? ($timeInputError !== '' ? 'error' : 'default');

$timeInputSizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-3.5 py-2 text-sm',
    'lg' => 'px-4 py-2.5 text-base',
];

$timeInputStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-500 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500',
];

$timeInputBase = 'w-full rounded-lg border pl-10 pr-3 shadow-sm outline-none transition focus:ring-2 cursor-pointer';
$timeInputClasses = trim(implode(' ', [
    $timeInputBase,
    $timeInputSizes[$timeInputSize],
    $timeInputStateClasses[$timeInputState] ?? $timeInputStateClasses['default'],
    $timeInputClass,
]));

$timeInputAttr = '';
foreach ($timeInputAttributes ?? [] as $attrName => $attrValue) {
    $timeInputAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $timeInputId !== '' ? $timeInputId : $timeInputName;
$wrapperId = 'time-wrapper-' . uniqid();
?>

<!-- Load Flatpickr assets dynamically if not present -->
<script>
if (!document.getElementById('flatpickr-css')) {
    const link = document.createElement('link');
    link.id = 'flatpickr-css';
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
    document.head.appendChild(link);
}
if (typeof window.flatpickr === 'undefined' && !document.getElementById('flatpickr-js')) {
    const script = document.createElement('script');
    script.id = 'flatpickr-js';
    script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
    document.head.appendChild(script);
}
</script>

<div class="space-y-1.5" id="<?= htmlspecialchars($wrapperId) ?>">
    <?php if ($timeInputLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $timeInputLabel) ?>
            <?php if ($timeInputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>

        <input
            type="text"
            id="<?= htmlspecialchars($labelId) ?>"
            name="<?= htmlspecialchars($timeInputName) ?>"
            value="<?= htmlspecialchars((string) $timeInputValue) ?>"
            class="<?= $timeInputClasses ?>"
            placeholder="09:00 AM"
            <?= $timeInputRequired ? ' required' : '' ?>
            <?= $timeInputDisabled || $timeInputState === 'disabled' ? ' disabled' : '' ?>
            aria-invalid="<?= $timeInputError !== '' ? 'true' : 'false' ?>"
            <?= $timeInputAttr ?>
        >
    </div>

    <?php if ($timeInputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($timeInputError) ?></p>
    <?php elseif ($timeInputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($timeInputHint) ?></p>
    <?php endif; ?>
</div>

<script>
(function () {
    const wrapper = document.getElementById('<?= $wrapperId ?>');
    if (!wrapper) return;

    const input = wrapper.querySelector('input');

    function init() {
        if (typeof window.flatpickr === 'undefined') {
            setTimeout(init, 50);
            return;
        }

        window.flatpickr(input, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false,
            allowInput: true,
            disableMobile: true,
        });
    }
    init();
})();
</script>
