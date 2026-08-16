<?php

declare(strict_types=1);

/**
 * Drawer component — a slide-in panel from the left or right edge.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/drawer.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $drawerId             string   Unique id for the drawer + trigger wiring.
 *   $drawerSide           string   left | right                             (default: right)
 *   $drawerSize           string   sm | md | lg | xl                       (default: md)
 *   $drawerTitle          string   Title in the drawer header.
 *   $drawerDescription    string   Optional subtitle.
 *   $drawerBody           string   HTML content of the body.
 *   $drawerFooter         string   HTML for the footer action area.
 *   $drawerStatic         bool     Do NOT close when clicking the backdrop (default: false).
 *   $drawerCloseOnEsc     bool     Close on Escape (default: true).
 *   $drawerShowCloseButton bool    Show the X close button (default: true).
 *   $drawerOverlay        bool     Show the backdrop (default: true).
 *   $drawerHeaderBottom   string   Optional HTML below the title row, inside the header border.
 *
 *   Trigger (choose one):
 *   $drawerTrigger        string   Raw HTML used as the trigger (overrides all below).
 *   $drawerTriggerLabel   string   Trigger button text.
 *   $drawerTriggerIcon    string   Inline SVG before the label.
 *   $drawerTriggerVariant string   solid | outline | soft | ghost | link   (default: solid)
 *   $drawerTriggerColor   string   primary | secondary | accent | success |
 *                                  warning | danger                        (default: primary)
 *   $drawerTriggerSize    string   xs | sm | md | lg | xl                 (default: md)
 *
 * -----------------------------------------------------------------------------
 * Example:
 * -----------------------------------------------------------------------------
 *   $drawerId = 'cart';
 *   $drawerSide = 'right';
 *   $drawerTriggerLabel = 'View cart';
 *   $drawerTitle = 'Your cart';
 *   $drawerBody = '<p>Cart contents…</p>';
 *   $drawerFooter = '<button data-drawer-close class="...">Continue shopping</button>';
 *   require 'drawer.php';
 *
 * @var string       $drawerId
 * @var string       $drawerSide
 * @var string       $drawerSize
 * @var string|null  $drawerTitle
 * @var string|null  $drawerDescription
 * @var string|null  $drawerBody
 * @var string|null  $drawerFooter
 * @var bool         $drawerStatic
 * @var bool         $drawerCloseOnEsc
 * @var bool         $drawerShowCloseButton
 * @var bool         $drawerOverlay
 * @var string|null  $drawerTrigger
 * @var string|null  $drawerTriggerLabel
 * @var string|null  $drawerTriggerIcon
 * @var string       $drawerTriggerVariant
 * @var string       $drawerTriggerColor
 * @var string       $drawerTriggerSize
 */

$drawerId              = $drawerId              ?? 'drawer-' . uniqid();
$drawerSide            = $drawerSide            ?? 'right';
$drawerSize            = $drawerSize            ?? 'md';
$drawerTitle           = $drawerTitle           ?? '';
$drawerDescription     = $drawerDescription     ?? '';
$drawerBody            = $drawerBody            ?? '';
$drawerFooter          = $drawerFooter          ?? '';
$drawerStatic          = $drawerStatic          ?? false;
$drawerCloseOnEsc      = $drawerCloseOnEsc      ?? true;
$drawerShowCloseButton = $drawerShowCloseButton ?? true;
$drawerOverlay         = $drawerOverlay         ?? true;
$drawerHeaderBottom    = $drawerHeaderBottom    ?? '';
$drawerBodyClass       = $drawerBodyClass       ?? 'flex-1 overflow-y-auto px-6 py-5 text-sm text-slate-600';

$drawerTrigger         = $drawerTrigger         ?? '';
$drawerTriggerLabel    = $drawerTriggerLabel    ?? 'Open Drawer';
$drawerTriggerIcon     = $drawerTriggerIcon     ?? '';
$drawerTriggerVariant  = $drawerTriggerVariant  ?? 'solid';
$drawerTriggerColor    = $drawerTriggerColor    ?? 'primary';
$drawerTriggerSize     = $drawerTriggerSize     ?? 'md';

/* sm=320px, md=384px, lg=448px, xl=512px, full=100% */
$drawerSizes = [
    'sm' => 'w-80',
    'md' => 'w-96',
    'lg' => 'w-[36rem]',
    'xl' => 'w-[40rem]',
    'full' => 'w-full',
];

$isRight = $drawerSide === 'right';
$slideClosed = $isRight ? 'translate-x-full' : '-translate-x-full';

/* Render the trigger — reuse the button component when no raw HTML is given. */
if ($drawerTrigger === '') {
    $buttonLabel          = $drawerTriggerLabel;
    $buttonIcon           = $drawerTriggerIcon;
    $buttonVariant        = $drawerTriggerVariant;
    $buttonColor          = $drawerTriggerColor;
    $buttonSize           = $drawerTriggerSize;
    $buttonType           = 'button';
    $buttonAttributes     = ['data-drawer-open' => $drawerId];
    $buttonDisabled       = false;
    $buttonLoading        = false;
    $buttonIconOnly       = false;
    $buttonFullWidth      = false;
    $buttonHref           = '';
    $buttonOnclick        = '';
    $buttonClass          = '';
    $buttonId             = '';
    $buttonName           = '';
    $buttonValue          = '';
    $buttonIconTrailing   = '';
    require BASE_PATH . '/resources/views/components/base/button.php';
} else {
    echo $drawerTrigger;
}
?>
<div
    id="<?= htmlspecialchars($drawerId) ?>"
    data-drawer
    data-side="<?= htmlspecialchars($drawerSide) ?>"
    data-static="<?= $drawerStatic ? '1' : '0' ?>"
    data-close-esc="<?= $drawerCloseOnEsc ? '1' : '0' ?>"
    aria-hidden="true"
    class="pointer-events-none fixed inset-0 z-50 opacity-0 transition-opacity duration-300"
    style="opacity:0;visibility:hidden;pointer-events:none"
>
    <?php if ($drawerOverlay): ?>
        <div data-drawer-overlay class="absolute inset-0 bg-secondary/50"></div>
    <?php endif; ?>

    <aside
        class="absolute inset-y-0 <?= $isRight ? 'right-0' : 'left-0' ?> flex <?= $drawerSizes[$drawerSize] ?? $drawerSizes['md'] ?> max-w-full flex-col bg-white shadow-2xl transition-drawer <?= $slideClosed ?>"
        style="<?= $isRight ? 'right:0;transform:translateX(100%)' : 'left:0;transform:translateX(-100%)' ?>"
        role="dialog"
        aria-modal="true"
        aria-labelledby="<?= htmlspecialchars($drawerId) ?>-title"
    >
        <?php if ($drawerTitle !== '' || $drawerShowCloseButton || $drawerHeaderBottom !== ''): ?>
            <div class="shrink-0 border-b border-slate-200">
                <?php if ($drawerTitle !== '' || $drawerShowCloseButton): ?>
                    <div class="flex items-start justify-between gap-4 px-6 py-4">
                        <div>
                            <?php if ($drawerTitle !== ''): ?>
                                <h3 id="<?= htmlspecialchars($drawerId) ?>-title" class="text-lg font-bold text-secondary"><?= htmlspecialchars((string) $drawerTitle) ?></h3>
                            <?php endif; ?>
                            <?php if ($drawerDescription !== ''): ?>
                                <p class="mt-0.5 text-sm text-slate-500"><?= htmlspecialchars((string) $drawerDescription) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($drawerShowCloseButton): ?>
                            <button
                                type="button"
                                data-drawer-close
                                class="rounded-xl p-1.5 bg-[#FF5A79] text-white hover:bg-slate-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/40 cursor-pointer"
                                aria-label="Close"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($drawerHeaderBottom !== ''): ?>
                    <?= $drawerHeaderBottom ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="<?= htmlspecialchars($drawerBodyClass) ?>"><?= $drawerBody ?></div>

        <?php if ($drawerFooter !== ''): ?>
            <div class="shrink-0 flex items-center justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4"><?= $drawerFooter ?></div>
        <?php endif; ?>
    </aside>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.drawer) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    var openDrawer = null;
    var lastOpener = null;

    function lockScroll() {
        document.body.style.overflow = 'hidden';
    }
    function unlockScroll() {
        if (!document.querySelector('[data-drawer].opacity-100')) {
            document.body.style.overflow = '';
        }
    }

    function open(el, opener) {
        lastOpener = opener || null;
        var aside = el.querySelector('aside');
        el.classList.remove('pointer-events-none', 'opacity-0');
        el.classList.add('pointer-events-auto', 'opacity-100');
        el.setAttribute('aria-hidden', 'false');
        el.style.opacity = '1';
        el.style.visibility = 'visible';
        el.style.pointerEvents = 'auto';
        if (aside) {
            aside.classList.remove('translate-x-full', '-translate-x-full');
            aside.style.transform = 'translateX(0)';
        }
        lockScroll();
        var closeBtn = el.querySelector('[data-drawer-close]');
        if (closeBtn) { closeBtn.focus(); }
        openDrawer = el;
    }

    function close(el) {
        var aside = el.querySelector('aside');
        var side = el.getAttribute('data-side') || 'right';
        if (aside) {
            aside.classList.add(side === 'right' ? 'translate-x-full' : '-translate-x-full');
            aside.style.transform = side === 'right' ? 'translateX(100%)' : 'translateX(-100%)';
        }
        el.classList.add('pointer-events-none', 'opacity-0');
        el.classList.remove('pointer-events-auto', 'opacity-100');
        el.setAttribute('aria-hidden', 'true');
        el.style.opacity = '0';
        el.style.visibility = 'hidden';
        el.style.pointerEvents = 'none';
        unlockScroll();
        if (lastOpener) {
            lastOpener.focus();
            lastOpener = null;
        }
        if (openDrawer === el) { openDrawer = null; }
    }

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-drawer-open]');
        if (opener) {
            var target = document.getElementById(opener.getAttribute('data-drawer-open'));
            if (target) { open(target, opener); }
            return;
        }

        var closer = e.target.closest('[data-drawer-close]');
        if (closer) {
            close(closer.closest('[data-drawer]'));
            return;
        }

        if (e.target.closest('[data-drawer-overlay]')) {
            var drawer = e.target.closest('[data-drawer]');
            if (drawer && drawer.getAttribute('data-static') !== '1') {
                close(drawer);
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        var open = document.querySelector('[data-drawer].opacity-100');
        if (open && open.getAttribute('data-close-esc') !== '0') {
            close(open);
        }
    });

    window.GiftVibeUI.drawer = true;
})();
</script>
