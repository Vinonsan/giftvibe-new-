<?php
declare(strict_types=1);
?>
<div class="flex w-full flex-col gap-3 lg:flex-row lg:items-center">
    <div class="w-full min-w-0 flex-1 lg:max-w-[200px]"><?php
        $selectName = 'inventory_type_filter';
        $selectId = 'inventory-type-filter';
        $selectOptions = ['' => 'All types', 'catalog' => 'Shop products', 'stock' => 'Extra stock only'];
        $selectValue = '';
        $selectPlaceholder = 'All types';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'inventory-table', 'data-filter-key' => 'source_type'];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <div class="min-w-0 flex-1"><?php
        $inputName = 'inventory_search';
        $inputId = 'inventory-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search name or SKU…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
        $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'inventory-table'];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
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
        $buttonOnclick = $buttonClass = 'shrink-0 w-full lg:w-auto';
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'inventory-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
