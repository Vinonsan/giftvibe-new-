<?php
declare(strict_types=1);
/** Finance filter bar — drives summary cards + datatable. */

$currentYear = (int) date('Y');
$yearOptions = [];
for ($y = $currentYear; $y >= $currentYear - 3; $y--) {
    $yearOptions[(string) $y] = 'Year ' . $y;
}
?>
<div class="flex w-full flex-col gap-3 lg:flex-row lg:items-center">
    <div class="w-full min-w-0 flex-1 lg:max-w-[200px]"><?php
        $selectName = 'finance_year_filter';
        $selectId = 'finance-year-filter';
        $selectOptions = $yearOptions;
        $selectValue = '';
        $selectPlaceholder = 'All years';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'finance-table', 'data-filter-key' => 'year', 'aria-label' => 'Filter by year'];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <div class="w-full min-w-0 flex-1 lg:max-w-[200px]"><?php
        $selectName = 'finance_month_filter';
        $selectId = 'finance-month-filter';
        $selectOptions = [
            'january' => 'January', 'february' => 'February', 'march' => 'March', 'april' => 'April',
            'may' => 'May', 'june' => 'June', 'july' => 'July', 'august' => 'August',
            'september' => 'September', 'october' => 'October', 'november' => 'November', 'december' => 'December',
        ];
        $selectValue = '';
        $selectPlaceholder = 'All months';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'finance-table', 'data-filter-key' => 'month', 'aria-label' => 'Filter by month'];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <div class="min-w-0 flex-1"><?php
        $inputName = 'finance_search';
        $inputId = 'finance-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search order #, customer…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
        $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'finance-table', 'aria-label' => 'Search transactions'];
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
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'finance-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
