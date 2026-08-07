<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';

ob_start();
?>
<form id="customer-form" method="post" action="/admin/customers" class="space-y-4" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
    <input type="hidden" name="action" value="create_customer">

    <p class="rounded-xl border border-blue-100 bg-blue-50/60 px-3 py-2 text-xs text-blue-800">
        Add a customer manually for phone orders or walk-in sales. Leave password blank to auto-generate one.
    </p>

    <div class="grid gap-4 sm:grid-cols-2">
        <?php
        foreach ([
            ['first_name', 'First name', 'text', 'e.g. Nivetha', true],
            ['last_name', 'Last name', 'text', 'Optional', false],
            ['email', 'Email', 'email', 'customer@email.com', true],
            ['phone', 'Primary phone', 'tel', '0771234567', true],
            ['phone_2', 'Secondary phone', 'tel', 'Optional', false],
            ['city', 'City', 'text', 'Colombo', false],
            ['district', 'District', 'text', 'Western Province', false],
        ] as [$name, $label, $type, $placeholder, $required]) {
            $inputName = $name;
            $inputId = 'customer-' . $name;
            $inputLabel = $label;
            $inputValue = '';
            $inputType = $type;
            $inputPlaceholder = $placeholder;
            $inputHint = $inputError = '';
            $inputSize = 'lg';
            $inputState = 'default';
            $inputRequired = $required;
            $inputReadonly = $inputDisabled = false;
            $inputAutocomplete = 'off';
            $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
            $inputAttributes = [];
            $inputClass = 'w-full';
            $inputWrapperClass = 'w-full';
            require BASE_PATH . '/resources/views/components/base/input.php';
        }
        ?>
    </div>

    <?php
    $inputName = 'address_line_1';
    $inputId = 'customer-address_line_1';
    $inputLabel = 'Address';
    $inputValue = '';
    $inputType = 'text';
    $inputPlaceholder = 'Street address';
    $inputHint = $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = false;
    $inputReadonly = $inputDisabled = false;
    $inputAutocomplete = 'off';
    $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
    $inputAttributes = [];
    $inputClass = 'w-full';
    $inputWrapperClass = 'w-full';
    require BASE_PATH . '/resources/views/components/base/input.php';

    $inputName = 'password';
    $inputId = 'customer-password';
    $inputLabel = 'Password';
    $inputValue = '';
    $inputType = 'password';
    $inputPlaceholder = 'Leave blank to auto-generate';
    $inputHint = 'Minimum 8 characters if set manually.';
    $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = false;
    require BASE_PATH . '/resources/views/components/base/input.php';
    ?>

    <div><?php
        $selectName = 'status';
        $selectId = 'customer-status';
        $selectOptions = ['active' => 'Active', 'inactive' => 'Inactive'];
        $selectValue = 'active';
        $selectPlaceholder = 'Status';
        $selectLabel = 'Account status';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = true;
        $selectDisabled = false;
        $selectAttributes = [];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-secondary transition">Save customer</button>
</form>
<?php
$drawerBody = (string) ob_get_clean();
$drawerId = 'customer-drawer';
$drawerTitle = 'Add customer';
$drawerDescription = 'Create a customer account for manual orders and delivery.';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerSize = 'md';
require BASE_PATH . '/resources/views/components/base/drawer.php';
