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
    window.GiftVibeUI = window.GiftVibeUI || {};
    function closeOthers(current) {
        document.querySelectorAll('[data-custom-select]').forEach(function (wrapper) {
            if (wrapper === current) return;
            wrapper.querySelector('[data-select-options]')?.classList.add('hidden');
            wrapper.querySelector('button[aria-haspopup="listbox"]')?.setAttribute('aria-expanded', 'false');
        });
    }
    document.querySelectorAll('[data-custom-select]').forEach(function (wrapper) {
        if (wrapper.dataset.selectReady === 'true') return;
        var trigger = wrapper.querySelector('button[aria-haspopup="listbox"]');
        var dropdown = wrapper.querySelector('[data-select-options]');
        var display = wrapper.querySelector('[data-select-display-text]');
        var hiddenContainer = wrapper.querySelector('[data-select-hidden-container]');
        var isMultiple = wrapper.hasAttribute('data-multiple');
        var name = wrapper.getAttribute('data-name');

        function sync() {
            var selected = Array.prototype.slice.call(dropdown.querySelectorAll('[data-select-option][data-selected="true"]'));
            dropdown.querySelectorAll('[data-select-option]').forEach(function (option) {
                var active = option.getAttribute('data-selected') === 'true';
                option.setAttribute('aria-selected', active ? 'true' : 'false');
                option.classList.toggle('bg-primary/10', active);
                option.classList.toggle('text-primary', active);
                option.classList.toggle('font-semibold', active);
                var box = option.querySelector('[data-select-checkbox]');
                if (box) {
                    box.classList.toggle('bg-primary', active);
                    box.classList.toggle('border-primary', active);
                    box.classList.toggle('bg-white', !active);
                    box.querySelector('svg')?.classList.toggle('hidden', !active);
                }
            });
            display.textContent = selected.length ? selected.map(function (option) { return option.getAttribute('data-label'); }).join(', ') : (wrapper.getAttribute('data-placeholder') || 'Choose options...');
            display.classList.toggle('text-slate-400', selected.length === 0);
            hiddenContainer.innerHTML = '';
            selected.forEach(function (option) {
                var input = document.createElement('input'); input.type = 'hidden'; input.name = name; input.value = option.getAttribute('data-value'); hiddenContainer.appendChild(input);
            });
            wrapper.dispatchEvent(new CustomEvent('select:change', {bubbles: true, detail: {values: selected.map(function (option) { return option.getAttribute('data-value'); })}}));
        }
        trigger.addEventListener('click', function (event) {
            event.stopPropagation(); closeOthers(wrapper); var opening = dropdown.classList.contains('hidden'); dropdown.classList.toggle('hidden', !opening); trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
        });
        dropdown.querySelectorAll('[data-select-option]').forEach(function (option) {
            option.addEventListener('click', function (event) {
                event.stopPropagation();
                if (isMultiple) option.setAttribute('data-selected', option.getAttribute('data-selected') === 'true' ? 'false' : 'true');
                else { dropdown.querySelectorAll('[data-select-option]').forEach(function (item) { item.setAttribute('data-selected', 'false'); }); option.setAttribute('data-selected', 'true'); dropdown.classList.add('hidden'); trigger.setAttribute('aria-expanded', 'false'); }
                sync();
            });
        });
        wrapper.dataset.selectReady = 'true';
    });
    if (!window.GiftVibeUI.customSelectOutsideClick) {
        document.addEventListener('click', function () { closeOthers(null); });
        window.GiftVibeUI.customSelectOutsideClick = true;
    }
    window.GiftVibeUI.customSelect = true;
})();
</script>
