<?php

declare(strict_types=1);

/**
 * Select component — custom dropdown select with placeholder, optgroups and checkbox multi-select.
 * Preserves the exact same options and structure but styles everything with brand colors and hides native browser blue overlays.
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
$selectPlaceholder= $selectPlaceholder ?? 'Choose option...';
$selectLabel      = $selectLabel      ?? '';
$selectHint       = $selectHint       ?? '';
$selectError      = $selectError      ?? '';
$selectMultiple   = $selectMultiple   ?? false;
$selectRequired   = $selectRequired   ?? false;
$selectDisabled   = $selectDisabled   ?? false;
$selectClass      = $selectClass      ?? '';

$selectState = $selectState ?? ($selectError !== '' ? 'error' : 'default');

$selectSizes = [
    'sm' => 'h-9 px-3 text-sm',
    'md' => 'h-10 px-3.5 text-sm',
    'lg' => 'h-11 px-4 text-base',
];

$selectStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-500 bg-rose-50/30 text-secondary focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500 opacity-60',
];

$selectBase = 'w-full appearance-none rounded-lg border shadow-sm outline-none transition focus:ring-2 cursor-pointer pr-10';
$selectClasses = trim(implode(' ', [
    $selectBase,
    $selectSizes[$selectSize],
    $selectStateClasses[$selectState] ?? $selectStateClasses['default'],
    $selectClass,
]));

/* Build extra attributes. */
$selectAttr = '';
foreach ($selectAttributes ?? [] as $attrName => $attrValue) {
    $selectAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$labelId = $selectId !== '' ? $selectId : $selectName;
$chevronColor = ($selectState === 'error') ? 'text-rose-600' : 'text-slate-400';

/* Normalize selected value(s) to an array for easy matching. */
$selectedValues = is_array($selectValue) ? $selectValue : [$selectValue];
$selectedValues = array_map(static fn ($v) => (string) $v, array_filter($selectedValues, static fn ($v) => $v !== null));

/* Flatten the options array for easy rendering and label calculations */
$flatOptions = [];
$addOption = function(string $value, string $label, string $group = '', bool $disabled = false) use (&$flatOptions, $selectedValues) {
    $isSelected = in_array($value, $selectedValues, true);
    $flatOptions[] = [
        'value' => $value,
        'label' => $label,
        'group' => $group,
        'disabled' => $disabled,
        'selected' => $isSelected
    ];
};

foreach ($selectOptions as $key => $entry) {
    if (is_array($entry) && isset($entry['options'])) {
        $groupLabel = $entry['label'] ?? $key;
        foreach ($entry['options'] as $optKey => $optEntry) {
            if (is_array($optEntry)) {
                $addOption(
                    (string)$optEntry['value'],
                    (string)($optEntry['label'] ?? $optEntry['value']),
                    (string)$groupLabel,
                    (bool)($optEntry['disabled'] ?? false)
                );
            } else {
                $addOption((string)$optKey, (string)$optEntry, (string)$groupLabel);
            }
        }
    } elseif (is_array($entry)) {
        $addOption(
            (string)$entry['value'],
            (string)($entry['label'] ?? $entry['value']),
            '',
            (bool)($entry['disabled'] ?? false)
        );
    } else {
        $addOption((string)$key, (string)$entry);
    }
}

/* Calculate initial display label text */
$selectedLabels = [];
foreach ($flatOptions as $opt) {
    if ($opt['selected']) {
        $selectedLabels[] = $opt['label'];
    }
}
$displayLabel = count($selectedLabels) > 0 ? implode(', ', $selectedLabels) : $selectPlaceholder;
$isPlaceholderActive = count($selectedLabels) === 0;
?>

<div class="space-y-1.5" 
     data-custom-select 
     data-name="<?= htmlspecialchars($selectName) ?>"
     data-placeholder="<?= htmlspecialchars($selectPlaceholder) ?>"
     <?= $selectMultiple ? 'data-multiple="true"' : '' ?>
>
    <?php if ($selectLabel !== ''): ?>
        <label class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $selectLabel) ?>
            <?php if ($selectRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <!-- Trigger button -->
        <button
            type="button"
            id="<?= htmlspecialchars($labelId) ?>"
            class="<?= $selectClasses ?> relative flex items-center justify-between text-left focus:outline-none"
            <?= $selectDisabled || $selectState === 'disabled' ? 'disabled' : '' ?>
            aria-haspopup="listbox"
            aria-expanded="false"
            <?= $selectAttr ?>
        >
            <span data-select-display-text class="truncate <?= $isPlaceholderActive ? 'text-slate-400' : '' ?>">
                <?= htmlspecialchars($displayLabel) ?>
            </span>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center <?= $chevronColor ?>">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </span>
        </button>

        <!-- Hidden input(s) to support HTML form submissions -->
        <div data-select-hidden-container>
            <?php if ($selectMultiple): ?>
                <?php foreach ($flatOptions as $opt): ?>
                    <?php if ($opt['selected']): ?>
                        <input type="hidden" name="<?= htmlspecialchars($selectName) ?>" value="<?= htmlspecialchars($opt['value']) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php if (count($selectedValues) > 0): ?>
                    <input type="hidden" name="<?= htmlspecialchars($selectName) ?>" value="<?= htmlspecialchars($selectedValues[0]) ?>">
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Dropdown Menu Options Panel -->
        <div
            data-select-options
            class="absolute z-30 mt-1 hidden w-full rounded-lg border border-slate-200 bg-white py-1 shadow-lg max-h-60 overflow-y-auto"
            role="listbox"
        >
            <?php
            $currentGroup = null;
            foreach ($flatOptions as $opt):
                if ($opt['group'] !== '' && $opt['group'] !== $currentGroup):
                    $currentGroup = $opt['group'];
                    ?>
                    <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-400 bg-slate-50/50"><?= htmlspecialchars($currentGroup) ?></div>
                <?php endif; ?>

                <?php
                $itemClass = 'flex items-center gap-2.5 px-3 py-2 text-sm cursor-pointer select-none transition ';
                if ($opt['selected'] && !$selectMultiple) {
                    $itemClass .= 'bg-primary/10 text-primary font-semibold';
                } elseif ($opt['selected'] && $selectMultiple) {
                    $itemClass .= 'text-primary font-semibold';
                } else {
                    $itemClass .= 'text-secondary hover:bg-slate-50 hover:text-slate-900';
                }
                if ($opt['disabled']) {
                    $itemClass .= ' pointer-events-none opacity-40';
                }
                ?>
                <div
                    data-select-option
                    data-value="<?= htmlspecialchars($opt['value']) ?>"
                    data-label="<?= htmlspecialchars($opt['label']) ?>"
                    data-selected="<?= $opt['selected'] ? 'true' : 'false' ?>"
                    class="<?= $itemClass ?>"
                    role="option"
                    aria-selected="<?= $opt['selected'] ? 'true' : 'false' ?>"
                >
                    <?php if ($selectMultiple): ?>
                        <!-- Checkbox Box for Multi-Select Options -->
                        <span 
                            data-select-checkbox
                            class="flex h-4 w-4 shrink-0 items-center justify-center rounded border border-slate-300 transition-colors <?= $opt['selected'] ? 'bg-primary border-primary' : 'bg-white' ?>"
                        >
                            <svg class="h-3 w-3 text-white <?= $opt['selected'] ? '' : 'hidden' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    <?php endif; ?>
                    
                    <span class="truncate"><?= htmlspecialchars($opt['label']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($selectError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($selectError) ?></p>
    <?php elseif ($selectHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($selectHint) ?></p>
    <?php endif; ?>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.customSelect) return;
    window.GiftVibeUI = window.GiftVibeUI || {};

    function closeAll(exceptEl) {
        document.querySelectorAll('[data-select-options]').forEach(function (el) {
            if (el !== exceptEl) {
                el.classList.add('hidden');
                const trigger = el.previousElementSibling.previousElementSibling;
                if (trigger) trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('click', function (e) {
        // 1. Click on Trigger
        const trigger = e.target.closest('[data-select-trigger]') || e.target.closest('button[aria-haspopup="listbox"]');
        if (trigger) {
            e.stopPropagation();
            const dropdown = trigger.nextElementSibling.nextElementSibling;
            const isHidden = dropdown.classList.contains('hidden');
            closeAll(dropdown);
            if (isHidden) {
                dropdown.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
            } else {
                dropdown.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            }
            return;
        }

        // 2. Click on Option
        const option = e.target.closest('[data-select-option]');
        if (option) {
            e.stopPropagation();
            const wrapper = option.closest('[data-custom-select]');
            const isMultiple = wrapper.hasAttribute('data-multiple');
            const value = option.getAttribute('data-value');
            const label = option.getAttribute('data-label');
            const dropdown = option.closest('[data-select-options]');
            const trigger = dropdown.previousElementSibling.previousElementSibling;

            if (!isMultiple) {
                dropdown.querySelectorAll('[data-select-option]').forEach(function (opt) {
                    opt.classList.remove('bg-primary/10', 'text-primary', 'font-semibold');
                    opt.setAttribute('data-selected', 'false');
                });
                option.classList.add('bg-primary/10', 'text-primary', 'font-semibold');
                option.setAttribute('data-selected', 'true');

                trigger.querySelector('[data-select-display-text]').textContent = label;
                trigger.querySelector('[data-select-display-text]').classList.remove('text-slate-400');

                const hiddenContainer = wrapper.querySelector('[data-select-hidden-container]');
                const name = wrapper.getAttribute('data-name');
                hiddenContainer.innerHTML = '<input type="hidden" name="' + name + '" value="' + value + '">';

                dropdown.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            } else {
                const isSelected = option.getAttribute('data-selected') === 'true';
                const nextSelected = !isSelected;
                option.setAttribute('data-selected', nextSelected ? 'true' : 'false');

                const checkbox = option.querySelector('[data-select-checkbox]');
                const checkSvg = checkbox.querySelector('svg');
                if (nextSelected) {
                    checkbox.classList.add('bg-primary', 'border-primary');
                    checkSvg.classList.remove('hidden');
                    option.classList.add('text-primary', 'font-semibold');
                } else {
                    checkbox.classList.remove('bg-primary', 'border-primary');
                    checkSvg.classList.add('hidden');
                    option.classList.remove('text-primary', 'font-semibold');
                }

                const selectedLabels = [];
                const selectedValues = [];
                dropdown.querySelectorAll('[data-select-option][data-selected="true"]').forEach(function (opt) {
                    selectedLabels.push(opt.getAttribute('data-label'));
                    selectedValues.push(opt.getAttribute('data-value'));
                });

                const displayText = trigger.querySelector('[data-select-display-text]');
                if (selectedLabels.length > 0) {
                    displayText.textContent = selectedLabels.join(', ');
                    displayText.classList.remove('text-slate-400');
                } else {
                    displayText.textContent = wrapper.getAttribute('data-placeholder') || 'Choose options...';
                    displayText.classList.add('text-slate-400');
                }

                const hiddenContainer = wrapper.querySelector('[data-select-hidden-container]');
                const name = wrapper.getAttribute('data-name');
                let inputsHtml = '';
                selectedValues.forEach(function (val) {
                    inputsHtml += '<input type="hidden" name="' + name + '" value="' + val + '">';
                });
                hiddenContainer.innerHTML = inputsHtml;
            }
            return;
        }

        closeAll();
    });

    window.GiftVibeUI.customSelect = true;
})();
</script>
