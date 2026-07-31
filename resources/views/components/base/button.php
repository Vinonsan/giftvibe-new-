<?php

declare(strict_types=1);

/**
 * Button component — solid, outline, soft, ghost and link variants.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/button.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $buttonLabel          string   Button text (required for text buttons).
 *   $buttonVariant        string   solid | outline | soft | ghost | link   (default: solid)
 *   $buttonColor          string   primary | secondary | accent | success |
 *                                  warning | danger                        (default: primary)
 *   $buttonSize           string   xs | sm | md | lg | xl                  (default: md)
 *   $buttonType           string   button | submit | reset                 (default: button)
 *   $buttonHref           string   When set, renders an <a> instead of <button>.
 *   $buttonName           string   name attribute.
 *   $buttonValue          string   value attribute.
 *   $buttonId             string   id attribute.
 *   $buttonIcon           string   Inline SVG (or HTML) shown BEFORE the label.
 *   $buttonIconTrailing   string   Inline SVG (or HTML) shown AFTER the label.
 *   $buttonIconOnly       bool     Render a square icon-only button (fixed width).
 *   $buttonFullWidth      bool     Add w-full.
 *   $buttonDisabled       bool     Disable the button.
 *   $buttonLoading        bool     Show a spinner and disable the button.
 *   $buttonOnclick        string   Inline onclick JS.
 *   $buttonClass          string   Extra CSS classes to append.
 *   $buttonAttributes     array    Extra HTML attributes, e.g. ['data-x' => '1', 'title' => 'Go'].
 *
 * -----------------------------------------------------------------------------
 * Examples:
 * -----------------------------------------------------------------------------
 *   $buttonLabel = 'Save'; require 'button.php';
 *   $buttonLabel = 'Add'; $buttonVariant = 'outline'; $buttonColor = 'success';
 *                       $buttonSize = 'sm'; $buttonIcon = '<svg ...></svg>'; require 'button.php';
 *   $buttonLabel = 'Edit'; $buttonVariant = 'ghost'; $buttonHref = '/products/1/edit'; require 'button.php';
 *   $buttonIcon = '<svg>...</svg>'; $buttonIconOnly = true; $buttonVariant = 'soft';
 *                 $buttonAttributes = ['title' => 'Delete']; require 'button.php';
 *
 * @var string|null  $buttonLabel
 * @var string       $buttonVariant
 * @var string       $buttonColor
 * @var string       $buttonSize
 * @var string       $buttonType
 * @var string|null  $buttonHref
 * @var string|null  $buttonName
 * @var string|null  $buttonValue
 * @var string|null  $buttonId
 * @var string|null  $buttonIcon
 * @var string|null  $buttonIconTrailing
 * @var bool         $buttonIconOnly
 * @var bool         $buttonFullWidth
 * @var bool         $buttonDisabled
 * @var bool         $buttonLoading
 * @var string|null  $buttonOnclick
 * @var string|null  $buttonClass
 * @var array        $buttonAttributes
 */

$buttonVariant = $buttonVariant ?? 'solid';
$buttonColor   = $buttonColor   ?? 'primary';
$buttonSize    = $buttonSize    ?? 'md';
$buttonType    = $buttonType    ?? 'button';

$buttonIconOnly  = $buttonIconOnly  ?? false;
$buttonFullWidth = $buttonFullWidth ?? false;
$buttonDisabled  = $buttonDisabled  ?? false;
$buttonLoading   = $buttonLoading   ?? false;
$buttonError     = $buttonError     ?? '';
$buttonState     = $buttonState     ?? ($buttonError !== '' ? 'error' : 'default');

$buttonLabel         = $buttonLabel         ?? '';
$buttonIcon          = $buttonIcon          ?? '';
$buttonIconTrailing  = $buttonIconTrailing  ?? '';
$buttonClass         = $buttonClass         ?? '';
$buttonAttributes    = $buttonAttributes    ?? [];

/* Variant × color class map — every combination. */
$buttonPalette = [
    'primary' => [
        'solid'   => 'bg-primary text-white hover:bg-primary/90 focus-visible:ring-primary/40 border border-transparent',
        'outline' => 'border border-primary/50 bg-transparent text-primary hover:bg-primary/10 focus-visible:ring-primary/30',
        'soft'    => 'bg-primary/10 text-primary hover:bg-primary/20 focus-visible:ring-primary/30 border border-transparent',
        'ghost'   => 'bg-transparent text-primary hover:bg-primary/10 focus-visible:ring-primary/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-primary underline-offset-4 hover:underline focus-visible:ring-primary/30 border border-transparent',
    ],
    'secondary' => [
        'solid'   => 'bg-secondary text-white hover:bg-slate-800 focus-visible:ring-secondary/40 border border-transparent',
        'outline' => 'border border-secondary/40 bg-transparent text-secondary hover:bg-secondary/10 focus-visible:ring-secondary/30',
        'soft'    => 'bg-secondary/10 text-secondary hover:bg-secondary/20 focus-visible:ring-secondary/30 border border-transparent',
        'ghost'   => 'bg-transparent text-secondary hover:bg-secondary/10 focus-visible:ring-secondary/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-secondary underline-offset-4 hover:underline focus-visible:ring-secondary/30 border border-transparent',
    ],
    'accent' => [
        'solid'   => 'bg-accent text-white hover:bg-pink-600 focus-visible:ring-accent/40 border border-transparent',
        'outline' => 'border border-accent/50 bg-transparent text-accent hover:bg-accent/10 focus-visible:ring-accent/30',
        'soft'    => 'bg-accent/10 text-accent hover:bg-accent/20 focus-visible:ring-accent/30 border border-transparent',
        'ghost'   => 'bg-transparent text-accent hover:bg-accent/10 focus-visible:ring-accent/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-accent underline-offset-4 hover:underline focus-visible:ring-accent/30 border border-transparent',
    ],
    'success' => [
        'solid'   => 'bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:ring-emerald-500/40 border border-transparent',
        'outline' => 'border border-emerald-600/50 bg-transparent text-emerald-700 hover:bg-emerald-600/10 focus-visible:ring-emerald-500/30',
        'soft'    => 'bg-emerald-600/10 text-emerald-700 hover:bg-emerald-600/20 focus-visible:ring-emerald-500/30 border border-transparent',
        'ghost'   => 'bg-transparent text-emerald-700 hover:bg-emerald-600/10 focus-visible:ring-emerald-500/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-emerald-700 underline-offset-4 hover:underline focus-visible:ring-emerald-500/30 border border-transparent',
    ],
    'warning' => [
        'solid'   => 'bg-amber-500 text-white hover:bg-amber-600 focus-visible:ring-amber-500/40 border border-transparent',
        'outline' => 'border border-amber-500/50 bg-transparent text-amber-600 hover:bg-amber-500/10 focus-visible:ring-amber-500/30',
        'soft'    => 'bg-amber-500/10 text-amber-700 hover:bg-amber-500/20 focus-visible:ring-amber-500/30 border border-transparent',
        'ghost'   => 'bg-transparent text-amber-600 hover:bg-amber-500/10 focus-visible:ring-amber-500/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-amber-600 underline-offset-4 hover:underline focus-visible:ring-amber-500/30 border border-transparent',
    ],
    'danger' => [
        'solid'   => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:ring-rose-500/40 border border-transparent',
        'outline' => 'border border-rose-600/50 bg-transparent text-rose-600 hover:bg-rose-600/10 focus-visible:ring-rose-500/30',
        'soft'    => 'bg-rose-600/10 text-rose-600 hover:bg-rose-600/20 focus-visible:ring-rose-500/30 border border-transparent',
        'ghost'   => 'bg-transparent text-rose-600 hover:bg-rose-600/10 focus-visible:ring-rose-500/30 border border-transparent',
        'link'    => 'bg-transparent p-0 text-rose-600 underline-offset-4 hover:underline focus-visible:ring-rose-500/30 border border-transparent',
    ],
];

$buttonSizes = [
    'xs' => 'px-2.5 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    'xl' => 'px-6 py-3 text-base',
];

/* Icon-only (square) sizes — fixed height/width, no horizontal padding. */
$buttonSquareSizes = [
    'xs' => 'h-7 w-7 !px-0 text-xs',
    'sm' => 'h-8 w-8 !px-0 text-sm',
    'md' => 'h-9 w-9 !px-0 text-sm',
    'lg' => 'h-10 w-10 !px-0 text-base',
    'xl' => 'h-12 w-12 !px-0 text-base',
];

$buttonBase = 'inline-flex select-none items-center justify-center gap-2 rounded-lg font-semibold transition focus:outline-none focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-60 cursor-pointer';
$buttonErrorClasses = ($buttonState === 'error') ? '!border-rose-500 ring-2 ring-rose-500/20' : '';

$buttonClasses = trim(implode(' ', [
    $buttonBase,
    $buttonIconOnly ? $buttonSquareSizes[$buttonSize] : $buttonSizes[$buttonSize],
    $buttonPalette[$buttonColor][$buttonVariant] ?? $buttonPalette['primary'][$buttonVariant] ?? $buttonPalette['primary']['solid'],
    $buttonFullWidth ? 'w-full' : '',
    $buttonErrorClasses,
    $buttonClass,
]));

/* Build extra attributes string. */
$buttonAttr = '';
foreach ($buttonAttributes as $attrName => $attrValue) {
    $buttonAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$buttonSpinner = '<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

$buttonContent = ($buttonLoading ? $buttonSpinner : $buttonIcon) . '<span>' . htmlspecialchars((string) $buttonLabel) . '</span>' . $buttonIconTrailing;
?>
<div class="inline-flex flex-col items-start gap-1 <?= $buttonFullWidth ? 'w-full' : '' ?>">
    <?php
    if (!empty($buttonHref)) {
        printf(
            '<a href="%s" class="%s" id="%s"%s%s%s>%s</a>',
            htmlspecialchars((string) $buttonHref, ENT_QUOTES),
            $buttonClasses,
            htmlspecialchars((string) ($buttonId ?? ''), ENT_QUOTES),
            $buttonDisabled || $buttonLoading ? ' aria-disabled="true"' : '',
            !empty($buttonOnclick) ? ' onclick="' . htmlspecialchars((string) $buttonOnclick, ENT_QUOTES) . '"' : '',
            $buttonAttr,
            $buttonContent,
        );
    } else {
        printf(
            '<button type="%s" class="%s" id="%s" name="%s" value="%s"%s%s%s%s>%s</button>',
            htmlspecialchars((string) $buttonType, ENT_QUOTES),
            $buttonClasses,
            htmlspecialchars((string) ($buttonId ?? ''), ENT_QUOTES),
            htmlspecialchars((string) ($buttonName ?? ''), ENT_QUOTES),
            htmlspecialchars((string) ($buttonValue ?? ''), ENT_QUOTES),
            $buttonDisabled || $buttonLoading ? ' disabled' : '',
            !empty($buttonOnclick) ? ' onclick="' . htmlspecialchars((string) $buttonOnclick, ENT_QUOTES) . '"' : '',
            $buttonAttr,
            $buttonLoading ? ' aria-busy="true"' : '',
            $buttonContent,
        );
    }
    ?>
    <?php if ($buttonError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($buttonError) ?></p>
    <?php endif; ?>
</div>

