<?php
declare(strict_types=1);

$modalId = 'order-receipt-modal';
$modalTitle = 'Payment Receipt';
$modalDescription = 'Review the uploaded bank slip or payment proof.';
$modalSize = 'lg';
$modalScrollable = true;
$modalBody = '
    <div class="flex flex-col items-center gap-4">
        <div class="relative flex min-h-[300px] w-full items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 p-2">
            <img id="order-modal-receipt-img" src="' . htmlspecialchars($receiptPath) . '" alt="Payment receipt" class="max-h-[60vh] max-w-full rounded-xl object-contain shadow-sm">
        </div>
        <div class="flex w-full items-center justify-between border-t border-slate-100 pt-4">
            <a id="order-modal-receipt-link" href="' . htmlspecialchars($receiptPath) . '" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-secondary hover:bg-slate-50 transition">
                View full image
            </a>
            <form method="post" action="' . htmlspecialchars(app_url('/admin/orders')) . '" onsubmit="return confirm(\'Delete this receipt?\')">
                <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">
                <input type="hidden" id="order-modal-receipt-order-id" name="order_id" value="' . $orderId . '">
                <input type="hidden" name="redirect_to" value="' . htmlspecialchars($viewBase . '&tab=payment') . '">
                <button type="submit" name="decision" value="delete_receipt" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer">
                    Delete receipt
                </button>
            </form>
        </div>
    </div>
';
$modalFooter = '<button data-modal-close class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">Close</button>';
$modalTrigger = '<span class="hidden"></span>';
require BASE_PATH . '/resources/views/components/base/modal.php';
