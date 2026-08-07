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
    <!-- Header -->
    <div class="flex items-center justify-between rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Customer Directory</h1>
            <p class="mt-1 text-sm text-slate-500">View customer profile details and track their lifetime orders report.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 items-center gap-3">
            <div class="w-full max-w-xs">
                <?php
                $renderInput('customer_search', '', '', 'search', false, [
                    'data-datatable-search' => '',
                    'data-datatable-target' => 'customers-table'
                ], 'Search customers...');
                ?>
            </div>
            <button type="button" data-datatable-reset data-datatable-target="customers-table" class="rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary/5 hover:text-primary">Reset</button>
        </div>
    </div>

    <!-- Datatable component -->
    <?php
    $rows = [];
    $avatarPaths = [
        'avatar_1' => '/assets/images/avatars/avatar-1.svg',
        'avatar_2' => '/assets/images/avatars/avatar-2.svg',
        'avatar_3' => '/assets/images/avatars/avatar-3.svg',
        'avatar_4' => '/assets/images/avatars/avatar-4.svg',
        'avatar_5' => '/assets/images/avatars/avatar-5.svg',
        'avatar_6' => '/assets/images/avatars/avatar-6.svg'
    ];

    foreach ($customers as $c) {
        $userId = (int)$c['id'];
        $history = $customerOrders[$userId] ?? [];
        $ordersCount = count($history);
        $avatarKey = $c['avatar'] ?? 'avatar_1';
        $avatarUrl = $avatarPaths[$avatarKey] ?? '/assets/images/avatars/avatar-1.svg';

        $cPayload = htmlspecialchars(json_encode([
            'id' => $userId,
            'name' => trim($c['first_name'] . ' ' . ($c['last_name'] ?? '')),
            'email' => $c['email'],
            'phone' => $c['phone'] ?: 'N/A',
            'phone_2' => $c['phone_2'] ?: 'N/A',
            'avatar_url' => $avatarUrl,
            'address' => trim(($c['address_line_1'] ?? '') . ', ' . ($c['city'] ?? '') . ', ' . ($c['district'] ?? '')),
            'orders' => $history
        ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES);

        $actions = '<div class="flex justify-end gap-2">'
            . '<button type="button" data-drawer-open="customer-drawer" data-customer-payload="' . $cPayload . '" title="View Details & Orders" aria-label="View Details & Orders" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary cursor-pointer">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></button>'
            . '</div>';

        $rows[] = [
            'avatar' => '<img src="' . htmlspecialchars($avatarUrl) . '" alt="avatar" class="h-9 w-9 rounded-full bg-slate-50 border border-slate-100 p-0.5">',
            'name' => '<strong>' . htmlspecialchars($c['first_name'] . ' ' . ($c['last_name'] ?? '')) . '</strong>',
            'contact' => '<span class="text-xs font-semibold">' . htmlspecialchars($c['email']) . '</span><small class="block text-slate-400">' . htmlspecialchars($c['phone'] ?: 'N/A') . '</small>',
            'address' => '<span class="text-xs text-slate-500">' . htmlspecialchars(trim(($c['address_line_1'] ?? '') . ', ' . ($c['city'] ?? ''))) . '</span>',
            'orders_count' => '<strong class="text-secondary">' . $ordersCount . ' orders</strong>',
            '_actions' => $actions
        ];
    }

    $tableId = 'customers-table';
    $tableRows = $rows;
    $tableColumns = [
        ['key' => 'avatar', 'label' => '', 'html' => true, 'sortable' => false, 'width' => 'w-12'],
        ['key' => 'name', 'label' => 'Name', 'html' => true],
        ['key' => 'contact', 'label' => 'Contact', 'html' => true],
        ['key' => 'address', 'label' => 'City', 'html' => true],
        ['key' => 'orders_count', 'label' => 'Order History', 'html' => true, 'type' => 'number'],
        ['_actions' => '', 'key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right', 'width' => 'w-24']
    ];
    $tablePerPage = 10;
    $tableZebra = false;
    $tableEmptyMessage = 'No matching customers found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>
</div>

<!-- Customer Profile & Orders History Drawer -->
<?php ob_start(); ?>
<div class="space-y-6">
    <!-- Modern Profile Header -->
    <div class="flex items-center gap-4 rounded-3xl border border-primary/10 bg-slate-50/30 p-6 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 h-24 w-24 rounded-full bg-primary/5 blur-xl"></div>
        <img id="drawer-c-avatar" src="" alt="avatar" class="h-16 w-16 rounded-full border-2 border-primary bg-white p-0.5 shadow-md">
        <div>
            <h3 id="drawer-c-name" class="text-base font-black text-secondary leading-tight">-</h3>
            <p id="drawer-c-email" class="text-xs text-slate-500 font-semibold mt-1 flex items-center gap-1">
                <svg class="h-3.5 w-3.5 text-[#FF5A79]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                <span>-</span>
            </p>
        </div>
    </div>

    <!-- Contact Info Cards -->
    <div class="grid grid-cols-2 gap-4 text-xs">
        <div class="rounded-2xl border border-primary/10 bg-white p-4.5 shadow-sm space-y-1">
            <span class="text-slate-400 block font-bold text-[9px] uppercase tracking-wider">Primary Phone</span>
            <strong id="drawer-c-phone" class="text-secondary text-sm">-</strong>
        </div>
        <div class="rounded-2xl border border-primary/10 bg-white p-4.5 shadow-sm space-y-1">
            <span class="text-slate-400 block font-bold text-[9px] uppercase tracking-wider">Secondary Phone</span>
            <strong id="drawer-c-phone2" class="text-secondary text-sm">-</strong>
        </div>
        <div class="col-span-2 rounded-2xl border border-primary/10 bg-white p-4.5 shadow-sm space-y-1">
            <span class="text-slate-400 block font-bold text-[9px] uppercase tracking-wider">Registered Address</span>
            <strong id="drawer-c-address" class="text-secondary leading-relaxed">-</strong>
        </div>
    </div>

    <!-- Orders Timeline/Report -->
    <div class="space-y-4">
        <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b pb-2 border-primary/10">Orders Report</h4>
        <div id="drawer-orders-list" class="space-y-3.5 max-h-[40vh] overflow-y-auto pr-1">
            <!-- Dynamic elements loaded via JS -->
        </div>
        <div id="drawer-orders-empty" class="hidden rounded-2xl border border-dashed border-primary/10 p-10 text-center text-xs text-slate-400">
            This customer has not placed any orders yet.
        </div>
    </div>
</div>
<?php
$drawerBody = ob_get_clean();
$drawerFooter = '<button data-drawer-close class="w-full rounded-xl border border-primary/10 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition">Close Directory</button>';
$drawerId = 'customer-drawer';
$drawerSide = 'right';
$drawerSize = 'md';
$drawerTitle = 'Customer Details';
$drawerDescription = 'Registered profile stats and order history report.';
$drawerTrigger = '<span class="hidden"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var drawer = document.getElementById('customer-drawer');
    if (!drawer) return;

    var buttons = document.querySelectorAll('[data-customer-payload]');
    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var payload = JSON.parse(btn.getAttribute('data-customer-payload'));

            // Populate fields
            document.getElementById('drawer-c-avatar').setAttribute('src', payload.avatar_url);
            document.getElementById('drawer-c-name').textContent = payload.name;
            document.getElementById('drawer-c-email').querySelector('span').textContent = payload.email;
            document.getElementById('drawer-c-phone').textContent = payload.phone;
            document.getElementById('drawer-c-phone2').textContent = payload.phone_2;
            document.getElementById('drawer-c-address').textContent = payload.address || 'No address set';

            // Orders list setup
            var list = document.getElementById('drawer-orders-list');
            var empty = document.getElementById('drawer-orders-empty');
            list.innerHTML = '';

            if (payload.orders && payload.orders.length > 0) {
                empty.classList.add('hidden');
                list.classList.remove('hidden');

                payload.orders.forEach(function (order) {
                    var card = document.createElement('div');
                    card.className = 'rounded-2xl border border-primary/10 bg-white p-4.5 shadow-sm space-y-3 hover:border-primary/20 transition-all';

                    var header = document.createElement('div');
                    header.className = 'flex items-center justify-between text-xs';
                    
                    var num = document.createElement('strong');
                    num.className = 'text-secondary font-black';
                    num.textContent = order.order_number;
                    
                    var status = document.createElement('span');
                    status.className = 'rounded-full px-2.5 py-0.5 font-bold uppercase text-[9px] ' + 
                        (order.order_status === 'confirmed' || order.order_status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100/50' : (order.order_status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-100/50' : 'bg-amber-50 text-amber-700 border border-amber-100/50'));
                    status.textContent = order.order_status;
                    
                    header.appendChild(num);
                    header.appendChild(status);

                    var body = document.createElement('div');
                    body.className = 'flex justify-between items-end text-xs text-slate-500 border-t border-slate-50 pt-2.5';
                    
                    var left = document.createElement('div');
                    left.innerHTML = '<span class="block">Recipient: <strong>' + (order.recipient_name || '-') + '</strong></span>' + 
                                     (order.delivery_date ? '<span class="block text-[#FF5A79] font-semibold mt-0.5">Delivered: ' + order.delivery_date + '</span>' : '');
                    
                    var right = document.createElement('div');
                    right.className = 'text-right';
                    right.innerHTML = '<strong class="text-secondary text-sm block font-black">LKR ' + parseFloat(order.grand_total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>' +
                                      '<small class="block text-slate-400 mt-0.5">' + (order.created_at ? order.created_at.substring(0, 10) : '') + '</small>';

                    body.appendChild(left);
                    body.appendChild(right);

                    card.appendChild(header);
                    card.appendChild(body);
                    list.appendChild(card);
                });
            } else {
                empty.classList.remove('hidden');
                list.classList.add('hidden');
            }
        });
    });
});
</script>
