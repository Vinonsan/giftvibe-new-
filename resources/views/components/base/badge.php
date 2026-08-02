<?php

declare(strict_types=1);

/**
 * Badge component — small status/label pills in many variants and colours.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/badge.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $badgeLabel      string   Badge text.
 *   $badgeVariant    string   solid | soft | outline | dot              (default: soft)
 *   $badgeColor      string   primary | secondary | accent | success |
 *                             warning | danger | neutral               (default: primary)
 *   $badgeSize       string   sm | md | lg                             (default: md)
 *   $badgeIcon       string   Inline SVG shown before the label.
 *   $badgeRounded    string   full | md | lg                           (default: full)
 *   $badgeRemovable  bool     Show an × button that removes the badge.
 *   $badgeOnRemove   string   Inline JS run when the badge is removed.
 *   $badgeAttributes array    Extra HTML attributes.
 *   $badgeClass      string   Extra CSS classes.
 *
 * -----------------------------------------------------------------------------
 * Examples:
 * -----------------------------------------------------------------------------
 *   $badgeLabel = 'In stock'; $badgeColor = 'success'; require 'badge.php';
 *   $badgeLabel = 'Pending'; $badgeVariant = 'solid'; $badgeColor = 'warning'; require 'badge.php';
 *   $badgeLabel = 'VIP'; $badgeVariant = 'outline'; $badgeColor = 'accent'; require 'badge.php';
 *   $badgeLabel = 'New'; $badgeVariant = 'dot'; $badgeColor = 'primary'; require 'badge.php';
 *   $badgeLabel = 'Filter'; $badgeRemovable = true; $badgeOnRemove = "alert('removed')"; require 'badge.php';
 *
 * @var string|null  $badgeLabel
 * @var string       $badgeVariant
 * @var string       $badgeColor
 * @var string       $badgeSize
 * @var string|null  $badgeIcon
 * @var string       $badgeRounded
 * @var bool         $badgeRemovable
 * @var string|null  $badgeOnRemove
 * @var array        $badgeAttributes
 * @var string|null  $badgeClass
 */

$badgeLabel     = $badgeLabel     ?? '';
$badgeVariant   = $badgeVariant   ?? 'soft';
$badgeColor     = $badgeColor     ?? 'primary';
$badgeSize      = $badgeSize      ?? 'md';
$badgeIcon      = $badgeIcon      ?? '';
$badgeRounded   = $badgeRounded   ?? 'full';
$badgeRemovable = $badgeRemovable ?? false;
$badgeOnRemove  = $badgeOnRemove  ?? '';
$badgeError     = $badgeError     ?? '';
$badgeState     = $badgeState     ?? ($badgeError !== '' ? 'error' : 'default');
$badgeClass     = $badgeClass     ?? '';

/* Variant × color class map. */
$badgePalette = [
    'primary' => [
        'solid'   => 'bg-primary text-white border border-transparent',
        'soft'    => 'bg-primary/10 text-primary border border-transparent',
        'outline' => 'border border-primary/50 text-primary',
        'dot'     => 'bg-primary/10 text-primary border border-transparent',
    ],
    'secondary' => [
        'solid'   => 'bg-secondary text-white border border-transparent',
        'soft'    => 'bg-secondary/10 text-secondary border border-transparent',
        'outline' => 'border border-secondary/40 text-secondary',
        'dot'     => 'bg-secondary/10 text-secondary border border-transparent',
    ],
    'accent' => [
        'solid'   => 'bg-accent text-white border border-transparent',
        'soft'    => 'bg-accent/10 text-accent border border-transparent',
        'outline' => 'border border-accent/50 text-accent',
        'dot'     => 'bg-accent/10 text-accent border border-transparent',
    ],
    'success' => [
        'solid'   => 'bg-emerald-600 text-white border border-transparent',
        'soft'    => 'bg-emerald-600/10 text-emerald-700 border border-transparent',
        'outline' => 'border border-emerald-600/50 text-emerald-700',
        'dot'     => 'bg-emerald-600/10 text-emerald-700 border border-transparent',
    ],
    'warning' => [
        'solid'   => 'bg-amber-500 text-white border border-transparent',
        'soft'    => 'bg-amber-500/10 text-amber-700 border border-transparent',
        'outline' => 'border border-amber-500/50 text-amber-700',
        'dot'     => 'bg-amber-500/10 text-amber-700 border border-transparent',
    ],
    'danger' => [
        'solid'   => 'bg-rose-600 text-white border border-transparent',
        'soft'    => 'bg-rose-600/10 text-rose-600 border border-transparent',
        'outline' => 'border border-rose-600/50 text-rose-600',
        'dot'     => 'bg-rose-600/10 text-rose-600 border border-transparent',
    ],
    'neutral' => [
        'solid'   => 'bg-slate-600 text-white border border-transparent',
        'soft'    => 'bg-slate-200/70 text-slate-700 border border-transparent',
        'outline' => 'border border-slate-300 text-slate-600',
        'dot'     => 'bg-slate-100 text-slate-700 border border-transparent',
    ],
];

/* Dot colour per color (for the "dot" variant). */
$badgeDotColors = [
    'primary'   => 'bg-primary',
    'secondary' => 'bg-secondary',
    'accent'    => 'bg-accent',
    'success'   => 'bg-emerald-500',
    'warning'   => 'bg-amber-500',
    'danger'    => 'bg-rose-500',
    'neutral'   => 'bg-slate-500',
];

$badgeSizes = [
    'sm' => 'px-2.5 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
];

$badgeRadius = [
    'full' => 'rounded-full',
    'md'   => 'rounded-md',
    'lg'   => 'rounded-lg',
];

$badgeBase = 'inline-flex items-center gap-1.5 font-medium whitespace-nowrap transition-colors';
$badgeErrorClasses = ($badgeState === 'error') ? '!border-rose-500 ring-2 ring-rose-500/20' : '';

$badgeClasses = trim(implode(' ', [
    $badgeBase,
    $badgeRadius[$badgeRounded] ?? 'rounded-full',
    $badgeSizes[$badgeSize] ?? $badgeSizes['md'],
    $badgePalette[$badgeColor][$badgeVariant] ?? $badgePalette['primary']['soft'],
    $badgeErrorClasses,
    $badgeClass,
]));

$badgeAttr = '';
foreach ($badgeAttributes ?? [] as $attrName => $attrValue) {
    $badgeAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$dotHtml = $badgeVariant === 'dot'
    ? '<span class="h-1.5 w-1.5 shrink-0 rounded-full ' . ($badgeDotColors[$badgeColor] ?? 'bg-primary') . '"></span>'
    : '';

$removeHtml = '';
if ($badgeRemovable) {
    $removeHtml = '<button type="button" class="group/remove -mr-1 ml-0.5 rounded-full p-0.5 transition hover:bg-black/10 focus:outline-none" aria-label="Remove badge"'
        . ($badgeOnRemove !== '' ? ' onclick="' . htmlspecialchars((string) $badgeOnRemove, ENT_QUOTES) . '; this.closest(\'[data-badge]\').remove();"' : ' onclick="this.closest(\'[data-badge]\').remove();"')
        . '><svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>';
}
?>
<div class="inline-flex flex-col items-start gap-1">
    <span class="<?= $badgeClasses ?>" data-badge<?= $badgeAttr ?>>
        <?= $dotHtml ?><?= $badgeIcon ?><span><?= htmlspecialchars((string) $badgeLabel) ?></span><?= $removeHtml ?>
    </span>
    <?php if ($badgeError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($badgeError) ?></p>
    <?php endif; ?>
</div>
