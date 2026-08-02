<?php

declare(strict_types=1);

/**
 * Modal component — a dialog with a trigger button, overlay, body and footer.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/modal.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $modalId             string   Unique id for the modal + trigger wiring.
 *   $modalTitle          string   Title in the modal header.
 *   $modalDescription    string   Optional subtitle under the title.
 *   $modalBody           string   HTML content of the body.
 *   $modalFooter         string   HTML for the footer action area (buttons etc.).
 *   $modalSize           string   sm | md | lg | xl | full                  (default: md)
 *   $modalStatic         bool     Do NOT close when clicking the backdrop (default: false).
 *   $modalCloseOnEsc     bool     Close on Escape (default: true).
 *   $modalScrollable     bool     Allow the modal panel to scroll internally.
 *   $modalShowCloseButton bool    Show the X close button (default: true).
 *
 *   Trigger (choose one):
 *   $modalTrigger        string   Raw HTML used as the trigger (overrides all below).
 *   $modalTriggerLabel   string   Trigger button text.
 *   $modalTriggerIcon    string   Inline SVG before the label.
 *   $modalTriggerVariant string   solid | outline | soft | ghost | link   (default: solid)
 *   $modalTriggerColor   string   primary | secondary | accent | success |
 *                                 warning | danger                        (default: primary)
 *   $modalTriggerSize    string   xs | sm | md | lg | xl                 (default: md)
 *
 * -----------------------------------------------------------------------------
 * Example:
 * -----------------------------------------------------------------------------
 *   $modalId = 'confirm-delete';
 *   $modalTriggerLabel = 'Delete';
 *   $modalTriggerVariant = 'outline';
 *   $modalTriggerColor = 'danger';
 *   $modalTitle = 'Delete product?';
 *   $modalBody = '<p>This action cannot be undone.</p>';
 *   $modalFooter = '<button data-modal-close class="...">Cancel</button> ...';
 *   require 'modal.php';
 *
 * @var string       $modalId
 * @var string|null  $modalTitle
 * @var string|null  $modalDescription
 * @var string|null  $modalBody
 * @var string|null  $modalFooter
 * @var string       $modalSize
 * @var bool         $modalStatic
 * @var bool         $modalCloseOnEsc
 * @var bool         $modalScrollable
 * @var bool         $modalShowCloseButton
 * @var string|null  $modalTrigger
 * @var string|null  $modalTriggerLabel
 * @var string|null  $modalTriggerIcon
 * @var string       $modalTriggerVariant
 * @var string       $modalTriggerColor
 * @var string       $modalTriggerSize
 */

$modalId              = $modalId              ?? 'modal-' . uniqid();
$modalTitle           = $modalTitle           ?? '';
$modalDescription     = $modalDescription     ?? '';
$modalBody            = $modalBody            ?? '';
$modalFooter          = $modalFooter          ?? '';
$modalSize            = $modalSize            ?? 'md';
$modalStatic          = $modalStatic          ?? false;
$modalCloseOnEsc      = $modalCloseOnEsc      ?? true;
$modalScrollable      = $modalScrollable      ?? false;
$modalShowCloseButton = $modalShowCloseButton ?? true;

$modalTrigger         = $modalTrigger         ?? '';
$modalTriggerLabel    = $modalTriggerLabel    ?? 'Open Modal';
$modalTriggerIcon     = $modalTriggerIcon     ?? '';
$modalTriggerVariant  = $modalTriggerVariant  ?? 'solid';
$modalTriggerColor    = $modalTriggerColor    ?? 'primary';
$modalTriggerSize     = $modalTriggerSize     ?? 'md';

$modalSizes = [
    'sm'   => 'max-w-md',
    'md'   => 'max-w-lg',
    'lg'   => 'max-w-2xl',
    'xl'   => 'max-w-4xl',
    'full' => 'max-w-6xl',
];

/* Render the trigger — reuse the button component when no raw HTML is given. */
if ($modalTrigger === '') {
    $buttonLabel          = $modalTriggerLabel;
    $buttonIcon           = $modalTriggerIcon;
    $buttonVariant        = $modalTriggerVariant;
    $buttonColor          = $modalTriggerColor;
    $buttonSize           = $modalTriggerSize;
    $buttonType           = 'button';
    $buttonAttributes     = ['data-modal-open' => $modalId];
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
    echo $modalTrigger;
}
?>
<div
    id="<?= htmlspecialchars($modalId) ?>"
    data-modal
    data-static="<?= $modalStatic ? '1' : '0' ?>"
    data-close-esc="<?= $modalCloseOnEsc ? '1' : '0' ?>"
    role="dialog"
    aria-modal="true"
    aria-labelledby="<?= htmlspecialchars($modalId) ?>-title"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4"
>
    <div data-modal-overlay class="absolute inset-0 bg-secondary/50 backdrop-blur-sm"></div>

    <div class="relative z-10 w-full <?= $modalSizes[$modalSize] ?? $modalSizes['md'] ?> <?= $modalScrollable ? 'max-h-[90vh] overflow-y-auto' : '' ?> rounded-2xl bg-white shadow-2xl">
        <?php if ($modalTitle !== '' || $modalShowCloseButton): ?>
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
                <div>
                    <?php if ($modalTitle !== ''): ?>
                        <h3 id="<?= htmlspecialchars($modalId) ?>-title" class="text-lg font-bold text-secondary"><?= htmlspecialchars((string) $modalTitle) ?></h3>
                    <?php endif; ?>
                    <?php if ($modalDescription !== ''): ?>
                        <p class="mt-0.5 text-sm text-slate-500"><?= htmlspecialchars((string) $modalDescription) ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($modalShowCloseButton): ?>
                    <button
                        type="button"
                        data-modal-close
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                        aria-label="Close"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($modalBody !== ''): ?>
            <div class="px-6 py-5 text-sm text-slate-600"><?= $modalBody ?></div>
        <?php endif; ?>

        <?php if ($modalFooter !== ''): ?>
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4"><?= $modalFooter ?></div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.modal) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    var openModal = null;
    var lastOpener = null;

    function lockScroll() {
        document.body.style.overflow = 'hidden';
    }
    function unlockScroll() {
        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.style.overflow = '';
        }
    }

    function open(el, opener) {
        lastOpener = opener || null;
        el.classList.remove('hidden');
        el.classList.add('flex');
        lockScroll();
        var closeBtn = el.querySelector('[data-modal-close]');
        if (closeBtn) { closeBtn.focus(); }
    }

    function close(el) {
        el.classList.add('hidden');
        el.classList.remove('flex');
        unlockScroll();
        if (lastOpener) {
            lastOpener.focus();
            lastOpener = null;
        }
    }

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-modal-open]');
        if (opener) {
            var target = document.getElementById(opener.getAttribute('data-modal-open'));
            if (target) { open(target, opener); }
            return;
        }

        var closer = e.target.closest('[data-modal-close]');
        if (closer) {
            close(closer.closest('[data-modal]'));
            return;
        }

        if (e.target.closest('[data-modal-overlay]')) {
            var modal = e.target.closest('[data-modal]');
            if (modal && modal.getAttribute('data-static') !== '1') {
                close(modal);
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        var open = document.querySelector('[data-modal]:not(.hidden)');
        if (open && open.getAttribute('data-close-esc') !== '0') {
            close(open);
        }
    });

    window.GiftVibeUI.modal = true;
})();
</script>
