<?php
declare(strict_types=1);

$catalogProducts = $catalogProducts ?? [];
$inventoryItems = $inventoryItems ?? [];

$productOptions = ['' => '— New item (use title above) —'];
foreach ($catalogProducts as $product) {
    $productOptions[(string) $product['id']] = (string) $product['name'] . (!empty($product['sku']) ? ' (' . $product['sku'] . ')' : '');
}

$stockOptions = ['' => '— Or pick extra stock item —'];
foreach ($inventoryItems as $item) {
    if (!empty($item['product_id'])) {
        continue;
    }
    $stockOptions[(string) $item['id']] = (string) $item['name'] . ' (' . number_format((int) $item['quantity']) . ' in stock)';
}

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';

ob_start();
?>
<form id="expense-form" method="post" action="/admin/expenses" class="space-y-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" id="expense-form-id" value="0">

    <div><?php
        $selectName = 'category';
        $selectId = 'expense-category';
        $selectOptions = $categories;
        $selectValue = 'product_purchase';
        $selectPlaceholder = 'Category';
        $selectLabel = 'Category';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = true;
        $selectDisabled = false;
        $selectAttributes = ['data-expense-category' => ''];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <?php
    foreach ([
        ['title', 'Title', 'text', 'e.g. Rose bouquet stock', true],
        ['amount', 'Amount (LKR)', 'number', '0.00', true],
        ['expense_date', 'Date', 'date', '', true],
    ] as [$name, $label, $type, $placeholder, $required]) {
        $inputName = $name;
        $inputId = 'expense-' . $name;
        $inputLabel = $label;
        $inputValue = $name === 'expense_date' ? date('Y-m-d') : '';
        $inputType = $type;
        $inputPlaceholder = $placeholder;
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $required;
        $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = $type === 'number' ? ['step' => '0.01', 'min' => '0.01'] : [];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/input.php';
    }
    ?>

    <div id="expense-inventory-panel" class="space-y-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="add_to_inventory" value="1" id="expense-add-inventory" checked class="mt-1 h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary/30">
            <span>
                <span class="block text-sm font-semibold text-secondary">Add to inventory</span>
                <span class="block text-xs text-slate-500">When buying product stock, units go to Inventory automatically.</span>
            </span>
        </label>

        <?php
        $inputName = 'stock_quantity';
        $inputId = 'expense-stock-quantity';
        $inputLabel = 'Quantity purchased';
        $inputValue = '1';
        $inputType = 'number';
        $inputPlaceholder = '1';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = false;
        $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['step' => '1', 'min' => '1'];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/input.php';
        ?>

        <div><?php
            $selectName = 'product_id';
            $selectId = 'expense-product-id';
            $selectOptions = $productOptions;
            $selectValue = '';
            $selectPlaceholder = 'Link to shop product';
            $selectLabel = 'Shop product (optional)';
            $selectHint = 'Pick a catalog product to increase its stock.';
            $selectError = '';
            $selectSize = 'lg';
            $selectState = 'default';
            $selectMultiple = false;
            $selectRequired = false;
            $selectDisabled = false;
            $selectAttributes = [];
            $selectClass = 'w-full';
            require BASE_PATH . '/resources/views/components/base/select.php';
        ?></div>

        <div><?php
            $selectName = 'inventory_item_id';
            $selectId = 'expense-inventory-item-id';
            $selectOptions = $stockOptions;
            $selectValue = '';
            $selectPlaceholder = 'Extra stock item';
            $selectLabel = 'Extra stock item (optional)';
            $selectHint = 'For items not in the shop. Leave both empty to create from title.';
            $selectError = '';
            $selectSize = 'lg';
            $selectState = 'default';
            $selectMultiple = false;
            $selectRequired = false;
            $selectDisabled = false;
            $selectAttributes = [];
            $selectClass = 'w-full';
            require BASE_PATH . '/resources/views/components/base/select.php';
        ?></div>
    </div>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Notes <span class="font-normal text-slate-400">(optional)</span></span>
        <textarea name="description" id="expense-description" rows="3" class="<?= $fc ?>"></textarea>
    </label>

    <button type="submit" id="expense-save-btn" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-secondary transition">Save expense</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var metaById = {};
    document.querySelectorAll('#expenses-table tbody tr[data-row]').forEach(function (row) {
        var metaCell = row.querySelector('td[data-key="_meta"]');
        if (!metaCell) return;
        try {
            var meta = JSON.parse(metaCell.textContent.trim());
            if (meta && meta.id) metaById[String(meta.id)] = meta;
        } catch (e) {}
    });

    var inventoryPanel = document.getElementById('expense-inventory-panel');

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

    function getCategoryValue() {
        var hidden = document.querySelector('[data-custom-select][data-name="category"] [data-select-hidden-container] input');
        return hidden ? hidden.value : 'product_purchase';
    }

    function toggleInventoryPanel() {
        var show = getCategoryValue() === 'product_purchase';
        if (inventoryPanel) {
            inventoryPanel.classList.toggle('hidden', !show);
        }
    }

    document.addEventListener('select:change', function (e) {
        if (e.target && e.target.closest && e.target.closest('[data-custom-select][data-name="category"]')) {
            toggleInventoryPanel();
        }
    });

    function resetForm() {
        document.getElementById('expense-form-id').value = '0';
        document.getElementById('expense-title').value = '';
        document.getElementById('expense-amount').value = '';
        document.getElementById('expense-expense_date').value = new Date().toISOString().slice(0, 10);
        document.getElementById('expense-description').value = '';
        document.getElementById('expense-stock-quantity').value = '1';
        document.getElementById('expense-add-inventory').checked = true;
        setSelectValue('category', 'product_purchase');
        setSelectValue('product_id', '');
        setSelectValue('inventory_item_id', '');
        toggleInventoryPanel();
        document.getElementById('expense-save-btn').textContent = 'Save expense';
    }

    document.querySelector('[data-drawer-open="expense-drawer"]')?.addEventListener('click', function (e) {
        if (!e.target.closest('[data-expense-edit]')) resetForm();
    });

    document.addEventListener('click', function (e) {
        var editBtn = e.target.closest('[data-expense-edit]');
        if (!editBtn) return;
        var meta = metaById[editBtn.getAttribute('data-expense-edit')];
        if (!meta) return;
        document.getElementById('expense-form-id').value = meta.id;
        document.getElementById('expense-title').value = meta.title || '';
        document.getElementById('expense-amount').value = meta.amount || '';
        document.getElementById('expense-expense_date').value = meta.expense_date || '';
        document.getElementById('expense-description').value = meta.description || '';
        setSelectValue('category', meta.category || 'other');
        toggleInventoryPanel();
        document.getElementById('expense-save-btn').textContent = 'Update expense';
    });

    toggleInventoryPanel();
});
</script>
<?php
$drawerBody = (string) ob_get_clean();
$drawerId = 'expense-drawer';
$drawerTitle = 'Add business expense';
$drawerDescription = 'Product stock, packing, delivery and other costs from business cash.';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerSize = 'md';
require BASE_PATH . '/resources/views/components/base/drawer.php';
