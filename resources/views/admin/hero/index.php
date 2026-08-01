<?php

declare(strict_types=1);

$slides = $slides ?? [];
$editSlide = $editSlide ?? null;
$parts = explode('|', (string) ($editSlide['subtitle'] ?? ''));
$background = '#0B1528';
if (preg_match('/bg-\[(#[0-9A-Fa-f]{6})\]/', $parts[4] ?? '', $matches)) {
    $background = strtoupper($matches[1]);
}

$fieldClass = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/10';
$tableRows = [];

foreach ($slides as $slide) {
    $slideParts = explode('|', (string) ($slide['subtitle'] ?? ''));
    $id = (int) $slide['id'];
    $image = htmlspecialchars((string) $slide['image_path'], ENT_QUOTES);
    $title = htmlspecialchars((string) $slide['title'], ENT_QUOTES);
    $description = htmlspecialchars((string) ($slideParts[1] ?? ''), ENT_QUOTES);
    $status = $slide['status'] === 'active'
        ? '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>'
        : '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Inactive</span>';

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<a href="/admin/hero?edit=' . $id . '" title="Edit banner" aria-label="Edit banner" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM16.862 4.487 19.5 7.125"/></svg></a>'
        . '<form method="post" action="/admin/hero" onsubmit="return confirm(\'Delete this hero banner?\')">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES) . '">'
        . '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" title="Delete banner" aria-label="Delete banner" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50 hover:text-rose-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button></form></div>';

    $tableRows[] = [
        'banner' => '<div class="flex min-w-64 items-center gap-3"><img src="' . $image . '" alt="" class="h-14 w-20 rounded-lg bg-slate-100 object-cover"><div class="min-w-0"><p class="truncate font-semibold text-secondary">' . $title . '</p><p class="mt-1 max-w-sm truncate text-xs text-slate-500">' . $description . '</p></div></div>',
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

    <label class="block">
        <span class="mb-1.5 block text-xs font-semibold text-slate-600">SEO title <span class="text-rose-500">*</span></span>
        <input name="title" required maxlength="190" value="<?= htmlspecialchars((string) ($editSlide['title'] ?? '')) ?>" placeholder="Find the perfect gift" class="<?= $fieldClass ?>">
    </label>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Eyebrow</span>
            <input name="eyebrow" maxlength="60" value="<?= htmlspecialchars((string) ($parts[0] ?? '')) ?>" placeholder="Crafted with love" class="<?= $fieldClass ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Price text</span>
            <input name="price" maxlength="60" value="<?= htmlspecialchars((string) ($parts[2] ?? '')) ?>" placeholder="From LKR 4,500" class="<?= $fieldClass ?>">
        </label>
    </div>

    <label class="block">
        <span class="mb-1.5 block text-xs font-semibold text-slate-600">SEO description</span>
        <textarea name="description" rows="3" maxlength="220" placeholder="Describe this banner clearly for customers and search engines" class="<?= $fieldClass ?>"><?= htmlspecialchars((string) ($parts[1] ?? '')) ?></textarea>
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
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Button label</span>
            <input name="button_label" maxlength="40" value="<?= htmlspecialchars((string) ($parts[3] ?? 'Shop now')) ?>" class="<?= $fieldClass ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Button link</span>
            <input name="link_url" maxlength="255" value="<?= htmlspecialchars((string) ($editSlide['link_url'] ?? '/shop')) ?>" class="<?= $fieldClass ?>">
        </label>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Background</span>
            <input type="color" name="background" value="<?= htmlspecialchars($background) ?>" class="h-11 w-full rounded-xl border border-slate-200 bg-white p-1">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Sort order</span>
            <input type="number" min="0" name="sort_order" value="<?= (int) ($editSlide['sort_order'] ?? count($slides) + 1) ?>" class="<?= $fieldClass ?>">
        </label>
    </div>

    <label class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <span><span class="block text-sm font-semibold text-secondary">Active</span><span class="mt-0.5 block text-xs text-slate-500">Show this banner on the homepage</span></span>
        <input type="checkbox" name="status" value="active" <?= !$editSlide || $editSlide['status'] === 'active' ? 'checked' : '' ?> class="h-5 w-5 accent-primary">
    </label>
</form>
<?php
$drawerBody = (string) ob_get_clean();
$drawerFooter = '<button type="button" data-drawer-close class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary transition hover:bg-slate-50">Cancel</button>'
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
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="space-y-4">
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h1 class="text-xl font-bold text-secondary">Hero Banners</h1>
                <p class="mt-1 text-sm text-slate-500">Manage homepage banners, display order and search metadata.</p>
            </div>
            <?php require BASE_PATH . '/resources/views/components/base/drawer.php'; ?>
        </div>

        <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
            <label class="relative block w-full flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                </span>
                <input type="search" data-datatable-search data-datatable-target="hero-banners-table" placeholder="Search banners..." aria-label="Search banners" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-secondary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/10">
            </label>
            <select data-datatable-filter data-datatable-target="hero-banners-table" data-filter-key="status" aria-label="Filter by status" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-secondary shadow-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="button" data-datatable-reset data-datatable-target="hero-banners-table" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-secondary">Reset</button>
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
        $tableEmptyMessage = 'No hero banners found.';
        require BASE_PATH . '/resources/views/components/base/datatable.php';
        ?>

        <div class="flex flex-col gap-4 px-1 py-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <label for="hero-rows-per-page" class="text-sm text-slate-500">Rows per page</label>
                <select id="hero-rows-per-page" data-table-perpage data-datatable-target="hero-banners-table" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-secondary outline-none focus:border-primary">
                    <option value="2" selected>2</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
            </div>
            <p data-table-summary data-datatable-target="hero-banners-table" class="text-sm font-medium text-slate-500"></p>
            <div data-table-pagination-wrap data-datatable-target="hero-banners-table">
                <nav data-table-pagination data-datatable-target="hero-banners-table" class="flex flex-wrap items-center justify-end gap-1.5" aria-label="Hero banner pagination"></nav>
            </div>
        </div>
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
