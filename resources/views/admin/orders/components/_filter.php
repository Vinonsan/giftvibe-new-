<?php
declare(strict_types=1);
/** Orders admin filter component. */
?>
<div class="flex w-full flex-col gap-3 lg:flex-row lg:items-center">
    <div class="min-w-0 flex-1"><?php
        $inputName = 'order_search';
        $inputId = 'order-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search by order #, customer name or phone…';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
        $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'orders-table', 'aria-label' => 'Search orders'];
        $inputClass = 'w-full';
        $inputWrapperClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/input.php';
    ?></div>

    <div class="w-full min-w-0 flex-1 lg:max-w-[220px]"><?php
        $selectName = 'order_status_filter';
        $selectId = 'order-status-filter';
        $selectOptions = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'ready' => 'Ready',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        $selectValue = '';
        $selectPlaceholder = 'All order statuses';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'orders-table', 'data-filter-key' => 'status', 'aria-label' => 'Filter by order status'];
        $selectClass = 'w-full';
        require BASE_PATH . '/resources/views/components/base/select.php';
    ?></div>

    <div class="w-full min-w-0 flex-1 lg:max-w-[220px]"><?php
        $selectName = 'payment_status_filter';
        $selectId = 'payment-status-filter';
        $selectOptions = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
        ];
        $selectValue = '';
        $selectPlaceholder = 'All payment statuses';
        $selectLabel = '';
        $selectHint = $selectError = '';
        $selectSize = 'lg';
        $selectState = 'default';
        $selectMultiple = false;
        $selectRequired = false;
        $selectDisabled = false;
        $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'orders-table', 'data-filter-key' => 'payment_verification', 'aria-label' => 'Filter by payment status'];
        $selectClass = 'w-full';
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
        $buttonOnclick = $buttonClass = 'shrink-0 w-full lg:w-auto';
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'orders-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
