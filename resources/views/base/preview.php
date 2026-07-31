<?php

declare(strict_types=1);

/**
 * Base components preview page — renders every component and variant.
 * Route: /base  →  App\Controllers\Public\BaseController@index
 */

$C = BASE_PATH . '/resources/views/components/base/';

$svgSearch = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>';
$svgPlus   = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>';
$svgPencil = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>';
$svgTrash  = '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
$svgMail   = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>';
$svgUser   = '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>';

/* --------------------------------------------------------------------------
 * Small render helpers so each component gets a clean set of variables.
 * ------------------------------------------------------------------------ */

$renderButton = function (
    string $label,
    string $variant = 'solid',
    string $color = 'primary',
    string $size = 'md',
    string $icon = '',
    bool $iconOnly = false,
    bool $loading = false,
    bool $disabled = false,
) use ($C): void {
    $buttonLabel = $label;
    $buttonVariant = $variant;
    $buttonColor = $color;
    $buttonSize = $size;
    $buttonIcon = $icon;
    $buttonIconTrailing = '';
    $buttonIconOnly = $iconOnly;
    $buttonLoading = $loading;
    $buttonDisabled = $disabled;
    $buttonFullWidth = false;
    $buttonHref = '';
    $buttonOnclick = '';
    $buttonClass = '';
    $buttonAttributes = [];
    $buttonType = 'button';
    $buttonName = '';
    $buttonValue = '';
    $buttonId = '';
    require $C . 'button.php';
};

$renderInput = function (string $name, string $label = '', string $value = '', string $placeholder = '', string $type = 'text', string $error = '', string $hint = '', string $leading = '', string $trailing = '', string $size = 'md', bool $required = false, string $state = '') use ($C): void {
    $inputName = $name;
    $inputLabel = $label;
    $inputValue = $value;
    $inputPlaceholder = $placeholder;
    $inputType = $type;
    $inputError = $error;
    $inputHint = $hint;
    $inputLeadingIcon = $leading;
    $inputTrailingIcon = $trailing;
    $inputSize = $size;
    $inputRequired = $required;
    $inputState = $state;
    $inputPrefix = '';
    $inputSuffix = '';
    $inputId = '';
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputAttributes = [];
    $inputClass = '';
    $inputWrapperClass = '';
    require $C . 'input.php';
};

$renderSelect = function (string $name, array $options, string $label = '', $value = null, string $placeholder = '', bool $multiple = false, string $error = '', string $hint = '', string $size = 'md') use ($C): void {
    $selectName = $name;
    $selectOptions = $options;
    $selectLabel = $label;
    $selectValue = $value;
    $selectPlaceholder = $placeholder;
    $selectMultiple = $multiple;
    $selectError = $error;
    $selectHint = $hint;
    $selectSize = $size;
    $selectId = '';
    $selectRequired = false;
    $selectDisabled = false;
    $selectState = '';
    $selectAttributes = [];
    $selectClass = '';
    require $C . 'select.php';
};

$renderDateInput = function (string $name, string $label = '', string $value = '', string $min = '', string $max = '', string $error = '', string $hint = '', string $size = 'md') use ($C): void {
    $dateInputName = $name;
    $dateInputLabel = $label;
    $dateInputValue = $value;
    $dateInputMin = $min;
    $dateInputMax = $max;
    $dateInputError = $error;
    $dateInputHint = $hint;
    $dateInputSize = $size;
    $dateInputId = '';
    $dateInputRequired = false;
    $dateInputDisabled = false;
    $dateInputState = '';
    $dateInputAttributes = [];
    $dateInputClass = '';
    require $C . 'date-input.php';
};

$renderPhoneInput = function (string $name, string $label = '', string $value = '', string $code = '94', string $error = '', string $hint = '', string $size = 'md') use ($C): void {
    $phoneInputName = $name;
    $phoneInputLabel = $label;
    $phoneInputValue = $value;
    $phoneCodeValue = $code;
    $phoneInputError = $error;
    $phoneInputHint = $hint;
    $phoneInputSize = $size;
    $phoneInputId = '';
    $phoneCodeName = 'country_code';
    $phoneInputRequired = false;
    $phoneInputDisabled = false;
    $phoneInputState = '';
    $phoneInputAttributes = [];
    $phoneInputClass = '';
    require $C . 'phone-input.php';
};

$renderBadge = function (string $label, string $variant = 'soft', string $color = 'primary', string $size = 'md', bool $removable = false, string $icon = '') use ($C): void {
    $badgeLabel = $label;
    $badgeVariant = $variant;
    $badgeColor = $color;
    $badgeSize = $size;
    $badgeRemovable = $removable;
    $badgeIcon = $icon;
    $badgeOnRemove = '';
    $badgeRounded = 'full';
    $badgeAttributes = [];
    $badgeClass = '';
    require $C . 'badge.php';
};

/* Capture helper — renders a callable into a string. */
$capture = static function (callable $fn): string {
    ob_start();
    $fn();
    return (string) ob_get_clean();
};

/** Section wrapper. */
$section = function (string $id, string $title, string $subtitle, string $body): void {
    echo '<section id="' . htmlspecialchars($id) . '" class="space-y-4">';
    echo '<div><h2 class="text-xl font-bold text-secondary">' . htmlspecialchars($title) . '</h2>';
    echo '<p class="text-sm text-slate-500">' . htmlspecialchars($subtitle) . '</p></div>';
    echo '<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">' . $body . '</div>';
    echo '</section>';
};

$colors = ['primary', 'secondary', 'accent', 'success', 'warning', 'danger'];
$variants = ['solid', 'outline', 'soft', 'ghost', 'link'];
?>

<section class="mx-auto max-w-7xl space-y-14 px-4 py-12 sm:px-6 lg:px-8">
    <header class="space-y-2">
        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Component Library</p>
        <h1 class="text-3xl font-extrabold text-secondary">GiftVibe Base UI</h1>
        <p class="max-w-2xl text-sm text-slate-500">
            Every component lives in <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">resources/views/components/base/</code>.
            Set the documented variables, then <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">require</code> the file.
            Full docs in <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">README.md</code>.
        </p>
    </header>

    <?php
    /* ======================= BUTTONS ======================= */
    $buttonBody = '';
    foreach ($colors as $color) {
        $buttonBody .= '<div class="space-y-2">';
        $buttonBody .= '<p class="text-xs font-semibold uppercase tracking-wide text-slate-400">' . $color . '</p>';
        $buttonBody .= '<div class="flex flex-wrap items-center gap-3">';
        $buttonBody .= $capture(static function () use ($variants, $renderButton, $color): void {
            foreach ($variants as $variant) {
                $renderButton(ucfirst($variant), $variant, $color, 'sm');
            }
        });
        $buttonBody .= '</div></div>';
    }

    /* Sizes */
    $buttonBody .= '<div class="mt-6 border-t border-slate-100 pt-6">';
    $buttonBody .= '<p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Sizes</p>';
    $buttonBody .= '<div class="flex flex-wrap items-center gap-3">';
    $buttonBody .= $capture(static function () use ($renderButton): void {
        foreach (['xs', 'sm', 'md', 'lg', 'xl'] as $s) {
            $renderButton(ucfirst($s), 'solid', 'primary', $s);
        }
    });
    $buttonBody .= '</div></div>';

    /* States & icons */
    $buttonBody .= '<div class="mt-6 border-t border-slate-100 pt-6">';
    $buttonBody .= '<p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">States & icons</p>';
    $buttonBody .= '<div class="flex flex-wrap items-center gap-3">';
    $buttonBody .= $capture(static function () use ($renderButton, $svgPlus, $svgTrash): void {
        $renderButton('Loading…', 'solid', 'primary', 'md', '', false, true);
        $renderButton('Disabled', 'solid', 'secondary', 'md', '', false, false, true);
        $renderButton('With icon', 'outline', 'primary', 'md', $svgPlus);
        $renderButton('Link', 'link', 'primary', 'md');
        $renderButton('', 'soft', 'danger', 'md', $svgTrash, true);
    });
    $buttonBody .= '</div></div>';

    $section('buttons', 'Buttons', '5 variants × 6 colours, 5 sizes, icons, loading & disabled states.', $buttonBody);
    ?>

    <?php
    /* ======================= INPUTS ======================= */
    $inputBody = $capture(static function () use ($renderInput, $svgMail, $svgSearch): void {
        echo '<div class="grid gap-5 lg:grid-cols-2">';
        $renderInput('fullname', 'Full name', 'Kamal Perera', 'e.g. Kamal Perera', 'text', '', 'Used for billing.', '', '', 'md', true);
        $renderInput('email', 'Email', '', 'you@example.com', 'email', '', '', $svgMail, '', 'md', true);
        $renderInput('search', '', '', 'Search products…', 'search', '', '', $svgSearch);
        $renderInput('price', 'Price (LKR)', '', '0.00', 'number', '', '', '', ' LKR');
        $renderInput('password', 'Password', '', '••••••••', 'password');
        $renderInput('invalid', 'With error', 'oops', '', 'text', 'This field is required.');
        $renderInput('small', 'Small', '', 'Small input', 'text', '', '', '', '', 'sm');
        $renderInput('large', 'Large', '', 'Large input', 'text', '', '', '', '', 'lg');
        echo '</div>';
    });
    $section('inputs', 'Inputs', 'All types, sizes, states, prefixes/suffixes and icons.', $inputBody);
    ?>

    <?php
    /* ======================= SELECTS ======================= */
    $selectBody = $capture(static function () use ($renderSelect): void {
        echo '<div class="grid gap-5 lg:grid-cols-2">';
        $renderSelect('category', ['' => 'Choose…', 'gifts' => 'Gifts', 'flowers' => 'Flowers', 'custom' => 'Custom Gifts'], 'Category', 'gifts');
        $renderSelect('occasion', [
            ['label' => 'Occasions', 'options' => ['birthday' => 'Birthday', 'wedding' => 'Wedding', 'anniversary' => 'Anniversary']],
            ['label' => 'Recipients', 'options' => ['her' => 'For Her', 'him' => 'For Him']],
        ], 'Occasion', '', 'Select an occasion');
        $renderSelect('tags[]', [
            ['label' => 'Themes', 'options' => ['romantic' => 'Romantic', 'fun' => 'Fun', 'luxury' => 'Luxury']],
        ], 'Tags (multi)', ['romantic', 'fun'], '', true);
        $renderSelect('broken', ['a' => 'Option A', 'b' => 'Option B'], 'With error', '', '', false, 'Please choose an option.');
        echo '</div>';
    });
    $section('selects', 'Selects', 'Shorthand options, optgroups, multi-select and error state.', $selectBody);
    ?>

    <?php
    /* ======================= DATE INPUTS ======================= */
    $dateBody = $capture(static function () use ($renderDateInput): void {
        echo '<div class="grid gap-5 lg:grid-cols-3">';
        $renderDateInput('event_date', 'Event date', '2026-08-15');
        $renderDateInput('min_date', 'Min today', '', date('Y-m-d'), '2026-12-31', '', 'Can\'t pick past dates.');
        $renderDateInput('bad_date', 'With error', '', '', '', 'Invalid date.');
        echo '</div>';
    });
    $section('date-inputs', 'Date inputs', 'Native picker with calendar icon, min/max and error state.', $dateBody);
    ?>

    <?php
    /* ======================= PHONE INPUTS ======================= */
    $phoneBody = $capture(static function () use ($renderPhoneInput): void {
        echo '<div class="grid gap-5 lg:grid-cols-2">';
        $renderPhoneInput('mobile', 'Mobile number', '77 123 4567');
        $renderPhoneInput('work', 'Work number', '', '971', '', 'Country code + number.');
        echo '</div>';
    });
    $section('phone-inputs', 'Phone inputs', 'Country-code select + telephone field.', $phoneBody);
    ?>

    <?php
    /* ======================= DROPDOWNS ======================= */
    $dropdownBody = $capture(static function () use ($C, $svgPencil, $svgPlus, $svgTrash, $svgUser): void {
        echo '<div class="flex flex-wrap items-start gap-8">';

        $dropdownId = 'dd-actions';
        $dropdownLabel = 'Actions';
        $dropdownTriggerVariant = 'outline';
        $dropdownTriggerColor = 'secondary';
        $dropdownTriggerSize = 'md';
        $dropdownTriggerIcon = '';
        $dropdownTrigger = '';
        $dropdownAlign = 'left';
        $dropdownWidth = 'w-56';
        $dropdownMenuClass = '';
        $dropdownIconTrailing = '';
        $dropdownItems = [
            ['type' => 'header', 'label' => 'Product actions'],
            ['label' => 'Edit', 'icon' => $svgPencil, 'href' => '#'],
            ['label' => 'Duplicate', 'icon' => $svgPlus, 'onclick' => "alert('Duplicated')"],
            ['type' => 'divider'],
            ['label' => 'Mark inactive', 'active' => true],
            ['label' => 'Delete', 'danger' => true, 'icon' => $svgTrash, 'onclick' => "confirm('Delete?') && alert('Deleted')"],
        ];
        require $C . 'dropdown-menu.php';

        $dropdownId = 'dd-profile';
        $dropdownLabel = 'Account';
        $dropdownTriggerVariant = 'soft';
        $dropdownTriggerColor = 'primary';
        $dropdownTriggerSize = 'md';
        $dropdownTriggerIcon = $svgUser;
        $dropdownTrigger = '';
        $dropdownAlign = 'right';
        $dropdownWidth = 'w-56';
        $dropdownMenuClass = '';
        $dropdownIconTrailing = '';
        $dropdownItems = [
            ['label' => 'Profile', 'href' => '#'],
            ['label' => 'Settings', 'href' => '#'],
            ['type' => 'divider'],
            ['label' => 'Sign out', 'danger' => true],
        ];
        require $C . 'dropdown-menu.php';

        echo '</div>';
    });
    $section('dropdowns', 'Dropdown menus', 'Trigger buttons, items with icons, dividers, headers, danger & active states.', $dropdownBody);
    ?>

    <?php
    /* ======================= DATA TABLE ======================= */
    $sampleNames = ['Teddy Bear', 'Birthday Cake', 'Flower Bouquet', 'Wrist Watch', 'Perfume Set', 'Chocolate Box', 'Photo Frame', 'Spa Voucher', 'Gift Card', 'Candle Set', 'Jewellery Box', 'Backpack', 'Smart Speaker', 'Desk Lamp', 'Wireless Earbuds', 'Scarf', 'Mug Set', 'Plant Pot', 'Sunglasses', 'Notebook', 'Umbrella', 'T-shirt', 'Keychain', 'Book'];
    $sampleRows = [];
    foreach ($sampleNames as $i => $name) {
        $sampleRows[] = [
            'name'  => $name,
            'sku'   => 'GV-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
            'price' => 500 + ($i * 137) % 4500,
            'stock' => $i % 4 === 0 ? 0 : ($i * 7) % 120,
            '_actions' => '<a href="#" class="text-primary hover:underline">Edit</a>',
        ];
    }

    $tableBody = $capture(static function () use ($C, $sampleRows): void {
        $badgeRender = static function (array $row) use ($C): string {
            $stock = (int) $row['stock'];
            $badgeLabel = $stock === 0 ? 'Out of stock' : ($stock < 20 ? 'Low: ' . $stock : 'In stock');
            $badgeVariant = 'soft';
            $badgeColor = $stock === 0 ? 'danger' : ($stock < 20 ? 'warning' : 'success');
            $badgeSize = 'sm';
            $badgeRemovable = false;
            $badgeIcon = '';
            $badgeOnRemove = '';
            $badgeRounded = 'full';
            $badgeAttributes = [];
            $badgeClass = '';
            ob_start();
            require $C . 'badge.php';
            return (string) ob_get_clean();
        };

        $tableId = 'products-table';
        $tableColumns = [
            ['key' => 'name',  'label' => 'Product', 'sortable' => true],
            ['key' => 'sku',   'label' => 'SKU', 'sortable' => true],
            ['key' => 'price', 'label' => 'Price', 'type' => 'number', 'align' => 'right', 'sortable' => true,
             'format' => fn ($v) => 'LKR ' . number_format((float) $v, 2)],
            ['key' => 'stock', 'label' => 'Stock', 'type' => 'number', 'align' => 'right', 'sortable' => true,
             'render' => $badgeRender],
            ['label' => '', 'align' => 'right', 'width' => 'w-24'],
        ];
        $tableRows = $sampleRows;
        $tableTitle = 'Products';
        $tableSearchable = true;
        $tableSearchPlaceholder = 'Search products…';
        $tableSortable = true;
        $tableDefaultSort = ['key' => 'name', 'dir' => 'asc'];
        $tablePaginated = true;
        $tablePerPage = 8;
        $tableZebra = true;
        $tableEmptyMessage = 'No products match your search.';
        $tableFooter = false;
        $tableClass = '';
        $tableAttributes = [];
        require $C . 'datatable.php';
    });
    $section('tables', 'Data table', 'Client-side search, click-to-sort columns and pagination.', $tableBody);
    ?>

    <?php
    /* ======================= MODALS ======================= */
    $modalBody = $capture(static function () use ($C, $renderInput, $capture): void {
        echo '<div class="flex flex-wrap items-center gap-3">';

        $modalId = 'modal-sm';
        $modalTriggerLabel = 'Small';
        $modalTriggerVariant = 'outline';
        $modalTriggerColor = 'primary';
        $modalTriggerSize = 'md';
        $modalTriggerIcon = '';
        $modalTrigger = '';
        $modalTitle = 'Small modal';
        $modalDescription = 'max-w-md';
        $modalBodyContent = '<p>Perfect for confirmations and quick forms.</p>';
        $modalFooter = '<button data-modal-close class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-slate-50">Cancel</button> <button data-modal-close class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">Confirm</button>';
        $modalSize = 'sm';
        $modalStatic = false;
        $modalCloseOnEsc = true;
        $modalScrollable = false;
        $modalShowCloseButton = true;
        require $C . 'modal.php';

        $modalId = 'modal-md';
        $modalTriggerLabel = 'Medium';
        $modalTriggerVariant = 'outline';
        $modalTriggerColor = 'primary';
        $modalTriggerSize = 'md';
        $modalTriggerIcon = '';
        $modalTrigger = '';
        $modalTitle = 'Medium modal';
        $modalDescription = 'A form inside a modal';
        $modalBodyContent = '<div class="space-y-4"><p>Modals reuse the base components — here is a form inside.</p>'
            . $capture(static function () use ($renderInput): void {
                $renderInput('m_title', 'Product title', '', 'e.g. Teddy Bear', 'text', '', '', '', '', 'md', true);
            })
            . '</div>';
        $modalFooter = '<button data-modal-close class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-slate-50">Cancel</button> <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">Save</button>';
        $modalSize = 'md';
        $modalStatic = false;
        $modalCloseOnEsc = true;
        $modalScrollable = false;
        $modalShowCloseButton = true;
        require $C . 'modal.php';

        $modalId = 'modal-danger';
        $modalTriggerLabel = 'Danger confirm';
        $modalTriggerVariant = 'soft';
        $modalTriggerColor = 'danger';
        $modalTriggerSize = 'md';
        $modalTriggerIcon = '';
        $modalTrigger = '';
        $modalTitle = 'Delete order?';
        $modalDescription = 'This cannot be undone.';
        $modalBodyContent = '<p>Are you sure you want to permanently delete order <strong>#1024</strong>?</p>';
        $modalFooter = '<button data-modal-close class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-slate-50">Cancel</button> <button data-modal-close class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Delete order</button>';
        $modalSize = 'sm';
        $modalStatic = true;
        $modalCloseOnEsc = true;
        $modalScrollable = false;
        $modalShowCloseButton = true;
        require $C . 'modal.php';

        echo '</div>';
    });
    $section('modals', 'Modals', 'Sizes, custom trigger buttons and footer actions. Escape / backdrop close (unless static).', $modalBody);
    ?>

    <?php
    /* ======================= DRAWERS ======================= */
    $drawerBody = $capture(static function () use ($C): void {
        echo '<div class="flex flex-wrap items-center gap-3">';

        $drawerId = 'drawer-right';
        $drawerSide = 'right';
        $drawerSize = 'md';
        $drawerTriggerLabel = 'Right drawer';
        $drawerTriggerVariant = 'outline';
        $drawerTriggerColor = 'secondary';
        $drawerTriggerSize = 'md';
        $drawerTriggerIcon = '';
        $drawerTrigger = '';
        $drawerTitle = 'Shopping cart';
        $drawerDescription = '2 items';
        $drawerBodyContent = '<p>Your cart lives here.</p>';
        $drawerFooter = '<button data-drawer-close class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-slate-50">Continue shopping</button> <button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">Checkout</button>';
        $drawerStatic = false;
        $drawerCloseOnEsc = true;
        $drawerShowCloseButton = true;
        $drawerOverlay = true;
        require $C . 'drawer.php';

        $drawerId = 'drawer-left';
        $drawerSide = 'left';
        $drawerSize = 'lg';
        $drawerTriggerLabel = 'Left drawer';
        $drawerTriggerVariant = 'soft';
        $drawerTriggerColor = 'accent';
        $drawerTriggerSize = 'md';
        $drawerTriggerIcon = '';
        $drawerTrigger = '';
        $drawerTitle = 'Filters';
        $drawerDescription = 'Refine your search';
        $drawerBodyContent = '<p>Filter controls could go here.</p>';
        $drawerFooter = '<button data-drawer-close class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">Apply filters</button>';
        $drawerStatic = false;
        $drawerCloseOnEsc = true;
        $drawerShowCloseButton = true;
        $drawerOverlay = true;
        require $C . 'drawer.php';

        echo '</div>';
    });
    $section('drawers', 'Drawers', 'Slide-in panels from either edge with header, body and footer.', $drawerBody);
    ?>

    <?php
    /* ======================= BADGES ======================= */
    $badgeBody = '';
    foreach ($colors as $color) {
        $badgeBody .= '<div class="space-y-2">';
        $badgeBody .= '<p class="text-xs font-semibold uppercase tracking-wide text-slate-400">' . $color . '</p>';
        $badgeBody .= '<div class="flex flex-wrap items-center gap-3">';
        $badgeBody .= $capture(static function () use ($renderBadge, $color): void {
            $renderBadge(ucfirst($color), 'solid', $color);
            $renderBadge('Soft', 'soft', $color);
            $renderBadge('Outline', 'outline', $color);
            $renderBadge('Dot', 'dot', $color);
        });
        $badgeBody .= '</div></div>';
    }
    $badgeBody .= '<div class="mt-6 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">';
    $badgeBody .= $capture(static function () use ($renderBadge, $svgPlus): void {
        $renderBadge('Neutral', 'soft', 'neutral');
        $renderBadge('Sizes', 'solid', 'primary', 'sm');
        $renderBadge('Sizes', 'solid', 'primary', 'md');
        $renderBadge('Sizes', 'solid', 'primary', 'lg');
        $renderBadge('Removable', 'soft', 'primary', 'md', true);
        $renderBadge('With icon', 'outline', 'success', 'md', false, $svgPlus);
    });
    $badgeBody .= '</div>';
    $section('badges', 'Badges', '4 variants × 6 colours + neutral, sizes, removable and icon badges.', $badgeBody);
    ?>
</section>
