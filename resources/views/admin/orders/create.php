<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';

$customerOptions = ['' => '— Select existing customer —'];
foreach ($customers as $customer) {
    $label = trim(((string) ($customer['first_name'] ?? '')) . ' ' . ((string) ($customer['last_name'] ?? '')));
    if ($label === '') {
        $label = (string) ($customer['email'] ?? 'Customer');
    }
    $customerOptions[(string) $customer['id']] = $label . ' · ' . ($customer['phone'] ?? $customer['email']);
}
?>
<div class="space-y-5">

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <form id="admin-create-order-form" method="post" action="<?= app_url('/admin/orders/create') ?>" class="grid gap-5 xl:grid-cols-[1.2fr_0.8fr]" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
        <input type="hidden" name="line_items" id="line-items-json" value="[]">
        <input type="hidden" name="auto_confirm" value="1">
        <input type="hidden" name="customer_mode" id="customer-mode-input" value="existing">

        <!-- LEFT: Products & combos -->
        <div class="space-y-4">
            <?php require __DIR__ . '/components/create/_items.php'; ?>
        </div>

        <!-- RIGHT: Customer + confirm -->
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4 xl:sticky xl:top-24">
                <div>
                    <h2 class="text-sm font-bold text-secondary">Customer & delivery</h2>
                    <p class="mt-1 text-xs text-slate-500">Existing customer pick pannunga, illana new customer add pannunga.</p>
                </div>

                <div class="grid grid-cols-2 gap-2 rounded-xl border border-slate-200 p-1">
                    <button type="button" data-customer-mode="existing" class="customer-mode-btn rounded-lg bg-primary px-3 py-2 text-xs font-bold text-white">Existing customer</button>
                    <button type="button" data-customer-mode="new" class="customer-mode-btn rounded-lg px-3 py-2 text-xs font-bold text-secondary hover:bg-slate-50">New customer</button>
                </div>

                <div id="existing-customer-panel" class="space-y-4">
                <?php
                $selectName = 'customer_id';
                $selectId = 'admin-order-customer';
                $selectOptions = $customerOptions;
                $selectValue = '';
                $selectPlaceholder = 'Select customer';
                $selectLabel = 'Customer';
                $selectHint = $selectError = '';
                $selectSize = 'lg';
                $selectState = 'default';
                $selectMultiple = false;
                $selectRequired = false;
                $selectDisabled = false;
                $selectAttributes = ['data-admin-order-customer' => ''];
                $selectClass = 'w-full';
                require BASE_PATH . '/resources/views/components/base/select.php';
                ?>
                </div>

                <div id="new-customer-panel" class="hidden space-y-3">
                    <?php
                    foreach ([
                        ['new_customer_name', 'Customer name', 'text', 'Full name', true],
                        ['new_customer_phone', 'Phone number', 'tel', '0771234567', true],
                    ] as [$name, $label, $type, $placeholder, $required]) {
                        $inputName = $name;
                        $inputId = 'admin-order-' . $name;
                        $inputLabel = $label;
                        $inputValue = '';
                        $inputType = $type;
                        $inputPlaceholder = $placeholder;
                        $inputHint = $inputError = '';
                        $inputSize = 'lg';
                        $inputState = 'default';
                        $inputRequired = false;
                        $inputReadonly = $inputDisabled = false;
                        $inputAutocomplete = 'off';
                        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
                        $inputAttributes = ['data-new-customer-field' => ''];
                        $inputClass = 'w-full';
                        $inputWrapperClass = 'w-full';
                        require BASE_PATH . '/resources/views/components/base/input.php';
                    }
                    ?>
                    <label class="block space-y-1.5">
                        <span class="text-sm font-medium text-secondary">Address <span class="text-rose-500">*</span></span>
                        <textarea name="new_customer_address" id="admin-order-new_customer_address" rows="3" class="<?= $fc ?>" placeholder="Full delivery address" data-new-customer-field></textarea>
                    </label>
                </div>

                <div id="customer-preview" class="hidden rounded-xl border border-slate-100 bg-slate-50/70 p-4 text-sm space-y-2">
                    <p class="font-bold text-secondary" id="preview-name"></p>
                    <p class="text-slate-600" id="preview-contact"></p>
                    <p class="text-slate-600" id="preview-address"></p>
                </div>

                <div id="no-address-warning" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-800">
                    This customer has no saved address. Enter delivery details below.
                </div>

                <div id="address-fields" class="hidden grid gap-3">
                    <input type="hidden" name="customer_name" id="admin-order-customer_name" value="">
                    <input type="hidden" name="customer_email" id="admin-order-customer_email" value="">
                    <input type="hidden" name="customer_phone" id="admin-order-customer_phone" value="">

                    <div id="saved-address-picker" class="hidden space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Saved addresses</p>
                        <div id="saved-address-list" class="grid gap-2"></div>
                    </div>

                    <div id="existing-address-fields" class="grid gap-3">
                    <?php
                    foreach ([
                        ['delivery_address_line_1', 'Address line 1', true],
                        ['delivery_address_line_2', 'Address line 2', false],
                        ['delivery_city', 'City', true],
                        ['delivery_district', 'District', true],
                    ] as [$name, $label, $required]) {
                        $inputName = $name;
                        $inputId = 'admin-order-' . $name;
                        $inputLabel = $label;
                        $inputValue = '';
                        $inputType = 'text';
                        $inputPlaceholder = '';
                        $inputHint = $inputError = '';
                        $inputSize = 'lg';
                        $inputState = 'default';
                        $inputRequired = false;
                        $inputReadonly = false;
                        $inputDisabled = false;
                        $inputAutocomplete = 'off';
                        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
                        $inputAttributes = ['data-address-field' => ''];
                        $inputClass = 'w-full';
                        $inputWrapperClass = 'w-full';
                        require BASE_PATH . '/resources/views/components/base/input.php';
                    }
                    ?>
                    </div>
                </div>

                <div class="space-y-5">
                    <div><?php
                        $dateInputName='delivery_date';$dateInputId='order-delivery-date';$dateInputValue='';$dateInputMin=date('Y-m-d');$dateInputMax='';$dateInputLabel='Delivery date';$dateInputHint='';$dateInputError='';$dateInputSize='lg';$dateInputState='default';$dateInputRequired=false;$dateInputDisabled=false;$dateInputAttributes=[];$dateInputClass='';
                        require BASE_PATH.'/resources/views/components/base/date-input.php';
                    ?></div>
                    <div class="space-y-2"><span class="block text-sm font-medium text-secondary">Payment method</span><div class="grid gap-3 sm:grid-cols-2">
                        <div data-payment-card class="cursor-pointer rounded-xl border border-slate-200 bg-white p-4 transition has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-2 has-[:checked]:ring-primary/10"><?php
                            $radioName='payment_method';$radioId='payment-method-cod';$radioValue='cod';$radioChecked=true;$radioLabel='Cash on delivery';$radioHint='Enter the initial amount paid';$radioError='';$radioSize='md';$radioColor='primary';$radioState='default';$radioRequired=true;$radioDisabled=false;$radioAttributes=[];$radioClass='';require BASE_PATH.'/resources/views/components/base/radio.php';
                        ?></div>
                        <div data-payment-card class="cursor-pointer rounded-xl border border-slate-200 bg-white p-4 transition has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-2 has-[:checked]:ring-primary/10"><?php
                            $radioName='payment_method';$radioId='payment-method-bank';$radioValue='bank_deposit';$radioChecked=false;$radioLabel='Bank deposit';$radioHint='Full order amount is received';$radioError='';$radioSize='md';$radioColor='primary';$radioState='default';$radioRequired=true;$radioDisabled=false;$radioAttributes=[];$radioClass='';require BASE_PATH.'/resources/views/components/base/radio.php';
                        ?></div>
                    </div></div>
                    <label id="cod-initial-amount-wrap" class="block space-y-1.5"><span class="text-sm font-medium text-secondary">Initial amount paid (LKR)</span>
                        <input type="number" min="0" step="0.01" name="payment_amount" id="payment-amount" value="0" class="<?= $fc ?>">
                    </label>
                </div>

                <div class="rounded-xl border border-primary/15 bg-primary/[0.04] p-4">
                    <div class="flex items-center justify-between text-sm font-semibold text-secondary">
                        <span>Order total</span>
                        <span id="order-summary-total" class="text-lg font-black text-primary">LKR 0.00</span>
                    </div>
                    <p id="payment-method-help" class="mt-2 text-xs text-slate-500">COD initial payment மட்டும் cash income-ல் சேரும்.</p>
                </div>

                <button type="submit" id="admin-create-order-btn"
                    class="w-full rounded-xl bg-primary px-4 py-3.5 text-sm font-bold text-white shadow-sm shadow-primary/20 transition hover:bg-secondary">
                    Confirm order
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var catalogProducts = <?= json_encode(array_map(static fn(array $p): array => [
        'type' => 'product',
        'id' => (int) $p['id'],
        'name' => (string) $p['name'],
        'slug' => (string) $p['slug'],
        'sku' => (string) ($p['sku'] ?? ''),
        'price' => (float) $p['base_price'],
        'cost' => (float) ($p['cost_price'] ?? 0),
        'image' => (string) ($p['image_path'] ?? ''),
    ], $catalogProducts), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var catalogCombos = <?= json_encode(array_map(static fn(array $c): array => [
        'type' => 'combo',
        'id' => (int) $c['id'],
        'name' => (string) $c['name'],
        'slug' => (string) $c['slug'],
        'sku' => 'COMBO-' . (int) $c['id'],
        'price' => (float) $c['price'],
        'cost' => (float) ($c['other_cost'] ?? 0),
        'image' => (string) ($c['image_path'] ?? ''),
    ], $catalogCombos), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var customers = <?= json_encode(array_values(array_map(static fn(array $u): array => [
        'id' => (int) $u['id'],
        'name' => trim(((string) ($u['first_name'] ?? '')) . ' ' . ((string) ($u['last_name'] ?? ''))) ?: (string) ($u['email'] ?? 'Customer'),
        'email' => (string) ($u['email'] ?? ''),
        'phone' => (string) ($u['phone'] ?? ''),
    ], $customers)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var addressesByCustomer = <?= json_encode($addressesByCustomer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    var cart = [];
    var activeCatalog = 'product';
    var selectedCustomerId = '';
    var customerMode = 'existing';
    var lineItemsInput = document.getElementById('line-items-json');
    var summaryTotal = document.getElementById('order-summary-total');
    var customerPreview = document.getElementById('customer-preview');
    var noAddressWarning = document.getElementById('no-address-warning');
    var addressFields = document.getElementById('address-fields');
    var existingAddressFields = document.getElementById('existing-address-fields');
    var existingCustomerPanel = document.getElementById('existing-customer-panel');
    var newCustomerPanel = document.getElementById('new-customer-panel');
    var customerModeInput = document.getElementById('customer-mode-input');
    var savedAddressPicker = document.getElementById('saved-address-picker');
    var savedAddressList = document.getElementById('saved-address-list');
    var customerSelectWrap = document.querySelector('[data-custom-select][data-name="customer_id"]');
    var paymentAmount = document.getElementById('payment-amount');
    var initialAmountWrap = document.getElementById('cod-initial-amount-wrap');

    function syncPaymentMethod() {
        var isBank = document.querySelector('input[name="payment_method"]:checked')?.value === 'bank_deposit';
        initialAmountWrap?.classList.toggle('hidden', isBank);
        var total = cart.reduce(function (sum, item) { return sum + item.price * item.qty; }, 0);
        if (isBank && paymentAmount) paymentAmount.value = Math.max(0, total).toFixed(2);
        var help = document.getElementById('payment-method-help');
        if (help) help.textContent = isBank ? 'Full order amount bank income-ல் சேரும்.' : 'COD initial payment மட்டும் cash income-ல் சேரும்.';
    }

    function setCustomerMode(mode) {
        customerMode = mode === 'new' ? 'new' : 'existing';
        if (customerModeInput) customerModeInput.value = customerMode;

        document.querySelectorAll('.customer-mode-btn').forEach(function (btn) {
            var active = btn.getAttribute('data-customer-mode') === customerMode;
            btn.classList.toggle('bg-primary', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('text-secondary', !active);
            btn.classList.toggle('hover:bg-slate-50', !active);
        });

        if (existingCustomerPanel) existingCustomerPanel.classList.toggle('hidden', customerMode === 'new');
        if (newCustomerPanel) newCustomerPanel.classList.toggle('hidden', customerMode !== 'new');
        if (existingAddressFields) existingAddressFields.classList.toggle('hidden', customerMode === 'new');
        if (addressFields) addressFields.classList.toggle('hidden', customerMode === 'new' && !selectedCustomerId);

        if (customerMode === 'new') {
            customerPreview.classList.add('hidden');
            noAddressWarning.classList.add('hidden');
            if (savedAddressPicker) savedAddressPicker.classList.add('hidden');
            selectedCustomerId = '';
        } else {
            fillCustomer(getSelectedCustomerId());
        }
    }

    function getSelectedCustomerId() {
        var hidden = customerSelectWrap?.querySelector('[data-select-hidden-container] input');
        return hidden ? hidden.value : '';
    }

    document.querySelectorAll('[data-customer-mode]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setCustomerMode(btn.getAttribute('data-customer-mode'));
        });
    });

    function money(n) { return 'LKR ' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function toast(msg, type) { if (window.GiftVibeToast) window.GiftVibeToast.show(msg, type || 'error'); }
    function cartKey(item) { return item.type === 'custom' ? ('custom:' + item.uid) : (item.type + ':' + item.id); }
    function esc(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;'); }

    function field(name) { return document.getElementById('admin-order-' + name); }

    function setItemTab(tab) {
        ['catalog', 'custom'].forEach(function (t) {
            var panel = document.getElementById('item-tab-' + t);
            if (panel) panel.classList.toggle('hidden', t !== tab);
            document.querySelectorAll('[data-item-tab="' + t + '"]').forEach(function (btn) {
                var on = t === tab;
                btn.classList.toggle('bg-white', on);
                btn.classList.toggle('text-primary', on);
                btn.classList.toggle('shadow-sm', on);
                btn.classList.toggle('text-slate-600', !on);
            });
        });
    }

    document.querySelectorAll('[data-item-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () { setItemTab(btn.getAttribute('data-item-tab')); });
    });

    function renderCatalog() {
        var list = activeCatalog === 'product' ? catalogProducts : catalogCombos;
        var q = (document.getElementById('catalog-search')?.value || '').trim().toLowerCase();
        var container = document.getElementById('catalog-list');
        if (!container) return;
        var filtered = list.filter(function (item) {
            if (!q) return true;
            return item.name.toLowerCase().indexOf(q) !== -1 || item.sku.toLowerCase().indexOf(q) !== -1;
        });
        if (!filtered.length) {
            container.innerHTML = '<p class="col-span-full py-8 text-center text-sm text-slate-400">No items found.</p>';
            return;
        }
        container.innerHTML = filtered.slice(0,20).map(function (item) {
            return '<button type="button" data-add-item="' + item.type + ':' + item.id + '" class="group flex min-h-[82px] w-full items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 text-left shadow-sm transition hover:border-primary/40 hover:bg-primary/[0.02] hover:shadow-md">'
                + '<img src="' + esc(item.image) + '" alt="' + esc(item.name) + '" class="h-16 w-16 shrink-0 rounded-lg bg-slate-100 object-cover ring-1 ring-slate-100">'
                + '<span class="min-w-0 flex-1"><span class="line-clamp-2 block text-sm font-bold leading-5 text-secondary transition group-hover:text-primary">' + esc(item.name) + '</span><span class="mt-1.5 block text-sm font-black text-primary">' + money(item.price) + '</span></span></button>';
        }).join('');
    }

    function updateTotals() {
        var totalSell = cart.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
        var totalCost = cart.reduce(function (s, i) { return s + i.cost * i.qty; }, 0);
        summaryTotal.textContent = money(totalSell);
        var sc = document.getElementById('summary-cost');
        var ss = document.getElementById('summary-sell');
        var sp = document.getElementById('summary-profit');
        if (sc) sc.textContent = money(totalCost);
        if (ss) ss.textContent = money(totalSell);
        if (sp) sp.textContent = money(totalSell - totalCost);
        lineItemsInput.value = JSON.stringify(cart.map(function (item) {
            return { type: item.type, id: item.type === 'custom' ? 0 : item.id, qty: item.qty, name: item.name, cost_price: item.cost, sell_price: item.price, sku: item.sku || '' };
        }));
        syncPaymentMethod();
    }

    function renderCart() {
        var selectedWrap = document.getElementById('selected-items');
        var cartBadge = document.getElementById('cart-count-badge');
        var cartSummary = document.getElementById('cart-summary');
        if (!selectedWrap) return;

        if (!cart.length) {
            selectedWrap.innerHTML = '<div class="rounded-xl border border-dashed border-slate-200 py-10 text-center text-sm text-slate-400">Catalog or custom item add pannunga.</div>';
            if (cartBadge) cartBadge.textContent = '0';
            if (cartSummary) cartSummary.classList.add('hidden');
        } else {
            if (cartBadge) cartBadge.textContent = String(cart.length);
            if (cartSummary) cartSummary.classList.remove('hidden');
            selectedWrap.innerHTML = cart.map(function (item, index) {
                var typeLabel = item.type === 'combo' ? 'Combo' : (item.type === 'custom' ? 'Custom' : 'Product');
                var lineSell = item.price * item.qty;
                return '<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">'
                    + '<div class="flex items-start justify-between gap-2">'
                    + '<div class="min-w-0 flex-1"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">' + typeLabel + '</span>'
                    + '<input type="text" value="' + esc(item.name) + '" data-name-index="' + index + '" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-secondary" placeholder="Item name"></div>'
                    + '<button type="button" data-remove-index="' + index + '" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50" aria-label="Remove">&times;</button></div>'
                    + '<div class="grid grid-cols-3 gap-2">'
                    + '<label class="block"><span class="mb-1 block text-[10px] font-bold uppercase text-slate-400">Qty</span>'
                    + '<input type="number" min="1" max="99" value="' + item.qty + '" data-qty-index="' + index + '" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-sm text-center"></label>'
                    + '<label class="block"><span class="mb-1 block text-[10px] font-bold uppercase text-rose-500">Buy</span>'
                    + '<input type="number" min="0" step="0.01" value="' + item.cost + '" data-cost-index="' + index + '" class="w-full rounded-lg border border-rose-100 bg-rose-50/30 px-2 py-2 text-sm"></label>'
                    + '<label class="block"><span class="mb-1 block text-[10px] font-bold uppercase text-emerald-600">Sell</span>'
                    + '<input type="number" min="0.01" step="0.01" value="' + item.price + '" data-price-index="' + index + '" class="w-full rounded-lg border border-emerald-100 bg-emerald-50/30 px-2 py-2 text-sm"></label>'
                    + '</div>'
                    + '<div class="flex justify-between border-t border-slate-100 pt-2 text-xs">'
                    + '<span class="text-slate-500">Line total</span>'
                    + '<span class="font-bold text-secondary">' + money(lineSell) + '</span>'
                    + '</div></div>';
            }).join('');
        }

        updateTotals();
    }

    function pushCartItem(data) {
        var key = cartKey(data);
        var existing = cart.find(function (item) { return cartKey(item) === key && item.type !== 'custom'; });
        if (existing && data.type !== 'custom') {
            existing.qty = Math.min(99, existing.qty + 1);
        } else {
            cart.push(data);
        }
        renderCart();
    }

    function addItem(type, id) {
        var list = type === 'combo' ? catalogCombos : catalogProducts;
        var found = list.find(function (item) { return item.id === id; });
        if (!found) return;
        pushCartItem({
            type: found.type, id: found.id, uid: '', name: found.name, sku: found.sku,
            price: found.price, cost: found.cost || 0, qty: 1, image: found.image
        });
    }

    function addCustomItem() {
        var name = document.getElementById('custom-item-name')?.value.trim();
        var cost = parseFloat(document.getElementById('custom-item-cost')?.value || '0') || 0;
        var price = parseFloat(document.getElementById('custom-item-price')?.value || '0') || 0;
        if (!name) { toast('Custom item name enter pannunga.', 'error'); return; }
        if (price <= 0) { toast('Selling price enter pannunga.', 'error'); return; }
        pushCartItem({
            type: 'custom', id: 0, uid: 'c' + Date.now(), name: name, sku: 'CUSTOM',
            price: price, cost: cost, qty: 1
        });
        document.getElementById('custom-item-name').value = '';
        document.getElementById('custom-item-cost').value = '';
        document.getElementById('custom-item-price').value = '';
        toast('Custom item added.', 'success');
    }

    function applyAddress(addr, readonly) {
        var normalized = normalizeAddressRow(addr);
        var map = {
            delivery_address_line_1: normalized.address_line_1,
            delivery_address_line_2: normalized.address_line_2,
            delivery_city: normalized.city,
            delivery_district: normalized.district
        };
        Object.keys(map).forEach(function (name) {
            var el = field(name);
            if (!el) return;
            el.value = map[name];
            el.readOnly = !!readonly;
            el.disabled = false;
            el.classList.toggle('bg-slate-50', !!readonly);
        });
    }

    function normalizeAddressRow(addr) {
        if (!addr) {
            return { address_line_1: '', address_line_2: '', city: '', district: '' };
        }
        var line1 = String(addr.address_line_1 || addr.address || '').trim();
        return {
            address_line_1: line1,
            address_line_2: String(addr.address_line_2 || '').trim(),
            city: String(addr.city || '').trim() || '—',
            district: String(addr.district || '').trim() || '—'
        };
    }

    function ensureDeliveryFromSaved(customerId) {
        var addresses = addressesByCustomer[customerId] || addressesByCustomer[String(customerId)] || [];
        if (!addresses.length) return false;
        var picked = savedAddressList?.querySelector('input[name="saved_address_choice"]:checked');
        var index = picked ? parseInt(picked.value, 10) : 0;
        var addr = normalizeAddressRow(addresses[index] || addresses[0]);
        applyAddress(addr, addresses.length > 0);
        document.getElementById('preview-address').textContent = [addr.address_line_1, addr.city, addr.district].filter(Boolean).join(', ');
        return addr.address_line_1 !== '';
    }

    function fillCustomer(id) {
        selectedCustomerId = String(id || '');
        var customer = customers.find(function (c) { return String(c.id) === selectedCustomerId; });
        var nameInput = field('customer_name');
        var emailInput = field('customer_email');
        var phoneInput = field('customer_phone');

        if (!customer) {
            customerPreview.classList.add('hidden');
            addressFields.classList.add('hidden');
            noAddressWarning.classList.add('hidden');
            if (savedAddressPicker) savedAddressPicker.classList.add('hidden');
            return;
        }

        if (nameInput) nameInput.value = customer.name;
        if (emailInput) emailInput.value = customer.email;
        if (phoneInput) phoneInput.value = customer.phone;

        document.getElementById('preview-name').textContent = customer.name;
        document.getElementById('preview-contact').textContent = customer.phone + ' · ' + customer.email;
        customerPreview.classList.remove('hidden');
        addressFields.classList.remove('hidden');

        var addresses = addressesByCustomer[customer.id] || addressesByCustomer[String(customer.id)] || [];
        if (!addresses.length) {
            noAddressWarning.classList.remove('hidden');
            if (savedAddressPicker) savedAddressPicker.classList.add('hidden');
            if (existingAddressFields) existingAddressFields.classList.remove('hidden');
            applyAddress({}, false);
            document.getElementById('preview-address').textContent = 'No saved address — enter below.';
            return;
        }

        noAddressWarning.classList.add('hidden');
        if (existingAddressFields) existingAddressFields.classList.add('hidden');
        if (savedAddressPicker && savedAddressList) {
            savedAddressPicker.classList.remove('hidden');
            savedAddressList.innerHTML = addresses.map(function (addr, index) {
                var normalized = normalizeAddressRow(addr);
                var line = [normalized.address_line_1, normalized.city, normalized.district].filter(Boolean).join(', ');
                return '<label class="cursor-pointer rounded-xl border border-slate-200 p-3 text-sm text-secondary has-[:checked]:border-primary has-[:checked]:ring-2 has-[:checked]:ring-primary/10">'
                    + '<span class="flex gap-3"><input type="radio" name="saved_address_choice" value="' + index + '" ' + (index === 0 ? 'checked' : '') + ' class="mt-1 accent-primary">'
                    + '<span><strong class="block text-primary">' + esc(addr.label || 'Address') + '</strong>'
                    + '<span class="mt-1 block text-slate-600">' + esc(line) + '</span></span></span></label>';
            }).join('');
        }

        var first = normalizeAddressRow(addresses[0]);
        applyAddress(first, true);
        document.getElementById('preview-address').textContent = [first.address_line_1, first.city, first.district].filter(Boolean).join(', ');
    }

    document.addEventListener('click', function (e) {
        var addBtn = e.target.closest('[data-add-item]');
        if (addBtn) {
            var parts = addBtn.getAttribute('data-add-item').split(':');
            addItem(parts[0], parseInt(parts[1], 10));
            return;
        }
        var removeBtn = e.target.closest('[data-remove-index]');
        if (removeBtn) {
            cart.splice(parseInt(removeBtn.getAttribute('data-remove-index'), 10), 1);
            renderCart();
        }
    });

    document.getElementById('custom-item-add-btn')?.addEventListener('click', addCustomItem);

    document.addEventListener('input', function (e) {
        var index;
        if (e.target.matches('[data-qty-index]')) {
            index = parseInt(e.target.getAttribute('data-qty-index'), 10);
            cart[index].qty = Math.max(1, Math.min(99, parseInt(e.target.value, 10) || 1));
            renderCart();
        }
    });

    document.addEventListener('change', function (e) {
        var index;
        if (e.target.matches('[data-cost-index]')) {
            index = parseInt(e.target.getAttribute('data-cost-index'), 10);
            cart[index].cost = Math.max(0, parseFloat(e.target.value) || 0);
            renderCart();
        }
        if (e.target.matches('[data-price-index]')) {
            index = parseInt(e.target.getAttribute('data-price-index'), 10);
            cart[index].price = Math.max(0, parseFloat(e.target.value) || 0);
            renderCart();
        }
        if (e.target.matches('[data-name-index]')) {
            index = parseInt(e.target.getAttribute('data-name-index'), 10);
            cart[index].name = e.target.value.trim();
            updateTotals();
        }
    });

    document.querySelectorAll('[data-catalog-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activeCatalog = btn.getAttribute('data-catalog-tab');
            document.querySelectorAll('.catalog-type-btn').forEach(function (b) {
                var on = b === btn;
                b.classList.toggle('bg-primary', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('border-primary', on);
                b.classList.toggle('text-secondary', !on);
                b.classList.toggle('border-slate-200', !on);
            });
            renderCatalog();
        });
    });

    document.getElementById('catalog-search')?.addEventListener('input', renderCatalog);
    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio){radio.addEventListener('change',syncPaymentMethod);});
    document.querySelectorAll('[data-payment-card]').forEach(function(card){card.addEventListener('click',function(e){if(e.target.closest('label,input'))return;var radio=card.querySelector('input[type="radio"]');if(radio&&!radio.checked){radio.checked=true;radio.dispatchEvent(new Event('change',{bubbles:true}));}});});

    customerSelectWrap?.addEventListener('select:change', function (e) {
        if (customerMode !== 'existing') return;
        fillCustomer((e.detail && e.detail.values && e.detail.values[0]) || '');
    });

    document.addEventListener('select:change', function (e) {
        if (customerMode !== 'existing') return;
        if (!e.target.closest('[data-custom-select][data-name="customer_id"]')) return;
        fillCustomer((e.detail && e.detail.values && e.detail.values[0]) || '');
    });

    savedAddressList?.addEventListener('change', function (e) {
        if (!e.target.matches('input[name="saved_address_choice"]')) return;
        var addresses = addressesByCustomer[selectedCustomerId] || addressesByCustomer[String(selectedCustomerId)] || [];
        var addr = normalizeAddressRow(addresses[parseInt(e.target.value, 10)]);
        applyAddress(addr, true);
        document.getElementById('preview-address').textContent = [addr.address_line_1, addr.city, addr.district].filter(Boolean).join(', ');
    });

    document.getElementById('admin-create-order-form')?.addEventListener('submit', function (e) {
        renderCart();
        updateTotals();

        if (!cart.length) {
            e.preventDefault();
            toast('At least one item add pannunga.', 'error');
            return;
        }

        if (customerMode === 'new') {
            var newName = document.getElementById('admin-order-new_customer_name')?.value.trim();
            var newPhone = document.getElementById('admin-order-new_customer_phone')?.value.trim();
            var newAddress = document.getElementById('admin-order-new_customer_address')?.value.trim();
            if (!newName || !newPhone || !newAddress) {
                e.preventDefault();
                toast('New customer name, phone and address enter pannunga.', 'error');
            }
            return;
        }

        var customerId = getSelectedCustomerId();
        if (!customerId) {
            e.preventDefault();
            toast('Customer select pannunga.', 'error');
            return;
        }
        selectedCustomerId = customerId;
        fillCustomer(customerId);
        ensureDeliveryFromSaved(customerId);

        var line1 = field('delivery_address_line_1')?.value.trim() || '';
        var city = field('delivery_city')?.value.trim() || '';
        var district = field('delivery_district')?.value.trim() || '';
        if (!line1 || !city || !district) {
            e.preventDefault();
            toast('Delivery address complete pannunga. Customer saved address illa na manually enter pannunga.', 'error');
            if (existingAddressFields) existingAddressFields.classList.remove('hidden');
            addressFields?.classList.remove('hidden');
        }
    });

    setCustomerMode(customers.length ? 'existing' : 'new');
    setItemTab('catalog');
    syncPaymentMethod();
    renderCatalog();
    renderCart();
});
</script>
