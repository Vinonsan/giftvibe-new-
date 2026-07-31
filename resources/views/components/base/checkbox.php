<?php

declare(strict_types=1);

/**
 * Checkbox component — custom styled checkbox with support for sizes, colors, and error highlights.
 *
 * @var string|null  $checkboxName
 * @var string|null  $checkboxId
 * @var string|null  $checkboxValue
 * @var bool         $checkboxChecked
 * @var string|null  $checkboxLabel
 * @var string|null  $checkboxHint
 * @var string|null  $checkboxError
 * @var string       $checkboxSize
 * @var string       $checkboxColor
 * @var string       $checkboxState
 * @var bool         $checkboxRequired
 * @var bool         $checkboxDisabled
 * @var array        $checkboxAttributes
 * @var string|null  $checkboxClass
 */

$checkboxName       = $checkboxName       ?? '';
$checkboxId         = $checkboxId         ?? $checkboxName;
$checkboxValue      = $checkboxValue      ?? '1';
$checkboxChecked    = $checkboxChecked    ?? false;
$checkboxLabel      = $checkboxLabel      ?? '';
$checkboxHint       = $checkboxHint       ?? '';
$checkboxError      = $checkboxError      ?? '';
$checkboxSize       = $checkboxSize       ?? 'md';
$checkboxColor      = $checkboxColor      ?? 'primary';
$checkboxRequired   = $checkboxRequired   ?? false;
$checkboxDisabled   = $checkboxDisabled   ?? false;
$checkboxClass      = $checkboxClass      ?? '';

$checkboxState = $checkboxState ?? ($checkboxError !== '' ? 'error' : 'default');

$sizeClasses = [
    'sm' => 'h-4 w-4',
    'md' => 'h-5 w-5',
    'lg' => 'h-6 w-6',
];

$iconSizes = [
    'sm' => 'h-2.5 w-2.5',
    'md' => 'h-3.5 w-3.5',
    'lg' => 'h-4 w-4',
];

$colorClasses = [
    'primary'   => 'peer-checked:bg-primary peer-checked:border-primary peer-focus-visible:ring-primary/20',
    'secondary' => 'peer-checked:bg-secondary peer-checked:border-secondary peer-focus-visible:ring-secondary/20',
    'accent'    => 'peer-checked:bg-accent peer-checked:border-accent peer-focus-visible:ring-accent/20',
    'success'   => 'peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-focus-visible:ring-emerald-500/20',
    'warning'   => 'peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-focus-visible:ring-amber-500/20',
    'danger'    => 'peer-checked:bg-rose-600 peer-checked:border-rose-600 peer-focus-visible:ring-rose-500/20',
];

$borderStateClasses = [
    'default'  => 'border-slate-300 bg-white text-secondary',
    'error'    => 'border-rose-500 bg-rose-50/50 text-rose-900',
    'success'  => 'border-emerald-500 bg-emerald-50/50 text-emerald-900',
    'disabled' => 'border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed',
];

$labelId = $checkboxId !== '' ? $checkboxId : $checkboxName;

/* Build extra attributes. */
$checkboxAttr = '';
foreach ($checkboxAttributes ?? [] as $attrName => $attrValue) {
    $checkboxAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}
?>

<div class="space-y-1">
    <label class="inline-flex items-start gap-2.5 <?= $checkboxDisabled || $checkboxState === 'disabled' ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' ?> select-none">
        <div class="relative flex items-center pt-0.5">
            <input
                type="checkbox"
                id="<?= htmlspecialchars($labelId) ?>"
                name="<?= htmlspecialchars($checkboxName) ?>"
                value="<?= htmlspecialchars($checkboxValue) ?>"
                class="peer sr-only"
                <?= $checkboxChecked ? 'checked' : '' ?>
                <?= $checkboxRequired ? 'required' : '' ?>
                <?= $checkboxDisabled || $checkboxState === 'disabled' ? 'disabled' : '' ?>
                <?= $checkboxAttr ?>
            >
            
            <!-- Custom Styled Checkbox box -->
            <span class="flex shrink-0 items-center justify-center rounded border transition focus-visible:ring-2 peer-checked:[&_svg]:block <?= $sizeClasses[$checkboxSize] ?> <?= $borderStateClasses[$checkboxState] ?? $borderStateClasses['default'] ?> <?= $colorClasses[$checkboxColor] ?> <?= $checkboxClass ?>">
                <!-- Checkmark Icon -->
                <svg class="text-white hidden <?= $iconSizes[$checkboxSize] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
        </div>

        <?php if ($checkboxLabel !== ''): ?>
            <div class="space-y-0.5">
                <span class="text-sm font-medium <?= $checkboxState === 'error' ? 'text-rose-600' : 'text-secondary' ?>">
                    <?= htmlspecialchars($checkboxLabel) ?>
                    <?php if ($checkboxRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
                </span>
                <?php if ($checkboxHint !== ''): ?>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($checkboxHint) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </label>

    <?php if ($checkboxError !== ''): ?>
        <p class="text-xs font-medium text-rose-600 pl-7"><?= htmlspecialchars($checkboxError) ?></p>
    <?php endif; ?>
</div>
