<?php
declare(strict_types=1);

$categoryFilterOptions = ['' => 'All categories'] + $categories;
?>
<div class="flex w-full flex-col gap-3 lg:flex-row lg:items-center">
    <div class="w-full min-w-0 flex-1 lg:max-w-[220px]"><?php
        $selectName = 'expense_category_filter';
        $selectId = 'expense-category-filter';
        $selectOptions = $categoryFilterOptions;
        $selectValue = '';
        $selectPlaceholder = 'All categories';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'expenses-table', 'data-filter-key' => 'category'];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <div class="min-w-0 flex-1"><?php
        $inputName = 'expense_search';
        $inputId = 'expenses-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search title, reference, category…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
        $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'expenses-table'];
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
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'expenses-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
