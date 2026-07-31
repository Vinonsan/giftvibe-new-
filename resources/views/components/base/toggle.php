<?php

declare(strict_types=1);

/**
 * Toggle component — iOS-style toggle switch with support for sizes, colors, and error highlights.
 *
 * @var string|null  $toggleName
 * @var string|null  $toggleId
 * @var bool         $toggleChecked
 * @var string|null  $toggleLabel
 * @var string|null  $toggleHint
 * @var string|null  $toggleError
 * @var string       $toggleSize
 * @var string       $toggleColor
 * @var string       $toggleState
 * @var bool         $toggleRequired
 * @var bool         $toggleDisabled
 * @var array        $toggleAttributes
 * @var string|null  $toggleClass
 */

$toggleName       = $toggleName       ?? '';
$toggleId         = $toggleId         ?? $toggleName;
$toggleChecked    = $toggleChecked    ?? false;
$toggleLabel      = $toggleLabel      ?? '';
$toggleHint       = $toggleHint       ?? '';
$toggleError      = $toggleError      ?? '';
$toggleSize       = $toggleSize       ?? 'md';
$toggleColor      = $toggleColor      ?? 'primary';
$toggleRequired   = $toggleRequired   ?? false;
$toggleDisabled   = $toggleDisabled   ?? false;
$toggleClass      = $toggleClass      ?? '';

$toggleState = $toggleState ?? ($toggleError !== '' ? 'error' : 'default');

$sizeClasses = [
    'sm' => 'h-5 w-9',
    'md' => 'h-6.5 w-11',
    'lg' => 'h-8 w-14',
];

$thumbSizes = [
    'sm' => 'h-4 w-4',
    'md' => 'h-5.5 w-5.5',
    'lg' => 'h-7 w-7',
];

$thumbTranslations = [
    'sm' => 'peer-checked:[&>span]:translate-x-4',
    'md' => 'peer-checked:[&>span]:translate-x-4.5',
    'lg' => 'peer-checked:[&>span]:translate-x-6',
];

$colorClasses = [
    'primary'   => 'peer-checked:bg-primary peer-focus-visible:ring-primary/20',
    'secondary' => 'peer-checked:bg-secondary peer-focus-visible:ring-secondary/20',
    'accent'    => 'peer-checked:bg-accent peer-focus-visible:ring-accent/20',
    'success'   => 'peer-checked:bg-emerald-600 peer-focus-visible:ring-emerald-500/20',
    'warning'   => 'peer-checked:bg-amber-500 peer-focus-visible:ring-amber-500/20',
    'danger'    => 'peer-checked:bg-rose-600 peer-focus-visible:ring-rose-500/20',
];

$borderStateClasses = [
    'default'  => 'border border-slate-200 bg-slate-200',
    'error'    => 'border border-rose-500 bg-rose-50',
    'success'  => 'border border-emerald-500 bg-emerald-50',
    'disabled' => 'border border-slate-100 bg-slate-100 cursor-not-allowed',
];

$labelId = $toggleId !== '' ? $toggleId : $toggleName;

/* Build extra attributes. */
$toggleAttr = '';
foreach ($toggleAttributes ?? [] as $attrName => $attrValue) {
    $toggleAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}
?>

<div class="space-y-1">
    <label class="inline-flex items-start gap-2.5 <?= $toggleDisabled || $toggleState === 'disabled' ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' ?> select-none">
        <div class="relative flex items-center pt-0.5">
            <input
                type="checkbox"
                id="<?= htmlspecialchars($labelId) ?>"
                name="<?= htmlspecialchars($toggleName) ?>"
                class="peer sr-only"
                <?= $toggleChecked ? 'checked' : '' ?>
                <?= $toggleRequired ? 'required' : '' ?>
                <?= $toggleDisabled || $toggleState === 'disabled' ? 'disabled' : '' ?>
                <?= $toggleAttr ?>
            >
            
            <!-- Custom Styled Toggle track -->
            <span class="relative flex shrink-0 items-center rounded-full p-0.5 transition-colors duration-200 focus-visible:ring-2 <?= $sizeClasses[$toggleSize] ?> <?= $borderStateClasses[$toggleState] ?? $borderStateClasses['default'] ?> <?= $colorClasses[$toggleColor] ?> <?= $thumbTranslations[$toggleSize] ?> <?= $toggleClass ?>">
                <!-- Sliding Thumb circle -->
                <span class="inline-block transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out <?= $thumbSizes[$toggleSize] ?>"></span>
            </span>
        </div>

        <?php if ($toggleLabel !== ''): ?>
            <div class="space-y-0.5">
                <span class="text-sm font-medium <?= $toggleState === 'error' ? 'text-rose-600' : 'text-secondary' ?>">
                    <?= htmlspecialchars($toggleLabel) ?>
                    <?php if ($toggleRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
                </span>
                <?php if ($toggleHint !== ''): ?>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($toggleHint) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </label>

    <?php if ($toggleError !== ''): ?>
        <p class="text-xs font-medium text-rose-600 pl-12"><?= htmlspecialchars($toggleError) ?></p>
    <?php endif; ?>
</div>
