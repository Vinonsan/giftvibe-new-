<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';

$catalogOptions = ['' => '— Not linked to shop —'];
foreach ($catalogProducts as $product) {
    $catalogOptions[(string) $product['id']] = (string) $product['name'] . (!empty($product['sku']) ? ' (' . $product['sku'] . ')' : '');
}

ob_start();
?>
<form id="inventory-item-form" method="post" action="/admin/inventory" class="space-y-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
    <input type="hidden" name="action" value="save_item">
    <input type="hidden" name="id" id="inventory-form-id" value="0">

    <p class="rounded-xl border border-violet-100 bg-violet-50/60 px-3 py-2 text-xs text-violet-800">
        Extra stock items are not sold on the shop. Use this for supplier-only or packing materials. Shop products sync from Products automatically.
    </p>

    <?php
    foreach ([
        ['name', 'Item name', 'text', 'e.g. Gift wrap rolls', true],
        ['sku', 'SKU / code', 'text', 'Optional', false],
        ['quantity', 'Starting quantity', 'number', '0', false],
        ['cost_price', 'Cost per unit (LKR)', 'number', '0.00', false],
        ['low_stock_threshold', 'Low stock alert at', 'number', '5', false],
    ] as [$name, $label, $type, $placeholder, $required]) {
        $inputName = $name;
        $inputId = 'inventory-' . $name;
        $inputLabel = $label;
        $inputValue = $name === 'low_stock_threshold' ? '5' : ($name === 'quantity' ? '0' : '');
        $inputType = $type;
        $inputPlaceholder = $placeholder;
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $required;
        $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = $type === 'number' ? ['step' => $name === 'cost_price' ? '0.01' : '1', 'min' => '0'] : [];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/input.php';
    }
    ?>

    <div><?php
        $selectName = 'unit';
        $selectId = 'inventory-unit';
        $selectOptions = ['pcs' => 'Pieces', 'box' => 'Boxes', 'roll' => 'Rolls', 'kg' => 'Kg', 'set' => 'Sets'];
        $selectValue = 'pcs';
        $selectPlaceholder = 'Unit';
        $selectLabel = 'Unit';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = [];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Notes <span class="font-normal text-slate-400">(optional)</span></span>
        <textarea name="notes" id="inventory-notes" rows="3" class="<?= $fc ?>"></textarea>
    </label>

    <button type="submit" id="inventory-save-btn" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-secondary transition">Save stock item</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var metaById = {};
    document.querySelectorAll('#inventory-table tbody tr[data-row]').forEach(function (row) {
        var metaCell = row.querySelector('td[data-key="_meta"]');
        if (!metaCell) return;
        try {
            var meta = JSON.parse(metaCell.textContent.trim());
            if (meta && meta.id) metaById[String(meta.id)] = meta;
        } catch (e) {}
    });

    function setSelectValue(name, value) {
        var wrap = document.querySelector('[data-custom-select][data-name="' + name + '"]');
        if (!wrap) return;
        wrap.querySelectorAll('[data-select-option]').forEach(function (opt) {
            opt.setAttribute('data-selected', opt.getAttribute('data-value') === value ? 'true' : 'false');
        });
        var selected = wrap.querySelector('[data-select-option][data-selected="true"]');
        var display = wrap.querySelector('[data-select-display-text]');
        var hidden = wrap.querySelector('[data-select-hidden-container]');
        if (display && selected) {
            display.textContent = selected.getAttribute('data-label') || value;
            display.classList.remove('text-slate-400');
        }
        if (hidden) {
            hidden.innerHTML = value ? '<input type="hidden" name="' + name + '" value="' + value + '">' : '';
        }
    }

    function resetForm() {
        document.getElementById('inventory-form-id').value = '0';
        document.getElementById('inventory-name').value = '';
        document.getElementById('inventory-sku').value = '';
        document.getElementById('inventory-quantity').value = '0';
        document.getElementById('inventory-cost_price').value = '';
        document.getElementById('inventory-low_stock_threshold').value = '5';
        document.getElementById('inventory-notes').value = '';
        setSelectValue('unit', 'pcs');
        document.getElementById('inventory-save-btn').textContent = 'Save stock item';
    }

    document.querySelector('[data-drawer-open="inventory-item-drawer"]')?.addEventListener('click', function (e) {
        if (!e.target.closest('[data-inventory-edit]')) resetForm();
    });

    document.addEventListener('click', function (e) {
        var editBtn = e.target.closest('[data-inventory-edit]');
        if (!editBtn) return;
        var meta = metaById[editBtn.getAttribute('data-inventory-edit')];
        if (!meta) return;
        document.getElementById('inventory-form-id').value = meta.id;
        document.getElementById('inventory-name').value = meta.name || '';
        document.getElementById('inventory-sku').value = meta.sku || '';
        document.getElementById('inventory-quantity').value = meta.quantity ?? '0';
        document.getElementById('inventory-cost_price').value = meta.cost_price ?? '';
        document.getElementById('inventory-low_stock_threshold').value = meta.low_stock_threshold ?? '5';
        document.getElementById('inventory-notes').value = meta.notes || '';
        setSelectValue('unit', meta.unit || 'pcs');
        document.getElementById('inventory-save-btn').textContent = 'Update stock item';
    });
});
</script>
<?php
$drawerBody = (string) ob_get_clean();
$drawerId = 'inventory-item-drawer';
$drawerTitle = 'Add extra stock item';
$drawerDescription = 'Items that are not in your shop catalog — packing, ribbons, supplier stock, etc.';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerSize = 'md';
require BASE_PATH . '/resources/views/components/base/drawer.php';
