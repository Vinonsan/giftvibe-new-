<?php
declare(strict_types=1);
/** Products admin filter component. */
?>
<div class="flex gap-3">
    <div class="flex-1"><?php
        $inputName = '';
        $inputId = 'product-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search by name, SKU or category…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'products-table', 'aria-label' => 'Search products'];
        $inputClass = $inputWrapperClass = '';
        require BASE_PATH . '/resources/views/components/base/input.php';
    ?></div>
    <?php
        $buttonLabel = 'Reset';
        $buttonVariant = 'outline';
        $buttonColor = 'primary';
        $buttonSize = 'lg';
        $buttonType = 'button';
        $buttonHref = $buttonName = $buttonValue = $buttonId = $buttonIcon = $buttonIconTrailing = '';
        $buttonIconOnly = $buttonFullWidth = $buttonDisabled = $buttonLoading = false;
        $buttonOnclick = $buttonClass = '';
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'products-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
