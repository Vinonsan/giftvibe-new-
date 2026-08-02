<?php
$categories = $categories ?? [];
$editCategory = $editCategory ?? null;
$fieldClass = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/10';
$imageValue = (string) ($editCategory['image_path'] ?? $editCategory['image'] ?? '');
$orderValue = (int) ($editCategory['sort_order'] ?? $editCategory['display_order'] ?? count($categories) + 1);
$isActive = isset($editCategory['is_active']) ? (bool) $editCategory['is_active'] : (($editCategory['status'] ?? 'active') === 'active');
$tableRows = [];
foreach ($categories as $category) {
    $id = (int) $category['id'];
    $name = htmlspecialchars((string) ($category['name'] ?? $category['title'] ?? 'Category'), ENT_QUOTES);
    $description = htmlspecialchars((string) ($category['description'] ?? 'No description added.'), ENT_QUOTES);
    $image = htmlspecialchars((string) ($category['image_path'] ?? $category['image'] ?? '/assets/images/hero_slide_1.jpg'), ENT_QUOTES);
    $active = isset($category['is_active']) ? (bool) $category['is_active'] : (($category['status'] ?? 'active') === 'active');
    $status = $active
        ? '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>'
        : '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">Inactive</span>';
    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<a href="/admin/categories?edit=' . $id . '" title="View category" aria-label="View category" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
        . '<form method="post" action="/admin/categories" onsubmit="return confirm(\'Delete this category?\')">'
        . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES) . '"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" title="Delete category" aria-label="Delete category" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50 hover:text-rose-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79M9 5.25V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5v.75M3.75 5.79h16.5"/></svg></button></form></div>';
    $tableRows[] = [
        'category' => '<div class="flex min-w-64 items-center gap-3"><img src="' . $image . '" alt="" class="h-14 w-16 rounded-lg bg-slate-100 object-cover"><div class="min-w-0"><p class="truncate font-semibold text-secondary">' . $name . '</p><p class="mt-1 max-w-sm truncate text-xs text-slate-500">' . $description . '</p></div></div>',
        'status' => $status,
        'sort_order' => (int) ($category['sort_order'] ?? $category['display_order'] ?? 0),
        'updated_at' => (string) ($category['updated_at'] ?? ''),
        '_actions' => $actions,
    ];
}
?>
<div class="space-y-5">
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div><h1 class="text-xl font-bold text-secondary">Categories</h1><p class="mt-1 text-sm text-slate-500">Manage the collections shown on the public homepage.</p></div>
        <button type="button" data-drawer-open="category-drawer" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary"><span class="text-lg leading-none">+</span> Add category</button>
    </div>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center">
        <label class="relative block w-full flex-1"><span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg></span><input type="search" data-datatable-search data-datatable-target="categories-table" placeholder="Search categories..." class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-secondary shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"></label>
        <select data-datatable-filter data-datatable-target="categories-table" data-filter-key="status" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-secondary shadow-sm outline-none focus:border-primary"><option value="">All statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select>
        <button type="button" data-datatable-reset data-datatable-target="categories-table" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100">Reset</button>
    </div>

    <?php
    $tableId = 'categories-table';
    $tableColumns = [
        ['key' => 'category', 'label' => 'Category', 'html' => true, 'sortable' => false],
        ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false, 'width' => 'w-28'],
        ['key' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'width' => 'w-24'],
        ['key' => 'updated_at', 'label' => 'Updated', 'type' => 'date', 'width' => 'w-40'],
        ['key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right', 'width' => 'w-32'],
    ];
    $tableSortable = true; $tableDefaultSort = ['key' => 'sort_order', 'dir' => 'asc'];
    $tablePaginated = true; $tablePerPage = 5; $tableZebra = false; $tableEmbedded = false;
    $tableEmptyMessage = 'No categories found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>

    <div class="flex flex-col gap-4 px-1 py-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3"><label for="category-rows" class="text-sm text-slate-500">Rows per page</label><select id="category-rows" data-table-perpage data-datatable-target="categories-table" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-secondary outline-none"><option value="5" selected>5</option><option value="10">10</option><option value="25">25</option></select></div>
        <p data-table-summary data-datatable-target="categories-table" class="text-sm font-medium text-slate-500"></p>
        <div data-table-pagination-wrap data-datatable-target="categories-table"><nav data-table-pagination data-datatable-target="categories-table" class="flex flex-wrap items-center justify-end gap-1.5" aria-label="Category pagination"></nav></div>
    </div>
</div>

<?php ob_start(); ?>
<form id="category-form" method="post" action="/admin/categories" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="action" value="save">
    <?php if ($editCategory): ?><input type="hidden" name="id" value="<?= (int) $editCategory['id'] ?>"><?php endif; ?>
    <label class="block"><span class="mb-1.5 block text-xs font-semibold text-slate-600">Category name <span class="text-rose-500">*</span></span><input name="name" required maxlength="150" value="<?= htmlspecialchars((string) ($editCategory['name'] ?? $editCategory['title'] ?? '')) ?>" placeholder="Birthday gifts" class="<?= $fieldClass ?>"></label>
    <label class="block"><span class="mb-1.5 block text-xs font-semibold text-slate-600">Category link <span class="text-rose-500">*</span></span><input name="link_url" required maxlength="255" value="<?= htmlspecialchars((string) ($editCategory['link_url'] ?? '')) ?>" placeholder="/shop?category=birthday-gifts" class="<?= $fieldClass ?>"><span class="mt-1.5 block text-xs text-slate-400">Card click செய்தால் open ஆக வேண்டிய URL.</span></label>
    <?php
    $fileName = 'category_image'; $fileId = 'category-image'; $fileLabel = 'Category image';
    $fileHint = 'JPG, PNG or WebP · maximum 5MB · recommended 800 × 900px';
    $fileAccept = 'image/png,image/jpeg,image/webp'; $fileRequired = $editCategory === null; $fileCurrentUrl = $imageValue;
    require BASE_PATH . '/resources/views/components/base/file-input.php';
    ?>
</form>
<?php
$drawerBody = (string) ob_get_clean();
$drawerFooter = '<button type="button" data-drawer-close class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary">Cancel</button><button type="submit" form="category-form" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white">' . ($editCategory ? 'Update category' : 'Add category') . '</button>';
$drawerId = 'category-drawer'; $drawerSide = 'right'; $drawerSize = 'lg';
$drawerTitle = $editCategory ? 'View & edit category' : 'Add category'; $drawerDescription = $editCategory ? 'Review the category details and update them when needed.' : 'This content will appear on the public homepage.';
$drawerTrigger = ''; $drawerStatic = false; $drawerCloseOnEsc = true; $drawerShowCloseButton = true; $drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if ($editCategory): ?><script>document.addEventListener('DOMContentLoaded',function(){document.querySelector('[data-drawer-open="category-drawer"]')?.click();});</script><?php endif; ?>
