<?php
declare(strict_types=1);
/** Categories admin filter component. */
?>
<div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
    <div class="min-w-0 flex-1 sm:max-w-3xl"><?php
        $inputName = '';
        $inputId = 'category-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search categories by name or link…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
        $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'categories-table', 'aria-label' => 'Search categories'];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/input.php';
    ?></div>
    <div class="w-full sm:w-48 shrink-0"><?php
        $selectName        = 'status_filter';
        $selectId          = 'category-status-filter';
        $selectOptions     = ['' => 'All statuses', 'active' => 'Active', 'inactive' => 'Inactive'];
        $selectValue       = '';
        $selectPlaceholder = '';
        $selectLabel       = '';
        $selectHint        = '';
        $selectError       = '';
        $selectSize        = 'lg';
        $selectState       = 'default';
        $selectMultiple    = false;
        $selectRequired    = false;
        $selectDisabled    = false;
        $selectClass       = '';
        $selectAttributes  = [
            'data-datatable-filter'  => '',
            'data-datatable-target'  => 'categories-table',
            'data-filter-key'        => 'status',
            'aria-label'             => 'Filter by status',
        ];
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>
    <?php
        $buttonLabel = 'Reset';
        $buttonVariant = 'outline';
        $buttonColor = 'primary';
        $buttonSize = 'lg';
        $buttonType = 'button';
        $buttonHref = $buttonName = $buttonValue = $buttonId = $buttonIcon = $buttonIconTrailing = '';
        $buttonIconOnly = $buttonFullWidth = $buttonDisabled = $buttonLoading = false;
        $buttonOnclick = $buttonClass = 'shrink-0';
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'categories-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
