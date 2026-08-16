<?php
declare(strict_types=1);

$renderInput = static function(string $name, string $label, string $value, string $type, string $placeholder = ''): void {
    $inputName = $name;
    $inputId = 'combos-' . $name;
    $inputLabel = $label;
    $inputValue = $value;
    $inputType = $type;
    $inputPlaceholder = $placeholder;
    $inputHint = '';
    $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = false;
    $inputAutocomplete = 'off';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = [];
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};
?>
<section class="mx-auto w-full py-8 max-w-7xl">

    <form id="combo-filters" class="grid gap-3 rounded-2xl border border-primary/10 p-4 sm:grid-cols-2 lg:grid-cols-[1.8fr_1.2fr_1.2fr_auto_auto]" action="<?= htmlspecialchars(app_url('/combos')) ?>" method="get">
        <?php $renderInput('search', 'Search combos', (string)$filters['search'], 'search', 'Combo name or description'); ?>
        <?php $renderInput('min_price', 'Minimum price', $filters['min_price'] > 0 ? (string)$filters['min_price'] : '', 'number', 'LKR ' . number_format((float)$priceBounds['min_price'], 0)); ?>
        <?php $renderInput('max_price', 'Maximum price', $filters['max_price'] > 0 ? (string)$filters['max_price'] : '', 'number', 'LKR ' . number_format((float)$priceBounds['max_price'], 0)); ?>
        
        <div class="self-end whitespace-nowrap rounded-xl  px-5 py-3.5 text-center text-sm font-semibold bg-primary text-white">
            <span><?= count($combos) ?></span> 
        </div>
        
        <div class="self-end">
            <?php 
            $buttonLabel = 'Reset';
            $buttonVariant = 'outline';
            $buttonColor = 'primary';
            $buttonSize = 'lg';
            $buttonType = 'button';
            $buttonHref = '';
            $buttonName = '';
            $buttonValue = '';
            $buttonId = 'combo-filter-reset';
            $buttonIcon = '';
            $buttonIconTrailing = '';
            $buttonIconOnly = false;
            $buttonFullWidth = true;
            $buttonDisabled = false;
            $buttonLoading = false;
            $buttonOnclick = '';
            $buttonClass = '';
            $buttonAttributes = [];
            require BASE_PATH . '/resources/views/components/base/button.php'; 
            ?>
        </div>
    </form>

    <div id="combo-grid" class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 2xl:grid-cols-4">
        <?php foreach ($combos as $combo) {
            require BASE_PATH . '/resources/views/components/base/combo-card.php';
        } ?>
    </div>

    <!-- Empty State -->
    <?php if (empty($combos)): ?>
        <div id="combo-empty" class="mt-8 rounded-2xl border border-dashed border-primary/20 bg-primary/5 px-6 py-16 text-center text-sm text-secondary/60">
            No gift combos match these filters.
        </div>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('combo-filters');
    if (!form) return;

    var searchInput = form.querySelector('[name="search"]');
    var minPriceInput = form.querySelector('[name="min_price"]');
    var maxPriceInput = form.querySelector('[name="max_price"]');
    var resetBtn = document.getElementById('combo-filter-reset');
    var timer = null;

    function applyFilters() {
        form.submit();
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(applyFilters, 400);
    });

    [minPriceInput, maxPriceInput].forEach(function (input) {
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(applyFilters, 400);
        });
    });

    resetBtn.addEventListener('click', function () {
        searchInput.value = '';
        minPriceInput.value = '';
        maxPriceInput.value = '';
        window.location.href = window.gvUrl('/combos');
    });
});
</script>
