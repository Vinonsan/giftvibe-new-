<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';

ob_start();
?>
<form id="inventory-adjust-form" method="post" action="/admin/inventory" class="space-y-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
    <input type="hidden" name="action" value="adjust_stock">
    <input type="hidden" name="inventory_item_id" id="adjust-item-id" value="0">

    <p id="adjust-item-label" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-secondary">—</p>

    <p class="text-xs text-slate-500">Use positive numbers to add stock, negative to remove (e.g. damage or correction).</p>

    <?php
    $inputName = 'change_qty';
    $inputId = 'adjust-change-qty';
    $inputLabel = 'Quantity change';
    $inputValue = '';
    $inputType = 'number';
    $inputPlaceholder = 'e.g. 10 or -3';
    $inputHint = $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = true;
    $inputReadonly = $inputDisabled = false;
    $inputAutocomplete = 'off';
    $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
    $inputAttributes = ['step' => '1'];
    $inputClass = 'w-full';
    $inputWrapperClass = 'w-full';
    require BASE_PATH . '/resources/views/components/base/input.php';
    ?>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Unit price (LKR) <span class="font-normal text-slate-400">(for purchased stock)</span></span>
        <input type="number" min="0" step="0.01" name="unit_cost" id="adjust-unit-cost" class="<?= $fc ?>" placeholder="0.00">
        <span class="text-xs text-slate-500">Positive quantity × unit price is automatically deducted from cash as an expense.</span>
    </label>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Note <span class="font-normal text-slate-400">(optional)</span></span>
        <input type="text" name="note" id="adjust-note" class="<?= $fc ?>" placeholder="Reason for adjustment">
    </label>

    <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-secondary transition">Update stock</button>
</form>

<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-adjust-id]');
    if (!btn) return;
    document.getElementById('adjust-item-id').value = btn.getAttribute('data-adjust-id') || '0';
    document.getElementById('adjust-item-label').textContent = btn.getAttribute('data-adjust-name') || 'Item';
    document.getElementById('adjust-change-qty').value = '';
    document.getElementById('adjust-unit-cost').value = '';
    document.getElementById('adjust-note').value = '';
});
</script>
<?php
$drawerBody = (string) ob_get_clean();
$drawerId = 'inventory-adjust-drawer';
$drawerTitle = 'Adjust stock';
$drawerDescription = 'Add or remove units. Catalog products stay in sync with the Products page.';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerSize = 'sm';
require BASE_PATH . '/resources/views/components/base/drawer.php';
