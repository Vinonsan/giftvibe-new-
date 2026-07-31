<?php

declare(strict_types=1);

/**
 * Radio component — custom styled radio button with support for sizes, colors, and error highlights.
 *
 * @var string|null  $radioName
 * @var string|null  $radioId
 * @var string|null  $radioValue
 * @var bool         $radioChecked
 * @var string|null  $radioLabel
 * @var string|null  $radioHint
 * @var string|null  $radioError
 * @var string       $radioSize
 * @var string       $radioColor
 * @var string       $radioState
 * @var bool         $radioRequired
 * @var bool         $radioDisabled
 * @var array        $radioAttributes
 * @var string|null  $radioClass
 */

$radioName       = $radioName       ?? '';
$radioId         = $radioId         ?? '';
$radioValue      = $radioValue      ?? '';
$radioChecked    = $radioChecked    ?? false;
$radioLabel      = $radioLabel      ?? '';
$radioHint       = $radioHint       ?? '';
$radioError      = $radioError      ?? '';
$radioSize       = $radioSize       ?? 'md';
$radioColor      = $radioColor      ?? 'primary';
$radioRequired   = $radioRequired   ?? false;
$radioDisabled   = $radioDisabled   ?? false;
$radioClass      = $radioClass      ?? '';

$radioState = $radioState ?? ($radioError !== '' ? 'error' : 'default');

$sizeClasses = [
    'sm' => 'h-4 w-4',
    'md' => 'h-5 w-5',
    'lg' => 'h-6 w-6',
];

$dotSizes = [
    'sm' => 'h-1.5 w-1.5',
    'md' => 'h-2.5 w-2.5',
    'lg' => 'h-3 w-3',
];

$colorClasses = [
    'primary'   => 'peer-checked:border-primary peer-checked:text-primary peer-focus-visible:ring-primary/20',
    'secondary' => 'peer-checked:border-secondary peer-checked:text-secondary peer-focus-visible:ring-secondary/20',
    'accent'    => 'peer-checked:border-accent peer-checked:text-accent peer-focus-visible:ring-accent/20',
    'success'   => 'peer-checked:border-emerald-600 peer-checked:text-emerald-600 peer-focus-visible:ring-emerald-500/20',
    'warning'   => 'peer-checked:border-amber-500 peer-checked:text-amber-500 peer-focus-visible:ring-amber-500/20',
    'danger'    => 'peer-checked:border-rose-600 peer-checked:text-rose-600 peer-focus-visible:ring-rose-500/20',
];

$borderStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary',
    'error'    => 'border-rose-500 bg-rose-50/50 text-rose-900',
    'success'  => 'border-emerald-500 bg-emerald-50/50 text-emerald-900',
    'disabled' => 'border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed',
];

$labelId = $radioId !== '' ? $radioId : ($radioName . '-' . uniqid());

/* Build extra attributes. */
$radioAttr = '';
foreach ($radioAttributes ?? [] as $attrName => $attrValue) {
    $radioAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}
?>

<div class="space-y-1">
    <label class="inline-flex items-start gap-2.5 <?= $radioDisabled || $radioState === 'disabled' ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' ?> select-none">
        <div class="relative flex items-center pt-0.5">
            <input
                type="radio"
                id="<?= htmlspecialchars($labelId) ?>"
                name="<?= htmlspecialchars($radioName) ?>"
                value="<?= htmlspecialchars($radioValue) ?>"
                class="peer sr-only"
                <?= $radioChecked ? 'checked' : '' ?>
                <?= $radioRequired ? 'required' : '' ?>
                <?= $radioDisabled || $radioState === 'disabled' ? 'disabled' : '' ?>
                <?= $radioAttr ?>
            >
            
            <!-- Custom Styled Radio circle -->
            <span class="flex shrink-0 items-center justify-center rounded-full border transition focus-visible:ring-2 peer-checked:[&_span]:block <?= $sizeClasses[$radioSize] ?> <?= $borderStateClasses[$radioState] ?? $borderStateClasses['default'] ?> <?= $colorClasses[$radioColor] ?> <?= $radioClass ?>">
                <!-- Center Dot -->
                <span class="rounded-full bg-current hidden <?= $dotSizes[$radioSize] ?>"></span>
            </span>
        </div>

        <?php if ($radioLabel !== ''): ?>
            <div class="space-y-0.5">
                <span class="text-sm font-medium <?= $radioState === 'error' ? 'text-rose-600' : 'text-secondary' ?>">
                    <?= htmlspecialchars($radioLabel) ?>
                    <?php if ($radioRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
                </span>
                <?php if ($radioHint !== ''): ?>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($radioHint) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </label>

    <?php if ($radioError !== ''): ?>
        <p class="text-xs font-medium text-rose-600 pl-7"><?= htmlspecialchars($radioError) ?></p>
    <?php endif; ?>
</div>
