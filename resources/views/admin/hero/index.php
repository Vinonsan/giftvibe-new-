<?php

declare(strict_types=1);

$slides = $slides ?? [];
$editSlide = $editSlide ?? null;
$parts = explode('|', (string) ($editSlide['subtitle'] ?? ''));
$background = strtoupper((string) ($editSlide['background_color'] ?? '#0B1528'));
$background = preg_match('/^#[0-9A-F]{6}$/', $background) ? $background : '#0B1528';
$heroButtonColor = strtoupper((string) ($editSlide['button_color'] ?? '#102E50'));
$heroButtonColor = preg_match('/^#[0-9A-F]{6}$/', $heroButtonColor) ? $heroButtonColor : '#102E50';

$tableRows = [];

$input = static function(string $name, string $label, string $value = '', string $type = 'text', bool $required = false, string $placeholder = '', string $hint = '', array $attributes = []): void {
    $inputType = $type;
    $inputName = $name;
    $inputId = $name;
    $inputValue = $value;
    $inputPlaceholder = $placeholder;
    $inputLabel = $label;
    $inputHint = $hint;
    $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = $required;
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = $attributes;
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};

foreach ($slides as $slide) {
    $slideParts = explode('|', (string) ($slide['subtitle'] ?? ''));
    $id = (int) $slide['id'];
    $image = htmlspecialchars((string) $slide['image_path'], ENT_QUOTES);
    $title = htmlspecialchars((string) $slide['title'], ENT_QUOTES);
    $description = htmlspecialchars((string) ($slideParts[1] ?? ''), ENT_QUOTES);
    $status = $slide['status'] === 'active'
        ? '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>'
        : '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Inactive</span>';

    ob_start();
    ?>
    <div class="flex items-center justify-end gap-2">
        <a href="/admin/hero?edit=<?= $id ?>" title="Edit banner" aria-label="Edit banner" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM16.862 4.487 19.5 7.125"/></svg>
        </a>
        <form method="post" action="/admin/hero" onsubmit="return confirm('Delete this hero banner?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" title="Delete banner" aria-label="Delete banner" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50 hover:text-rose-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            </button>
        </form>
    </div>
    <?php
    $actions = ob_get_clean();

    $tableRows[] = [
        'banner' => '<div class="flex min-w-64 items-center gap-3"><img src="' . $image . '" alt="" class="h-14 w-20 rounded-lg bg-slate-100 object-cover border border-primary/10"><div class="min-w-0"><p class="truncate font-semibold text-secondary">' . $title . '</p><p class="mt-1 max-w-sm truncate text-xs text-slate-500">' . $description . '</p></div></div>',
        'status' => $status,
        'sort_order' => (int) $slide['sort_order'],
        'updated_at' => (string) ($slide['updated_at'] ?? ''),
        '_actions' => $actions,
    ];
}

ob_start();
?>
<form id="hero-banner-form" method="post" action="/admin/hero" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editSlide): ?><input type="hidden" name="id" value="<?= (int) $editSlide['id'] ?>"><?php endif; ?>

    <?php $input('title', 'SEO title', $editSlide['title'] ?? '', 'text', true, 'Find the perfect gift'); ?>

    <div class="grid gap-4 sm:grid-cols-2">
        <?php $input('eyebrow', 'Eyebrow', $parts[0] ?? '', 'text', false, 'Crafted with love'); ?>
        <?php $input('price', 'Price text', $parts[2] ?? '', 'text', false, 'From LKR 4,500'); ?>
    </div>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">SEO description</span>
        <textarea name="description" rows="3" maxlength="220" placeholder="Describe this banner clearly for customers and search engines" class="w-full rounded-xl border border-primary/10 bg-white p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string) ($parts[1] ?? '')) ?></textarea>
    </label>

    <?php
    $fileName = 'hero_image';
    $fileId = 'hero-image';
    $fileLabel = 'Banner image';
    $fileHint = 'JPG, PNG or WebP · maximum 5MB · recommended 1200 × 800px';
    $fileAccept = 'image/png,image/jpeg,image/webp';
    $fileRequired = $editSlide === null;
    $fileCurrentUrl = (string) ($editSlide['image_path'] ?? '');
    require BASE_PATH . '/resources/views/components/base/file-input.php';
    ?>

    <div class="grid gap-4 sm:grid-cols-2">
        <?php $input('button_label', 'Button label', $parts[3] ?? 'Shop now', 'text', false); ?>
        <?php $input('link_url', 'Button link', $editSlide['link_url'] ?? '/shop', 'text', false); ?>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <label class="block space-y-1.5">
            <span class="block text-xs font-semibold text-slate-600">Background</span>
            <input type="color" name="background" value="<?= htmlspecialchars($background) ?>" class="h-11 w-full rounded-xl border border-primary/10 bg-white p-1">
        </label>
        <label class="block space-y-1.5">
            <span class="block text-xs font-semibold text-slate-600">Button color</span>
            <input type="color" name="button_color" value="<?= htmlspecialchars($heroButtonColor) ?>" class="h-11 w-full rounded-xl border border-primary/10 bg-white p-1">
        </label>
        <?php $input('sort_order', 'Sort order', (string)($editSlide['sort_order'] ?? count($slides) + 1), 'number', false, '', '', ['min' => '0']); ?>
    </div>

    <?php
    $toggleName = 'status';
    $toggleId = 'hero-status';
    $toggleChecked = !$editSlide || $editSlide['status'] === 'active';
    $toggleLabel = 'Active';
    $toggleHint = 'Show this banner on the homepage.';
    $toggleError = '';
    $toggleSize = 'md';
    $toggleColor = 'primary';
    $toggleState = 'default';
    $toggleRequired = false;
    $toggleDisabled = false;
    $toggleAttributes = ['value' => 'active'];
    $toggleClass = '';
    require BASE_PATH . '/resources/views/components/base/toggle.php';
    ?>
</form>
<?php
$drawerBody = (string) ob_get_clean();
$drawerFooter = '<button type="button" data-drawer-close class="rounded-xl border border-primary/10 px-4 py-2.5 text-sm font-semibold text-secondary transition hover:bg-primary/5">Cancel</button>'
    . '<button type="submit" form="hero-banner-form" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-secondary">' . ($editSlide ? 'Update banner' : 'Add banner') . '</button>';
$drawerId = 'hero-banner-drawer';
$drawerSide = 'right';
$drawerSize = 'lg';
$drawerTitle = $editSlide ? 'Edit hero banner' : 'Add hero banner';
$drawerDescription = 'Manage homepage content and SEO details.';
$drawerTrigger = '<button type="button" data-drawer-open="hero-banner-drawer" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-secondary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>Add Banner</button>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
?>

<div class="space-y-5">
    <?php if ($flash): ?><span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'], ENT_QUOTES) ?>"></span><?php endif; ?>

    <div class="space-y-4">
        <div class="flex flex-col gap-4 rounded-2xl border border-primary/10 bg-white px-5 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h1 class="text-xl font-bold text-secondary">Hero Banners</h1>
                <p class="mt-1 text-sm text-slate-500">Manage homepage banners, display order and search metadata.</p>
            </div>
            <?php require BASE_PATH . '/resources/views/components/base/drawer.php'; ?>
        </div>

        <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
            <?php
            $inputName = 'hero_search';
            $inputId = 'hero-search';
            $inputValue = '';
            $inputType = 'search';
            $inputPlaceholder = 'Search banners...';
            $inputLabel = '';
            $inputHint = '';
            $inputError = '';
            $inputSize = 'md';
            $inputState = 'default';
            $inputRequired = false;
            $inputAutocomplete = '';
            $inputReadonly = false;
            $inputDisabled = false;
            $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
            $inputPrefix = '';
            $inputTrailingIcon = '';
            $inputSuffix = '';
            $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'hero-banners-table'];
            $inputClass = '';
            $inputWrapperClass = 'flex-1';
            require BASE_PATH . '/resources/views/components/base/input.php';

            $selectName = 'status';
            $selectId = 'hero-status-filter';
            $selectOptions = ['' => 'All statuses', 'active' => 'Active', 'inactive' => 'Inactive'];
            $selectValue = '';
            $selectPlaceholder = '';
            $selectLabel = '';
            $selectHint = '';
            $selectError = '';
            $selectSize = 'md';
            $selectState = 'default';
            $selectMultiple = false;
            $selectRequired = false;
            $selectDisabled = false;
            $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'hero-banners-table', 'data-filter-key' => 'status'];
            $selectClass = '';
            require BASE_PATH . '/resources/views/components/base/select.php';
            ?>
            <button type="button" data-datatable-reset data-datatable-target="hero-banners-table" class="rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary/5 hover:text-primary">Reset</button>
        </div>

        <?php
        $tableId = 'hero-banners-table';
        $tableColumns = [
            ['key' => 'banner', 'label' => 'Banner', 'html' => true, 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false, 'width' => 'w-28'],
            ['key' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'width' => 'w-24'],
            ['key' => 'updated_at', 'label' => 'Updated', 'type' => 'date', 'width' => 'w-40'],
            ['key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right', 'width' => 'w-32'],
        ];
        $tableSortable = true;
        $tableDefaultSort = ['key' => 'sort_order', 'dir' => 'asc'];
        $tablePaginated = true;
        $tablePerPage = 2;
        $tableZebra = false;
        $tableEmbedded = false;
        require BASE_PATH . '/resources/views/components/base/datatable.php';
        ?>
    </div>
</div>

<?php if ($editSlide): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var trigger = document.querySelector('[data-drawer-open="hero-banner-drawer"]');
    if (trigger) trigger.click();
});
</script>
<?php endif; ?>
