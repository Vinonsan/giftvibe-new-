<?php
declare(strict_types=1);
/**
 * Global Reusable Delete Confirmation Modal
 *
 * Usage:
 *   $deleteModalId     = 'global-delete-modal';   // unique modal ID
 *   $deleteFormAction  = '/admin/products';         // form post target
 *   $deleteItemLabel   = 'product';                 // e.g. "product", "category"
 *   $deleteCsrfToken   = $csrfToken;
 *   require BASE_PATH . '/resources/views/components/modal/delete-confirm.php';
 *
 * Trigger from anywhere:
 *   <button
 *     data-delete-trigger
 *     data-modal-id="global-delete-modal"
 *     data-id="123"
 *     data-name="Product Name">Delete</button>
 *
 * Then on the page include the companion <script> block or rely on the global listener
 * registered automatically by this component.
 *
 * @var string $deleteModalId
 * @var string $deleteFormAction
 * @var string $deleteItemLabel
 * @var string $deleteCsrfToken
 */

$deleteModalId    = $deleteModalId    ?? 'delete-confirm-modal';
$deleteFormAction = $deleteFormAction ?? '';
$deleteItemLabel  = $deleteItemLabel  ?? 'item';
$deleteCsrfToken  = $deleteCsrfToken  ?? '';
$_deleteFormId    = 'delete-form-' . $deleteModalId;
?>
<!-- Delete Confirmation Modal: <?= htmlspecialchars($deleteModalId) ?> -->
<form id="<?= htmlspecialchars($_deleteFormId) ?>" method="post" action="<?= htmlspecialchars($deleteFormAction) ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($deleteCsrfToken) ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="<?= htmlspecialchars($deleteModalId) ?>-target-id" value="0">
</form>

<?php
$modalId              = $deleteModalId;
$modalTitle           = 'Confirm Delete';
$modalDescription     = 'This action is permanent and cannot be undone.';
$modalSize            = 'sm';
$modalStatic          = false;
$modalCloseOnEsc      = true;
$modalScrollable      = false;
$modalShowCloseButton = true;
$modalTrigger         = '<span class="hidden" aria-hidden="true"></span>';

ob_start();
?>
<div class="flex flex-col items-center gap-4 text-center py-2">
    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
        <svg class="h-7 w-7 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
    </div>
    <div>
        <p class="text-sm font-bold text-secondary">Delete this <?= htmlspecialchars($deleteItemLabel) ?>?</p>
        <p class="mt-1 text-xs text-slate-500">
            You are about to permanently delete
            <strong id="<?= htmlspecialchars($deleteModalId) ?>-name" class="text-secondary font-bold"></strong>.
            This action cannot be reversed.
        </p>
    </div>
</div>
<?php
$modalBody = ob_get_clean();

$modalFooter = '
<button type="button" data-modal-close class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition cursor-pointer">Cancel</button>
<button type="submit" form="' . htmlspecialchars($_deleteFormId) . '" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700 transition cursor-pointer shadow-sm shadow-rose-600/20">Yes, Delete</button>
';

require BASE_PATH . '/resources/views/components/base/modal.php';
?>

<script>
(function () {
    if (window.GiftVibeDeleteModal && window.GiftVibeDeleteModal['<?= htmlspecialchars($deleteModalId) ?>']) return;
    window.GiftVibeDeleteModal = window.GiftVibeDeleteModal || {};
    window.GiftVibeDeleteModal['<?= htmlspecialchars($deleteModalId) ?>'] = true;

    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-delete-trigger][data-modal-id="<?= htmlspecialchars($deleteModalId) ?>"]');
        if (!trigger) return;

        var id   = trigger.getAttribute('data-id')   || '0';
        var name = trigger.getAttribute('data-name') || '';

        document.getElementById('<?= htmlspecialchars($deleteModalId) ?>-target-id').value = id;
        var nameEl = document.getElementById('<?= htmlspecialchars($deleteModalId) ?>-name');
        if (nameEl) nameEl.textContent = name;

        /* Reuse GiftVibeUI modal open API */
        var modal = document.getElementById('<?= htmlspecialchars($deleteModalId) ?>');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            var closeBtn = modal.querySelector('[data-modal-close]');
            if (closeBtn) closeBtn.focus();
        }
    });
})();
</script>
