<?php

declare(strict_types=1);

/**
 * Select component — native <select> with placeholder, optgroups and multi-select.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/select.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $selectName         string   name attribute.
 *   $selectId           string   id attribute (defaults to $selectName).
 *   $selectOptions      array    List of options. Each entry is either:
 *                                  'value' => 'Label'                         (shorthand)
 *                                  ['value'=>, 'label'=>, 'disabled'=>bool, 'selected'=>bool]
 *                                  ['label' => 'Group title', 'options' => [...]]
 *   $selectValue        mixed    Selected value (scalar) — or array when multiple.
 *   $selectPlaceholder  string   Disabled placeholder option shown first.
 *   $selectLabel        string   Label above the field.
 *   $selectHint         string   Helper text under the field.
 *   $selectError        string   Error message (switches state to "error").
 *   $selectSize         string   sm | md | lg                                (default: md)
 *   $selectState        string   default | error | success | disabled
 *   $selectMultiple     bool     Enable multi-select.
 *   $selectRequired     bool     Add * to label + required attribute.
 *   $selectDisabled     bool     Disabled.
 *   $selectAttributes   array    Extra HTML attributes.
 *   $selectClass        string   Extra CSS classes.
 *
 * -----------------------------------------------------------------------------
 * Examples:
 * -----------------------------------------------------------------------------
 *   $selectName = 'category'; $selectLabel = 'Category';
 *   $selectOptions = ['' => 'Select…', 'gifts' => 'Gifts', 'flowers' => 'Flowers'];
 *   $selectValue = 'gifts'; require 'select.php';
 *
 *   $selectName = 'tags[]'; $selectMultiple = true;
 *   $selectOptions = [['label' => 'Themes', 'options' => ['birthday' => 'Birthday', 'wedding' => 'Wedding']]];
 *   $selectValue = ['birthday']; require 'select.php';
 *
 * @var string|null  $selectName
 * @var string|null  $selectId
 * @var array        $selectOptions
 * @var mixed        $selectValue
 * @var string|null  $selectPlaceholder
 * @var string|null  $selectLabel
 * @var string|null  $selectHint
 * @var string|null  $selectError
 * @var string       $selectSize
 * @var string       $selectState
 * @var bool         $selectMultiple
 * @var bool         $selectRequired
 * @var bool         $selectDisabled
 * @var array        $selectAttributes
 * @var string|null  $selectClass
 */

$selectName       = $selectName       ?? '';
$selectId         = $selectId         ?? $selectName;
$selectOptions    = $selectOptions    ?? [];
$selectSize       = $selectSize       ?? 'md';
$selectValue      = $selectValue      ?? null;
$selectPlaceholder= $selectPlaceholder ?? '';
$selectLabel      = $selectLabel      ?? '';
$selectHint       = $selectHint       ?? '';
$selectError      = $selectError      ?? '';
$selectMultiple   = $selectMultiple   ?? false;
$selectRequired   = $selectRequired   ?? false;
$selectDisabled   = $selectDisabled   ?? false;
$selectClass      = $selectClass      ?? '';

$selectState = $selectState ?? ($selectError !== '' ? 'error' : 'default');

$selectSizes = [
    'sm' => 'py-1.5 pl-3 pr-10 text-sm',
    'md' => 'py-2 pl-3.5 pr-10 text-sm',
    'lg' => 'py-2.5 pl-4 pr-10 text-base',
];

$selectStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-400 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500',
];

$selectBase = 'w-full appearance-none rounded-lg border shadow-sm outline-none transition focus:ring-2';
$selectClasses = trim(implode(' ', [
    $selectBase,
    $selectMultiple ? 'py-2 pl-3 pr-3' : $selectSizes[$selectSize],
    $selectStateClasses[$selectState] ?? $selectStateClasses['default'],
    $selectClass,
]));

/* Build extra attributes. */
$selectAttr = '';
foreach ($selectAttributes ?? [] as $attrName => $attrValue) {
    $selectAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

/* Normalise selected value(s) to an array for easy matching. */
$selectedValues = is_array($selectValue) ? $selectValue : [$selectValue];
$selectedValues = array_map(static fn ($v) => (string) $v, array_filter($selectedValues, static fn ($v) => $v !== null));

/**
 * Render one <option>.
 */
$renderOption = static function (string $value, string $label, bool $disabled = false, bool $forcedSelected = false) use ($selectedValues): string {
    $selected = $forcedSelected || in_array($value, $selectedValues, true);
    return sprintf(
        '<option value="%s"%s%s>%s</option>',
        htmlspecialchars($value, ENT_QUOTES),
        $disabled ? ' disabled' : '',
        $selected ? ' selected' : '',
        htmlspecialchars($label),
    );
};

/* Build <option>/<optgroup> HTML. */
$optionsHtml = '';
foreach ($selectOptions as $key => $entry) {
    if (is_array($entry) && isset($entry['options'])) {
        /* Optgroup. */
        $groupLabel = $entry['label'] ?? $key;
        $optionsHtml .= '<optgroup label="' . htmlspecialchars((string) $groupLabel) . '">';
        foreach ($entry['options'] as $optKey => $optEntry) {
            if (is_array($optEntry)) {
                $optionsHtml .= $renderOption(
                    (string) $optEntry['value'],
                    (string) ($optEntry['label'] ?? $optEntry['value']),
                    (bool) ($optEntry['disabled'] ?? false),
                    (bool) ($optEntry['selected'] ?? false),
                );
            } else {
                $optionsHtml .= $renderOption((string) $optKey, (string) $optEntry);
            }
        }
        $optionsHtml .= '</optgroup>';
    } elseif (is_array($entry)) {
        /* Full option spec. */
        $optionsHtml .= $renderOption(
            (string) $entry['value'],
            (string) ($entry['label'] ?? $entry['value']),
            (bool) ($entry['disabled'] ?? false),
            (bool) ($entry['selected'] ?? false),
        );
    } else {
        /* Shorthand: value => label. */
        $optionsHtml .= $renderOption((string) $key, (string) $entry);
    }
}

$labelId = $selectId !== '' ? $selectId : $selectName;
?>
<div class="space-y-1.5">
    <?php if ($selectLabel !== ''): ?>
        <label for="<?= htmlspecialchars($labelId) ?>" class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $selectLabel) ?>
            <?php if ($selectRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <select
            id="<?= htmlspecialchars($labelId) ?>"
            name="<?= htmlspecialchars($selectName) ?>"
            class="<?= $selectClasses ?>"
            <?= $selectMultiple ? ' multiple' : '' ?>
            <?= $selectRequired ? ' required' : '' ?>
            <?= $selectDisabled || $selectState === 'disabled' ? ' disabled' : '' ?>
            aria-invalid="<?= $selectError !== '' ? 'true' : 'false' ?>"
            <?= $selectAttr ?>
        >
            <?php if ($selectPlaceholder !== ''): ?>
                <option value="" disabled selected><?= htmlspecialchars($selectPlaceholder) ?></option>
            <?php endif; ?>
            <?= $optionsHtml ?>
        </select>

        <?php if (!$selectMultiple): ?>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </span>
        <?php endif; ?>
    </div>

    <?php if ($selectError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($selectError) ?></p>
    <?php elseif ($selectHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($selectHint) ?></p>
    <?php endif; ?>
</div>
