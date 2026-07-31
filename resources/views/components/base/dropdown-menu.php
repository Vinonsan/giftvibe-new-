<?php

declare(strict_types=1);

/**
 * Dropdown menu component — a button trigger that toggles a menu.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/dropdown-menu.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $dropdownId          string   Unique id (used for the toggle wiring).
 *   $dropdownItems       array    Menu entries. Each entry can be:
 *                                  'label'                                  (plain item)
 *                                  ['label'=>, 'href'=>, 'icon'=>, 'onclick'=>,
 *                                   'disabled'=>bool, 'active'=>bool, 'danger'=>bool,
 *                                   'keepOpen'=>bool]                        (item)
 *                                  ['type'=>'header', 'label'=>...]
 *                                  ['type'=>'divider']
 *   $dropdownAlign       string   left | right                              (default: left)
 *   $dropdownWidth       string   w-48 | w-56 | w-64                       (default: w-56)
 *   $dropdownTrigger     string   Raw HTML used as the trigger (overrides label/icon).
 *   $dropdownLabel       string   Trigger button text.
 *   $dropdownTriggerIcon string   Inline SVG shown before the label.
 *   $dropdownTriggerVariant string solid | outline | soft | ghost | link    (default: outline)
 *   $dropdownTriggerColor  string primary | secondary | accent | success |
 *                                  warning | danger                         (default: secondary)
 *   $dropdownTriggerSize   string xs | sm | md | lg | xl                   (default: md)
 *   $dropdownMenuClass     string Extra classes on the menu panel.
 *   $dropdownIconTrailing  string Inline SVG shown after the label (default: chevron).
 *
 * -----------------------------------------------------------------------------
 * Example:
 * -----------------------------------------------------------------------------
 *   $dropdownId = 'actions';
 *   $dropdownLabel = 'Actions';
 *   $dropdownItems = [
 *       ['label' => 'Edit', 'href' => '/products/1/edit', 'icon' => '<svg ...>'],
 *       ['type' => 'divider'],
 *       ['label' => 'Delete', 'danger' => true, 'onclick' => "confirm('Delete?')"],
 *   ];
 *   require 'dropdown-menu.php';
 *
 * @var string       $dropdownId
 * @var array        $dropdownItems
 * @var string       $dropdownAlign
 * @var string       $dropdownWidth
 * @var string|null  $dropdownTrigger
 * @var string|null  $dropdownLabel
 * @var string|null  $dropdownTriggerIcon
 * @var string       $dropdownTriggerVariant
 * @var string       $dropdownTriggerColor
 * @var string       $dropdownTriggerSize
 * @var string|null  $dropdownMenuClass
 * @var string|null  $dropdownIconTrailing
 */

$dropdownId           = $dropdownId           ?? 'dropdown-' . uniqid();
$dropdownItems        = $dropdownItems        ?? [];
$dropdownAlign        = $dropdownAlign        ?? 'left';
$dropdownWidth        = $dropdownWidth        ?? 'w-56';
$dropdownTrigger      = $dropdownTrigger      ?? '';
$dropdownLabel        = $dropdownLabel        ?? 'Options';
$dropdownTriggerIcon  = $dropdownTriggerIcon  ?? '';
$dropdownTriggerVariant = $dropdownTriggerVariant ?? 'outline';
$dropdownTriggerColor = $dropdownTriggerColor ?? 'secondary';
$dropdownTriggerSize  = $dropdownTriggerSize  ?? 'md';
$dropdownMenuClass    = $dropdownMenuClass    ?? '';

/* Reuse the same trigger palette as the button component. */
$triggerPalette = [
    'primary' => [
        'solid'   => 'bg-primary text-white hover:bg-primary/90 focus-visible:ring-primary/40',
        'outline' => 'border border-primary/50 bg-transparent text-primary hover:bg-primary/10 focus-visible:ring-primary/30',
        'soft'    => 'bg-primary/10 text-primary hover:bg-primary/20 focus-visible:ring-primary/30',
        'ghost'   => 'bg-transparent text-primary hover:bg-primary/10 focus-visible:ring-primary/30',
        'link'    => 'bg-transparent p-0 text-primary underline-offset-4 hover:underline',
    ],
    'secondary' => [
        'solid'   => 'bg-secondary text-white hover:bg-slate-800 focus-visible:ring-secondary/40',
        'outline' => 'border border-secondary/40 bg-transparent text-secondary hover:bg-secondary/10 focus-visible:ring-secondary/30',
        'soft'    => 'bg-secondary/10 text-secondary hover:bg-secondary/20 focus-visible:ring-secondary/30',
        'ghost'   => 'bg-transparent text-secondary hover:bg-secondary/10 focus-visible:ring-secondary/30',
        'link'    => 'bg-transparent p-0 text-secondary underline-offset-4 hover:underline',
    ],
    'accent' => [
        'solid'   => 'bg-accent text-white hover:bg-pink-600 focus-visible:ring-accent/40',
        'outline' => 'border border-accent/50 bg-transparent text-accent hover:bg-accent/10 focus-visible:ring-accent/30',
        'soft'    => 'bg-accent/10 text-accent hover:bg-accent/20 focus-visible:ring-accent/30',
        'ghost'   => 'bg-transparent text-accent hover:bg-accent/10 focus-visible:ring-accent/30',
        'link'    => 'bg-transparent p-0 text-accent underline-offset-4 hover:underline',
    ],
    'success' => [
        'solid'   => 'bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:ring-emerald-500/40',
        'outline' => 'border border-emerald-600/50 bg-transparent text-emerald-700 hover:bg-emerald-600/10 focus-visible:ring-emerald-500/30',
        'soft'    => 'bg-emerald-600/10 text-emerald-700 hover:bg-emerald-600/20 focus-visible:ring-emerald-500/30',
        'ghost'   => 'bg-transparent text-emerald-700 hover:bg-emerald-600/10 focus-visible:ring-emerald-500/30',
        'link'    => 'bg-transparent p-0 text-emerald-700 underline-offset-4 hover:underline',
    ],
    'warning' => [
        'solid'   => 'bg-amber-500 text-white hover:bg-amber-600 focus-visible:ring-amber-500/40',
        'outline' => 'border border-amber-500/50 bg-transparent text-amber-600 hover:bg-amber-500/10 focus-visible:ring-amber-500/30',
        'soft'    => 'bg-amber-500/10 text-amber-700 hover:bg-amber-500/20 focus-visible:ring-amber-500/30',
        'ghost'   => 'bg-transparent text-amber-600 hover:bg-amber-500/10 focus-visible:ring-amber-500/30',
        'link'    => 'bg-transparent p-0 text-amber-600 underline-offset-4 hover:underline',
    ],
    'danger' => [
        'solid'   => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:ring-rose-500/40',
        'outline' => 'border border-rose-600/50 bg-transparent text-rose-600 hover:bg-rose-600/10 focus-visible:ring-rose-500/30',
        'soft'    => 'bg-rose-600/10 text-rose-600 hover:bg-rose-600/20 focus-visible:ring-rose-500/30',
        'ghost'   => 'bg-transparent text-rose-600 hover:bg-rose-600/10 focus-visible:ring-rose-500/30',
        'link'    => 'bg-transparent p-0 text-rose-600 underline-offset-4 hover:underline',
    ],
];

$triggerSizes = [
    'xs' => 'px-2.5 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    'xl' => 'px-6 py-3 text-base',
];

$triggerBase = 'inline-flex select-none items-center justify-center gap-2 rounded-lg font-semibold transition focus:outline-none focus-visible:ring-2';
$triggerClasses = trim(implode(' ', [
    $triggerBase,
    $triggerSizes[$dropdownTriggerSize],
    $triggerPalette[$dropdownTriggerColor][$dropdownTriggerVariant] ?? $triggerPalette['secondary']['outline'],
]));

$chevron = $dropdownIconTrailing ?? '<svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>';

$triggerHtml = $dropdownTrigger !== ''
    ? $dropdownTrigger
    : '<button type="button" data-dropdown-toggle="' . htmlspecialchars($dropdownId) . '" class="' . $triggerClasses . '" aria-haspopup="true" aria-expanded="false">'
        . $dropdownTriggerIcon
        . '<span>' . htmlspecialchars((string) $dropdownLabel) . '</span>'
        . $chevron
      . '</button>';
?>
<div class="relative inline-block text-left" data-dropdown="<?= htmlspecialchars($dropdownId) ?>">
    <?= $triggerHtml ?>

    <div
        data-dropdown-menu="<?= htmlspecialchars($dropdownId) ?>"
        class="absolute z-20 mt-2 hidden min-w-full origin-top rounded-xl border border-slate-200 bg-white py-1 shadow-lg ring-1 ring-black/5 <?= $dropdownAlign === 'right' ? 'right-0' : 'left-0' ?> <?= $dropdownWidth ?> <?= $dropdownMenuClass ?>"
        role="menu"
        aria-orientation="vertical"
    >
        <?php foreach ($dropdownItems as $item): ?>
            <?php if (is_string($item)): ?>
                <?php $item = ['label' => $item]; ?>
            <?php endif; ?>

            <?php $itemType = $item['type'] ?? 'item'; ?>

            <?php if ($itemType === 'divider'): ?>
                <div class="my-1 border-t border-slate-100"></div>
            <?php elseif ($itemType === 'header'): ?>
                <p class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400"><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></p>
            <?php else: ?>
                <?php
                $isDanger   = (bool) ($item['danger'] ?? false);
                $isActive   = (bool) ($item['active'] ?? false);
                $isDisabled = (bool) ($item['disabled'] ?? false);

                $itemBase = 'flex w-full items-center gap-2.5 px-4 py-2 text-sm transition';
                $itemClass = $isDanger
                    ? $itemBase . ' text-rose-600 hover:bg-rose-50'
                    : ($isActive
                        ? $itemBase . ' bg-primary/5 font-semibold text-primary'
                        : $itemBase . ' text-secondary hover:bg-slate-50');

                $itemAttrs = 'data-dropdown-item' . ($isDisabled ? ' disabled' : '');
                if (!empty($item['keepOpen'])) {
                    $itemAttrs .= ' data-keep-open="1"';
                }
                if (!empty($item['onclick'])) {
                    $itemAttrs .= ' onclick="' . htmlspecialchars((string) $item['onclick'], ENT_QUOTES) . '"';
                }

                if (!empty($item['href'])): ?>
                    <a
                        href="<?= htmlspecialchars((string) $item['href'], ENT_QUOTES) ?>"
                        class="<?= $itemClass . ($isDisabled ? ' pointer-events-none opacity-50' : '') ?>"
                        <?= $itemAttrs ?>
                        role="menuitem"
                    ><?= $item['icon'] ?? '' ?><span><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></span></a>
                <?php else: ?>
                    <button
                        type="button"
                        class="<?= $itemClass . ($isDisabled ? ' cursor-not-allowed opacity-50' : '') ?>"
                        <?= $itemAttrs ?>
                        <?= $isDisabled ? 'disabled' : '' ?>
                        role="menuitem"
                    ><?= $item['icon'] ?? '' ?><span><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></span></button>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.dropdown) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    function closeAll(except) {
        document.querySelectorAll('[data-dropdown-menu]').forEach(function (menu) {
            if (menu !== except) { menu.classList.add('hidden'); }
        });
    }

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('[data-dropdown-toggle]');
        if (toggle) {
            var id = toggle.getAttribute('data-dropdown-toggle');
            var menu = document.querySelector('[data-dropdown-menu="' + id + '"]');
            if (menu) {
                var wasOpen = !menu.classList.contains('hidden');
                closeAll(menu);
                if (!wasOpen) { menu.classList.remove('hidden'); }
                toggle.setAttribute('aria-expanded', String(!wasOpen));
            }
            return;
        }

        var item = e.target.closest('[data-dropdown-menu] [data-dropdown-item]');
        if (item && !item.hasAttribute('data-keep-open')) {
            closeAll();
            return;
        }

        if (!e.target.closest('[data-dropdown]')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeAll(); }
    });

    window.GiftVibeUI.dropdown = true;
})();
</script>
