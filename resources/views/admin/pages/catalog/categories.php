<?php
$categories = $categories ?? [];
$searchValue = trim((string) ($_GET['q'] ?? ''));
$requestedStatus = (string) ($_GET['status'] ?? 'all');
$statusValue = in_array($requestedStatus, ['all', 'active', 'inactive'], true) ? $requestedStatus : 'all';
$saved = $_GET['saved'] ?? '';
$totalCategories = count($categories);
$activeCategories = count(array_filter($categories, static fn (array $category): bool => ($category['status'] ?? 'active') === 'active'));
$inactiveCategories = count(array_filter($categories, static fn (array $category): bool => ($category['status'] ?? 'active') === 'inactive'));

$categoryImageUrl = static function (?string $path): string {
    if (!$path) {
        return asset('/public/assets/images/categories/category-fallback.svg');
    }

    return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
        ? $path
        : asset('/' . ltrim($path, '/'));
};

$categoryJson = static function (array $category): string {
    $payload = [
        'id' => (int) $category['id'],
        'name' => (string) $category['name'],
        'slug' => (string) ($category['slug'] ?? ''),
        'description' => (string) ($category['description'] ?? ''),
        'image_path' => (string) ($category['image_path'] ?? ''),
        'status' => (string) ($category['status'] ?? 'active'),
        'sort_order' => (int) ($category['sort_order'] ?? 0),
        'products_count' => (int) ($category['products_count'] ?? 0),
        'created_at' => (string) ($category['created_at'] ?? ''),
        'updated_at' => (string) ($category['updated_at'] ?? ''),
    ];

    return htmlspecialchars(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
};

$formatDate = static fn (?string $date): string => $date ? date('M d, Y h:i A', strtotime($date)) : 'Not available';
?>
<div class="min-h-[calc(100vh-8rem)] space-y-6" data-category-page data-base-url="<?= e(BASE_URL) ?>">
    <?php if (in_array($saved, ['created', 'updated', 'deleted'], true)): ?>
        <div class="rounded-card border border-success-200 bg-white px-4 py-3 text-sm font-semibold text-success-700 shadow-card" role="status">
            Category <?= $saved === 'created' ? 'created' : ($saved === 'deleted' ? 'deleted' : 'updated') ?> successfully.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="rounded-card border border-danger-200 bg-white px-4 py-3 text-sm font-semibold text-danger-700 shadow-card" role="alert">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="flex flex-col gap-4 rounded-card border border-slate-200 bg-white p-5 shadow-card sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-primary-900">Categories</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Manage your product categories</p>
        </div>
        <button
            type="button"
            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-900 focus:ring-offset-2"
            data-category-add
            aria-label="Add category"
        >
            <span class="text-xl font-light leading-none">+</span>
            Add category
        </button>
    </div>



    <!-- Filters -->
    <section aria-label="Category search and filters">
        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem_12rem_auto_auto] lg:items-center">
            <label class="min-w-0">
                <span class="sr-only">Search categories by name</span>
                <span class="relative block">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <?php component('admin/components/common/icon', ['name' => 'search', 'size' => 'sm']); ?>
                    </span>
                    <input
                        type="search"
                        value="<?= e($searchValue) ?>"
                        class="min-h-11 w-full rounded-button border border-slate-200 bg-white pl-11 pr-4 text-sm font-semibold text-primary-900 outline-none transition duration-200 placeholder:text-slate-400 focus:border-primary-900 focus:ring-2 focus:ring-primary-100"
                        placeholder="Search by category name..."
                        data-category-search
                        aria-label="Search categories by name"
                    >
                </span>
            </label>
            <?php component('admin/components/common/custom-select', [
                'name' => 'status',
                'placeholder' => 'All status',
                'selected' => $statusValue,
                'aria_label' => 'Filter categories by status',
                'class' => 'min-w-[12rem]',
                'data_attrs' => 'data-category-filter',
                'options' => [
                    ['value' => 'all', 'label' => 'All status'],
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                ],
            ]); ?>
            <?php component('admin/components/common/custom-select', [
                'name' => 'type',
                'placeholder' => 'All',
                'selected' => 'all',
                'aria_label' => 'Filter category type',
                'class' => 'min-w-[12rem]',
                'data_attrs' => '',
                'options' => [
                    ['value' => 'all', 'label' => 'All'],
                ],
            ]); ?>
            <button type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-900 focus:ring-offset-2">
                <?php component('admin/components/common/icon', ['name' => 'search', 'size' => 'sm']); ?>
                Filter
            </button>
            <button
                type="button"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-button px-4 text-sm font-bold text-blue-600 transition duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-blue-200"
                data-category-reset
            >
                Clear
            </button>
        </div>
    </section>

    <!-- Category table -->
    <section class="<?= empty($categories) ? 'hidden' : '' ?> overflow-hidden rounded-card border border-slate-200 bg-white shadow-card" aria-live="polite">
        <span class="sr-only">Catalog map</span>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-white">
                    <tr class="text-sm font-extrabold text-primary-900">
                        <th scope="col" class="px-5 py-4">Image</th>
                        <th scope="col" class="px-5 py-4">Category Name</th>
                        <th scope="col" class="px-5 py-4">Description</th>
                        <th scope="col" class="px-5 py-4">Products</th>
                        <th scope="col" class="px-5 py-4">Status</th>
                        <th scope="col" class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" data-category-grid>
                    <?php foreach ($categories as $index => $category): ?>
                        <?php
                            $status = (string) ($category['status'] ?? 'active');
                            $isActive = $status === 'active';
                            $productsCount = (int) ($category['products_count'] ?? 0);
                            $description = trim((string) ($category['description'] ?? ''));
                            $imageUrl = $categoryImageUrl($category['image_path'] ?? null);
                        ?>
                        <tr
                            class="transition duration-200 hover:bg-white"
                            data-category-card
                            data-category-name="<?= e(strtolower((string) $category['name'])) ?>"
                            data-category-status="<?= e($status) ?>"
                            data-category='<?= $categoryJson($category) ?>'
                        >
                            <td class="px-5 py-3">
                                <img src="<?= e($imageUrl) ?>" alt="<?= e($category['name']) ?>" class="h-14 w-14 rounded-button object-cover" loading="lazy">
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-extrabold text-primary-900"><?= e($category['name']) ?></span>
                            </td>
                            <td class="max-w-md px-5 py-3">
                                <p class="line-clamp-2 text-sm font-medium leading-6 text-primary-900">
                                    <?= e($description !== '' ? $description : 'GiftVibe storefront collection.') ?>
                                </p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-button border border-blue-200 bg-white px-3 py-1 text-sm font-bold text-blue-600">
                                    <?= $productsCount ?> product<?= $productsCount === 1 ? '' : 's' ?>
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-button border px-3 py-1 text-sm font-bold <?= $isActive ? 'border-green-200 bg-white text-green-700' : 'border-red-200 bg-white text-red-700' ?>">
                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-primary-900 transition duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                                        data-category-view
                                        aria-label="View category"
                                        title="View category"
                                    >
                                        <?php component('admin/components/common/icon', ['name' => 'eye', 'size' => 'sm']); ?>
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-primary-900 transition duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                                        data-category-edit
                                        aria-label="Edit category"
                                        title="Edit category"
                                    >
                                        <?php component('admin/components/common/icon', ['name' => 'edit', 'size' => 'sm']); ?>
                                    </button>
                                    <button type="button" class="inline-flex h-10 w-8 items-center justify-center rounded-button text-primary-900 transition duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100" aria-label="More actions" title="More actions">
                                        <span class="text-lg font-extrabold leading-none">...</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 text-sm font-semibold text-primary-900 sm:flex-row sm:items-center sm:justify-between">
            <span>Showing <?= $totalCategories > 0 ? '1' : '0' ?> to <?= $totalCategories ?> of <?= $totalCategories ?> categories</span>
            <div class="flex items-center gap-3">
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-400" aria-label="Previous page">
                    <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm', 'class' => 'rotate-180']); ?>
                </button>
                <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-button bg-primary-900 px-3 text-sm font-extrabold text-white">1</span>
                <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-400" aria-label="Next page">
                    <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- Empty states -->
    <section class="<?= empty($categories) ? '' : 'hidden' ?> rounded-card border border-dashed border-slate-200 bg-white p-10 text-center shadow-card" data-category-empty-all>
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-card border border-blue-200 bg-white text-blue-600">
            <?php component('admin/components/common/icon', ['name' => 'archive-box', 'size' => 'lg']); ?>
        </div>
        <h2 class="mt-5 text-lg font-extrabold text-primary-900">No categories yet</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">Create your first collection to organize the GiftVibe catalog.</p>
        <button
            type="button"
            class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-900"
            data-category-add
        >
            <span class="text-xl font-light leading-none">+</span>
            Add first category
        </button>
    </section>

    <section class="hidden rounded-card border border-slate-200 bg-white p-10 text-center shadow-card" data-category-empty-filter>
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-card border border-slate-200 bg-white text-slate-500">
            <?php component('admin/components/common/icon', ['name' => 'search', 'size' => 'lg']); ?>
        </div>
        <h2 class="mt-5 text-lg font-extrabold text-primary-900">No matching categories</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">Try a different search term or reset the status filter.</p>
        <button
            type="button"
            class="mt-6 inline-flex min-h-10 items-center justify-center rounded-button border border-slate-200 bg-white px-4 text-sm font-bold text-primary-900 transition duration-200 hover:-translate-y-0.5 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
            data-category-reset
        >
            Clear search
        </button>
    </section>

    <!-- Drawer overlay -->
    <div class="fixed inset-0 z-[9998] hidden bg-primary-950/50 opacity-0 backdrop-blur-sm transition-opacity duration-200" data-category-drawer-overlay></div>

    <!-- Add / Edit / View drawer -->
    <aside
        class="fixed inset-y-0 right-0 z-[9999] flex h-screen h-dvh max-h-screen max-h-dvh w-full translate-x-full flex-col border-l border-primary-900/10 bg-white shadow-[0_24px_80px_-30px_rgba(12,43,78,0.45)] transition-transform duration-300 ease-out sm:max-w-[38rem]"
        style="height: 100vh; height: 100dvh; max-height: 100vh; max-height: 100dvh;"
        data-category-drawer
        aria-hidden="true"
        aria-labelledby="category-drawer-title"
    >
        <div class="border-b border-primary-900/10 bg-white px-5 py-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Collection</p>
                    <h2 id="category-drawer-title" class="mt-1 text-xl font-extrabold tracking-tight text-primary-900">Add Category</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-blue-200 bg-white text-blue-600 shadow-card transition hover:bg-white" data-category-view-edit title="Edit category" aria-label="Edit category">
                        <?php component('admin/components/common/icon', ['name' => 'edit', 'size' => 'sm']); ?>
                    </button>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-red-200 bg-white text-red-600 shadow-card transition hover:bg-white" data-category-view-delete title="Delete category" aria-label="Delete category">
                        <?php component('admin/components/common/icon', ['name' => 'trash', 'size' => 'sm']); ?>
                    </button>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-500 shadow-card transition duration-200 hover:bg-white hover:text-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-100"
                        data-category-drawer-close
                        aria-label="Close category drawer"
                    >
                        <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto bg-white px-5 py-6">
            <div class="hidden space-y-5" data-category-view-panel>
                <!-- Category Image -->
                <div class="flex justify-center">
                    <img src="" alt="" class="rounded-button object-contain shadow-card bg-white" data-category-view-image style="max-width: 100%; max-height: 22rem; width: auto; height: auto;">
                </div>
                <div class="rounded-card border border-primary-900/10 bg-white p-5 shadow-card">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400" data-category-view-products>0 products</p>
                            <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-primary-900" data-category-view-name></h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-button border px-2.5 py-1 text-xs font-bold" data-category-view-status></span>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600" data-category-view-description></p>
                </div>
                <dl class="grid gap-3 rounded-card border border-primary-900/10 bg-white p-5 text-sm shadow-card">
                    <div class="flex justify-between gap-4 border-b border-primary-900/5 pb-3">
                        <dt class="font-semibold text-slate-500">Slug</dt>
                        <dd class="text-right font-bold text-primary-900" data-category-view-slug></dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-primary-900/5 pb-3">
                        <dt class="font-semibold text-slate-500">Created</dt>
                        <dd class="text-right text-slate-700" data-category-view-created></dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-semibold text-slate-500">Updated</dt>
                        <dd class="text-right text-slate-700" data-category-view-updated></dd>
                    </div>
                </dl>
            </div>

            <form method="POST" action="<?= e(url('/admin/categories')) ?>" enctype="multipart/form-data" class="space-y-5" data-category-form novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="image_path" value="" data-category-image-path>
                <input type="hidden" name="sort_order" value="0" data-category-sort-input>

                <div class="space-y-5">
                    <!-- Image upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                            Category image
                        </label>
                        <div class="flex items-center gap-4">
                            <img src="" alt="" class="h-20 w-20 rounded-button border border-slate-200 object-cover shadow-card" data-category-image-preview>
                            <label class="cursor-pointer rounded-button border border-blue-200 bg-white px-4 py-2 text-sm font-bold text-blue-600 transition hover:bg-white">
                                Choose File
                                <input type="file" name="image" accept="image/*" class="hidden" data-category-image-input>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="category-name" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                            Category name <span class="text-danger-600">*</span>
                        </label>
                        <input
                            id="category-name"
                            name="name"
                            required
                            class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-category-name-input
                            placeholder="e.g. Birthday Gifts"
                        >
                        <p class="hidden text-xs font-semibold text-danger-600" data-category-error-for="name">Category name is required.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="category-description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                            Category description
                        </label>
                        <textarea
                            id="category-description"
                            name="description"
                            rows="5"
                            class="w-full rounded-button border border-primary-900/10 bg-white px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-category-description-input
                            placeholder="Short description for this collection"
                        ></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Status</label>
                        <?php component('admin/components/common/custom-select', [
                            'name' => 'status',
                            'selected' => 'active',
                            'placeholder' => 'Select status',
                            'aria_label' => 'Category status',
                            'data_attrs' => 'data-category-status-input',
                            'options' => [
                                ['value' => 'active', 'label' => 'Active'],
                                ['value' => 'inactive', 'label' => 'Inactive'],
                            ],
                        ]); ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-end gap-3 border-t border-primary-900/10 bg-white px-5 py-4 shadow-[0_-18px_54px_-44px_rgba(12,43,78,0.35)]">
            <div class="flex justify-end" data-category-form-actions>
                <button
                    type="button"
                    class="inline-flex min-h-11 min-w-28 items-center justify-center rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    data-category-save
                >
                    <span data-category-save-label>Save</span>
                </button>
            </div>
        </div>
    </aside>
</div>

<script>
window.GiftvibeCategoryDates = {
<?php foreach ($categories as $category): ?>
    "<?= (int) $category['id'] ?>": {
        created: "<?= e($formatDate($category['created_at'] ?? null)) ?>",
        updated: "<?= e($formatDate($category['updated_at'] ?? null)) ?>"
    },
<?php endforeach; ?>
};
</script>
