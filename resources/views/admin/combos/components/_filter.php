<?php
declare(strict_types=1);
/** Combos admin filter component. */
?>
<div class="flex gap-3">
    <div class="flex-1"><?php
        $inputName = '';
        $inputId = 'combo-table-search';
        $inputLabel = '';
        $inputValue = '';
        $inputType = 'search';
        $inputPlaceholder = 'Search combos...';
        $inputHint = $inputError = '';
        $inputSize = 'lg';
        $inputState = 'default';
        $inputRequired = $inputReadonly = $inputDisabled = false;
        $inputAutocomplete = 'off';
        $inputLeadingIcon = $inputPrefix = $inputTrailingIcon = $inputSuffix = '';
        $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'combos-table', 'aria-label' => 'Search combos'];
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
        $buttonAttributes = ['data-datatable-reset' => '', 'data-datatable-target' => 'combos-table'];
        require BASE_PATH . '/resources/views/components/base/button.php';
    ?>
</div>
