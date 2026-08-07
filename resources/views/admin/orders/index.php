<?php
declare(strict_types=1);

$renderInput = static function(string $name, string $label, string $value = '', string $type = 'text', bool $required = false, array $attributes = [], string $placeholder = ''): void {
    $inputType = $type;
    $inputName = $name;
    $inputId = $name;
    $inputValue = $value;
    $inputPlaceholder = $placeholder;
    $inputLabel = $label;
    $inputHint = '';
    $inputError = '';
    $inputSize = 'md';
    $inputState = 'default';
    $inputRequired = $required;
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = $attributes;
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};
?>
<div class="space-y-5">
    <!-- Header Card -->
    <div class="flex items-center justify-between rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Order Verification</h1>
            <p class="mt-1 text-sm text-slate-500">Manage orders, check bank receipts in the detail drawer, and update dispatch stages.</p>
        </div>
    </div>

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="w-full max-w-xs">
                <?php
                $renderInput('order_search', '', '', 'search', false, [
                    'data-datatable-search' => '',
                    'data-datatable-target' => 'orders-table'
                ], 'Search orders...');
                ?>
            </div>
            <div class="w-full max-w-[180px]">
                <?php
                $selectName = 'order_status_filter';
                $selectId = 'order-status-filter';
                $selectOptions = [
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'ready' => 'Ready',
                    'out_for_delivery' => 'Out for Delivery',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled'
                ];
                $selectValue = 'pending'; // Default to pending
                $selectPlaceholder = 'All Statuses';
                $selectLabel = '';
                $selectHint = '';
                $selectError = '';
                $selectSize = 'md';
                $selectState = 'default';
                $selectMultiple = false;
                $selectRequired = false;
                $selectDisabled = false;
                $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'orders-table', 'data-filter-key' => 'status'];
                $selectClass = '';
                require BASE_PATH . '/resources/views/components/base/select.php';
                ?>
            </div>
            <div class="w-full max-w-[180px]">
                <?php
                $selectName = 'payment_status_filter';
                $selectId = 'payment-status-filter';
                $selectOptions = [
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed'
                ];
                $selectValue = '';
                $selectPlaceholder = 'All Payments';
                $selectLabel = '';
                $selectHint = '';
                $selectError = '';
                $selectSize = 'md';
                $selectState = 'default';
                $selectMultiple = false;
                $selectRequired = false;
                $selectDisabled = false;
                $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'orders-table', 'data-filter-key' => 'payment_verification'];
                $selectClass = '';
                require BASE_PATH . '/resources/views/components/base/select.php';
                ?>
            </div>
            <button type="button" data-datatable-reset data-datatable-target="orders-table" class="rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary/5 hover:text-primary">Reset</button>
        </div>
    </div>

    <!-- Datatable Component -->
    <?php
    $rows = [];
    foreach ($orders as $order) {
        $paymentMeta = json_decode((string) ($order['raw_response_json'] ?? ''), true) ?: [];
        
        $orderDataPayload = htmlspecialchars(json_encode([
            'id' => $order['id'],
            'order_number' => $order['order_number'],
            'customer_name' => $order['customer_name'],
            'customer_phone' => $order['customer_phone'],
            'customer_email' => $order['customer_email'],
            'recipient_name' => $order['recipient_name'],
            'recipient_phone' => $order['recipient_phone'],
            'address' => $order['delivery_address_line_1'] . ', ' . $order['delivery_city'] . ', ' . $order['delivery_district'],
            'payment_option' => ucfirst($paymentMeta['payment_option'] ?? 'N/A'),
            'balance_due' => number_format((float) ($paymentMeta['balance_due'] ?? 0), 2),
            'receipt_path' => $order['receipt_path'] ?: '',
            'order_status' => $order['order_status'],
            'payment_status' => $order['payment_status'],
            'delivery_date' => $order['delivery_date'] ?: '',
            'admin_notes' => $order['admin_notes'] ?: ''
        ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES);

        $actions = '<div class="flex justify-end gap-2">'
            . '<button type="button" data-drawer-open="order-drawer" data-order-payload="' . $orderDataPayload . '" title="View Details" aria-label="View Details" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary cursor-pointer">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></button>';

        if (in_array($order['order_status'], ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'], true)) {
            $actions .= '<a href="/admin/orders/receipt?id=' . $order['id'] . '" target="_blank" title="Print Receipt" aria-label="Print Receipt" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary">'
                . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75m0 0a1.5 1.5 0 0 1-3 0h6a1.5 1.5 0 0 1-3 0Z"/></svg></a>';
        }

        $actions .= '</div>';

        $pStatus = $order['payment_status'];
        $paymentBadge = '<span class="rounded-full px-2.5 py-0.5 text-xs font-bold ' . ($pStatus === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($pStatus === 'failed' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')) . '">' . ucfirst($pStatus) . '</span>';
        
        $oStatus = $order['order_status'];
        $orderBadge = '<span class="rounded-full px-2.5 py-0.5 text-xs font-bold ' . ($oStatus === 'delivered' || $oStatus === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : ($oStatus === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')) . '">' . ucfirst(str_replace('_', ' ', $oStatus)) . '</span>';

        $rows[] = [
            'order_number' => '<strong class="text-secondary">' . htmlspecialchars($order['order_number']) . '</strong>',
            'customer' => '<strong>' . htmlspecialchars($order['customer_name']) . '</strong><small class="block text-slate-400">' . htmlspecialchars($order['customer_phone']) . '</small>',
            'amount' => '<strong>LKR ' . number_format((float) $order['grand_total'], 2) . '</strong>',
            'payment_verification' => $paymentBadge,
            'status' => $orderBadge,
            '_actions' => $actions
        ];
    }

    $tableId = 'orders-table';
    $tableRows = $rows;
    $tableColumns = [
        ['key' => 'order_number', 'label' => 'Order #', 'html' => true],
        ['key' => 'customer', 'label' => 'Customer', 'html' => true],
        ['key' => 'amount', 'label' => 'Total Amount', 'html' => true, 'type' => 'number'],
        ['key' => 'payment_verification', 'label' => 'Payment Status', 'html' => true],
        ['key' => 'status', 'label' => 'Order Status', 'html' => true],
        ['_actions' => '', 'key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right', 'width' => 'w-24']
    ];
    $tablePerPage = 10;
    $tableZebra = false;
    $tableEmptyMessage = 'No orders matching your selection found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>
</div>

<!-- Details & Management Drawer (Modern Large size lg) -->
<?php ob_start(); ?>
<div class="space-y-6">
    <!-- Grid for Details Summary -->
    <div class="grid gap-6 sm:grid-cols-2">
        <!-- Customer & Delivery Information -->
        <div class="rounded-2xl border border-primary/10 bg-slate-50/40 p-5 space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b pb-2 border-primary/10">Delivery Details</h4>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="block text-slate-400">Recipient Name</span>
                    <strong id="drawer-recipient-name" class="text-secondary">-</strong>
                </div>
                <div>
                    <span class="block text-slate-400">Recipient Phone</span>
                    <strong id="drawer-recipient-phone" class="text-secondary">-</strong>
                </div>
                <div>
                    <span class="block text-slate-400">Delivery Address</span>
                    <strong id="drawer-delivery-address" class="text-secondary leading-relaxed">-</strong>
                </div>
            </div>
        </div>

        <!-- Payment & Metadata -->
        <div class="rounded-2xl border border-primary/10 bg-slate-50/40 p-5 space-y-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b pb-2 border-primary/10">Payment Details</h4>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="block text-slate-400">Payment Option</span>
                    <strong id="drawer-payment-option" class="text-secondary">-</strong>
                </div>
                <div>
                    <span class="block text-slate-400">Balance Due</span>
                    <strong id="drawer-balance-due" class="text-secondary">-</strong>
                </div>
                <div>
                    <span class="block text-slate-400 mb-1.5">Verification Receipt File</span>
                    <span id="drawer-receipt-wrapper">
                        <button type="button" id="drawer-receipt-btn" data-modal-open="receipt-modal" data-receipt-src="" data-order-id="" class="inline-flex items-center gap-1.5 rounded-lg border border-primary/15 bg-white px-3 py-2 text-xs font-bold text-primary hover:bg-primary/5 cursor-pointer transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            <span>Open Receipt Image</span>
                        </button>
                    </span>
                    <span id="drawer-receipt-missing" class="font-bold text-rose-600 hidden">Missing / Not Uploaded</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progressive Order Management Stages Form -->
    <div class="rounded-2xl border border-primary/10 bg-white p-5 space-y-4 shadow-sm">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b pb-2 border-primary/10">Order Status Manager</h4>
        
        <!-- INITIAL ACCEPTANCE PANEL (Only for Pending orders) -->
        <div id="drawer-initial-accept-panel" class="hidden">
            <form id="drawer-accept-form" method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" class="drawer-order-id-input" name="order_id" value="">
                
                <?php $renderInput('delivery_days', 'Delivery Days to Fulfill', '3', 'number', true, ['min' => '1', 'max' => '30']); ?>
                
                <label class="block space-y-1.5">
                    <span class="text-xs font-semibold text-secondary">Acceptance notes</span>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-xl border border-primary/10 bg-white p-3 text-xs text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Optional notes sent to customer..."></textarea>
                </label>

                <div class="flex gap-2">
                    <button type="submit" name="decision" value="confirm" class="flex-1 rounded-xl bg-primary py-3 text-xs font-bold text-white hover:bg-secondary cursor-pointer transition">Accept Order & Add to Income</button>
                    <button type="submit" name="decision" value="cancel" class="rounded-xl border border-accent px-4 py-3 text-xs font-bold text-accent hover:bg-accent hover:text-white cursor-pointer transition">Cancel Order</button>
                </div>
            </form>
        </div>

        <!-- PROGRESSIVE WORKFLOW CONTROLS (For Already Confirmed/Processing/Dispatched orders) -->
        <div id="drawer-progressive-panel" class="hidden">
            <form id="drawer-update-form" method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" class="drawer-order-id-input" name="order_id" value="">
                <input type="hidden" name="decision" value="update_status">

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Order Workflow State -->
                    <div>
                        <?php
                        $selectName = 'order_status';
                        $selectId = 'drawer-order-status-select';
                        $selectOptions = [
                            'confirmed' => 'Confirmed (Accepted)',
                            'processing' => 'Processing (Preparing)',
                            'ready' => 'On Ready (Ready for delivery)',
                            'out_for_delivery' => 'Picked for Courier service',
                            'delivered' => 'Delivered (Completed)',
                            'cancelled' => 'Cancelled'
                        ];
                        $selectValue = '';
                        $selectPlaceholder = 'Choose Order Status';
                        $selectLabel = 'Order Workflow Stage';
                        $selectRequired = true;
                        require BASE_PATH . '/resources/views/components/base/select.php';
                        ?>
                    </div>

                    <!-- Payment State -->
                    <div>
                        <?php
                        $selectName = 'payment_status';
                        $selectId = 'drawer-payment-status-select';
                        $selectOptions = [
                            'pending' => 'Pending',
                            'paid' => 'Paid (Fully Paid)',
                            'failed' => 'Failed'
                        ];
                        $selectValue = '';
                        $selectPlaceholder = 'Choose Payment Status';
                        $selectLabel = 'Payment Status';
                        $selectRequired = true;
                        require BASE_PATH . '/resources/views/components/base/select.php';
                        ?>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a id="drawer-receipt-print-btn" href="#" target="_blank" class="inline-flex w-full sm:w-auto items-center justify-center gap-1.5 rounded-xl border border-primary/15 bg-slate-50 px-5 py-3 text-xs font-bold text-secondary hover:bg-slate-100 transition">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75m0 0a1.5 1.5 0 0 1-3 0h6a1.5 1.5 0 0 1-3 0Z"/></svg>
                        <span>Print Receipt Document</span>
                    </a>
                </div>

                <label class="block space-y-1.5">
                    <span class="text-xs font-semibold text-secondary">Admin note</span>
                    <textarea id="drawer-admin-notes" name="admin_notes" rows="2" class="w-full rounded-xl border border-primary/10 bg-white p-3 text-xs text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Update verification notes or remarks..."></textarea>
                </label>

                <button type="submit" class="w-full rounded-xl bg-primary py-3 text-xs font-bold text-white hover:bg-secondary cursor-pointer transition">Save Updates & Notify Customer</button>
            </form>
        </div>
    </div>
</div>
<?php
$drawerBody = ob_get_clean();
$drawerFooter = '<button data-drawer-close class="w-full rounded-xl border border-primary/10 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition">Close Details</button>';
$drawerId = 'order-drawer';
$drawerSide = 'right';
$drawerSize = 'lg'; // Modern Large Drawer
$drawerTitle = 'Order details';
$drawerDescription = 'Verify receipt metadata and manage active workflow stages.';
$drawerTrigger = '<span class="hidden"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>

<!-- Document Receipt Modal Component -->
<?php
$modalId = 'receipt-modal';
$modalTitle = 'Payment Receipt Document';
$modalDescription = 'Review uploaded image slip details below.';
$modalSize = 'lg';
$modalScrollable = true;
$modalBody = '
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-full rounded-2xl overflow-hidden border border-primary/10 bg-slate-50 p-2 flex justify-center items-center min-h-[300px]">
            <img id="modal-receipt-img" src="" alt="Payment Receipt" class="max-w-full rounded-xl object-contain max-h-[60vh] shadow-sm">
        </div>
        
        <div class="flex w-full items-center justify-between border-t border-primary/10 pt-4 mt-2">
            <a id="modal-receipt-view-link" href="#" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                <span>View Full Image</span>
            </a>
            
            <form id="modal-receipt-delete-form" method="post" onsubmit="return confirm(\'Delete this receipt slip?\')">
                <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">
                <input type="hidden" id="modal-receipt-order-id" name="order_id" value="">
                <button type="submit" name="decision" value="delete_receipt" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    <span>Delete Receipt</span>
                </button>
            </form>
        </div>
    </div>
';
$modalFooter = '<button data-modal-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 font-semibold hover:bg-slate-50 transition cursor-pointer text-xs">Close Dialog</button>';
$modalTrigger = '<span class="hidden"></span>';
require BASE_PATH . '/resources/views/components/base/modal.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var drawer = document.getElementById('order-drawer');
    if (!drawer) return;

    // Helper to programmatically set and sync custom select values
    function setCustomSelectValue(selectId, val) {
        var wrapper = document.getElementById(selectId).closest('[data-custom-select]');
        if (!wrapper) return;
        var options = wrapper.querySelectorAll('[data-select-option]');
        var selectedOpt = null;
        options.forEach(function (opt) {
            if (opt.getAttribute('data-value') === val) {
                opt.setAttribute('data-selected', 'true');
                selectedOpt = opt;
            } else {
                opt.setAttribute('data-selected', 'false');
            }
        });
        
        if (selectedOpt) {
            var display = wrapper.querySelector('[data-select-display-text]');
            var hiddenContainer = wrapper.querySelector('[data-select-hidden-container]');
            var name = wrapper.getAttribute('data-name');
            
            display.textContent = selectedOpt.getAttribute('data-label');
            display.classList.remove('text-slate-400');
            
            hiddenContainer.innerHTML = '';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = val;
            hiddenContainer.appendChild(input);
            
            // Sync checkbox classes and state variables
            options.forEach(function (option) {
                var active = option.getAttribute('data-selected') === 'true';
                option.setAttribute('aria-selected', active ? 'true' : 'false');
                option.classList.toggle('bg-primary/10', active);
                option.classList.toggle('text-primary', active);
                option.classList.toggle('font-semibold', active);
            });
        }
    }

    var buttons = document.querySelectorAll('[data-order-payload]');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var payload = JSON.parse(btn.getAttribute('data-order-payload'));

            // Populate static information labels
            document.getElementById('drawer-recipient-name').textContent = payload.recipient_name;
            document.getElementById('drawer-recipient-phone').textContent = payload.recipient_phone;
            document.getElementById('drawer-delivery-address').textContent = payload.address;
            document.getElementById('drawer-payment-option').textContent = payload.payment_option;
            document.getElementById('drawer-balance-due').textContent = 'LKR ' + payload.balance_due;

            // Load order ID to forms
            var idInputs = document.querySelectorAll('.drawer-order-id-input');
            idInputs.forEach(function (inp) {
                inp.value = payload.id;
            });

            // Receipt link/btn setup inside drawer
            var receiptBtn = document.getElementById('drawer-receipt-btn');
            var receiptMissing = document.getElementById('drawer-receipt-missing');
            if (payload.receipt_path) {
                receiptBtn.setAttribute('data-receipt-src', payload.receipt_path);
                receiptBtn.setAttribute('data-order-id', payload.id);
                receiptBtn.parentElement.classList.remove('hidden');
                receiptMissing.classList.add('hidden');
            } else {
                receiptBtn.parentElement.classList.add('hidden');
                receiptMissing.classList.remove('hidden');
            }

            // Forms toggling & values
            var acceptPanel = document.getElementById('drawer-initial-accept-panel');
            var progressivePanel = document.getElementById('drawer-progressive-panel');
            
            if (payload.order_status === 'pending') {
                acceptPanel.classList.remove('hidden');
                progressivePanel.classList.add('hidden');
            } else {
                acceptPanel.classList.add('hidden');
                progressivePanel.classList.remove('hidden');

                // Populate custom selections
                setCustomSelectValue('drawer-order-status-select', payload.order_status);
                setCustomSelectValue('drawer-payment-status-select', payload.payment_status);
                document.getElementById('drawer-admin-notes').value = payload.admin_notes;

                // Sync printing button link
                var printBtn = document.getElementById('drawer-receipt-print-btn');
                if (['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'].indexOf(payload.order_status) !== -1) {
                    printBtn.setAttribute('href', '/admin/orders/receipt?id=' + payload.id);
                    printBtn.classList.remove('hidden');
                } else {
                    printBtn.classList.add('hidden');
                }
            }
        });
    });

    // Capture click events on modal trigger links to set dynamic attributes
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-modal-open="receipt-modal"]');
        if (trigger) {
            var src = trigger.getAttribute('data-receipt-src');
            var orderId = trigger.getAttribute('data-order-id');
            document.getElementById('modal-receipt-img').setAttribute('src', src);
            document.getElementById('modal-receipt-view-link').setAttribute('href', src);
            document.getElementById('modal-receipt-order-id').value = orderId;
        }
    });
});
</script>
