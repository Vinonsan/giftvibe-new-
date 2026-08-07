<?php
declare(strict_types=1);
$fieldClass = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10';
$tableRows = [];

foreach ($products as $item) {
    $id = (int)$item['id'];
    $img = (string)($item['image_path'] ?? '/assets/images/hero_slide_1.jpg');
    if (str_starts_with($img, 'public/')) {
        $img = '/' . substr($img, 7);
    }
    $active = $item['status'] === 'active';
    
    // Actions with dynamic delete modal hook
    $actions = '<div class="flex items-center justify-end gap-2">' .
        '<a href="/admin/products?edit=' . $id . '" title="View product" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-primary">' .
            '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">' .
                '<path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/>' .
                '<circle cx="12" cy="12" r="2.25"/>' .
            '</svg>' .
        '</a>' .
        '<button type="button" data-delete-trigger data-id="' . $id . '" data-name="' . htmlspecialchars($item['name'], ENT_QUOTES) . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50 cursor-pointer" title="Delete">&times;</button>' .
    '</div>';
    
    // Removed min-w-64 to fix table text alignment and spacing issues
    $tableRows[] = [
        'product' => '<div class="flex items-center gap-3"><img src="' . htmlspecialchars($img, ENT_QUOTES) . '" class="h-12 w-14 rounded-lg object-cover shrink-0"><div><p class="font-semibold text-secondary leading-snug">' . htmlspecialchars($item['name']) . '</p><p class="text-[11px] text-slate-500 mt-0.5">' . htmlspecialchars($item['sku']) . ' · ' . htmlspecialchars((string)$item['category_names']) . '</p></div></div>',
        'price' => '<span class="font-semibold text-secondary">LKR ' . number_format((float)$item['base_price'], 2) . '</span>',
        'stock' => '<span class="font-semibold text-secondary">' . (int)$item['stock_quantity'] . '</span>',
        'status' => $active ? '<span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>' : '<span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Draft</span>',
        '_actions' => $actions
    ];
}

$editImage = (string)($editProduct['image_path'] ?? '');
if (str_starts_with($editImage, 'public/')) {
    $editImage = '/' . substr($editImage, 7);
}
?>
<div class="space-y-5">
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Products</h1>
            <p class="mt-1 text-sm text-slate-500">Manage products and assign them to categories.</p>
        </div>
        <button type="button" data-drawer-open="product-drawer" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white cursor-pointer">+ Add product</button>
    </div>
    
    <div class="flex gap-3">
        <input type="search" data-datatable-search data-datatable-target="products-table" placeholder="Search products..." class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none">
        <button data-datatable-reset data-datatable-target="products-table" class="rounded-xl border border-slate-200 px-4 text-sm font-semibold cursor-pointer">Reset</button>
    </div>
    
    <?php 
    $tableId = 'products-table';
    $tableColumns = [
        ['key' => 'product', 'label' => 'Product', 'html' => true, 'sortable' => false],
        ['key' => 'price', 'label' => 'Price'],
        ['key' => 'stock', 'label' => 'Stock', 'type' => 'number'],
        ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false],
        ['key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right']
    ];
    $tableSortable = true;
    $tableDefaultSort = null;
    $tablePaginated = true;
    $tablePerPage = 10;
    $tableZebra = false;
    $tableEmptyMessage = 'No products found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>
</div>

<?php ob_start(); ?>
<form id="product-form" method="post" action="/admin/products" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?= (int)$editProduct['id'] ?>">
    <?php endif; ?>
    
    <div class="grid gap-4 sm:grid-cols-2">
        <label><span class="mb-1.5 block text-xs font-semibold">Product name *</span><input required name="name" value="<?= htmlspecialchars((string)($editProduct['name'] ?? '')) ?>" class="<?= $fieldClass ?>"></label>
        <label><span class="mb-1.5 block text-xs font-semibold">SKU *</span><input required name="sku" value="<?= htmlspecialchars((string)($editProduct['sku'] ?? '')) ?>" class="<?= $fieldClass ?>"></label>
    </div>
    
    <label class="block"><span class="mb-1.5 block text-xs font-semibold">Short description</span><textarea name="short_description" rows="2" class="<?= $fieldClass ?>"><?= htmlspecialchars((string)($editProduct['short_description'] ?? '')) ?></textarea></label>
    <label class="block"><span class="mb-1.5 block text-xs font-semibold">Full description</span><textarea name="description" rows="5" class="<?= $fieldClass ?>" placeholder="Product contents and detailed gifting information"><?= htmlspecialchars((string)($editProduct['description'] ?? '')) ?></textarea></label>
    
    <!-- Interactive Tag Selector replacing long SEO/GEO block -->
    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4 space-y-2">
        <span class="block text-xs font-bold text-primary uppercase tracking-wider">Search Keywords & Hashtags</span>
        <div id="keywords-tags-wrapper" class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-2.5 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10 transition">
            <div id="keywords-tags-container" class="flex flex-wrap items-center gap-1.5"></div>
            <input type="text" id="keywords-tag-input" placeholder="Type hashtag and press Enter" class="flex-1 bg-transparent text-sm text-secondary outline-none min-w-[120px] py-0.5">
        </div>
        <input type="hidden" name="search_keywords" id="search-keywords-hidden" value="<?= htmlspecialchars((string)($editProduct['search_keywords'] ?? '')) ?>">
        <span class="block text-[11px] text-slate-400">Type a keyword (e.g. birthday or flower) and press Enter or Comma. Stored dynamically for SEO discovery.</span>
    </div>
    
    <?php 
    $selectName = 'category_ids[]';
    $selectId = 'product-categories';
    $selectOptions = [];
    foreach ($categories as $category) {
        $selectOptions[(string)$category['id']] = $category['name'];
    }
    $selectValue = array_map('strval', $editCategoryIds);
    $selectPlaceholder = 'Select product categories';
    $selectLabel = 'Categories';
    $selectHint = 'Select one or more categories for this product.';
    $selectError = '';
    $selectSize = 'lg';
    $selectState = 'default';
    $selectMultiple = true;
    $selectRequired = true;
    $selectDisabled = false;
    $selectAttributes = [];
    $selectClass = '';
    require BASE_PATH . '/resources/views/components/base/select.php';
    ?>
    
    <label class="block"><span class="mb-1.5 block text-xs font-semibold">Image Alt Text (SEO)</span><input name="image_alt_text" value="<?= htmlspecialchars((string)($editProduct['alt_text'] ?? '')) ?>" placeholder="e.g. Handmade Flower Bouquet" class="<?= $fieldClass ?>"></label>
    
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold">YouTube / video reference links</span>
            <button type="button" data-add-video class="rounded-lg border border-primary/10 px-3 py-1.5 text-xs font-bold text-primary hover:bg-primary/5 cursor-pointer">+ Add link</button>
        </div>
        <div data-video-list class="space-y-2">
            <?php foreach (($productVideos ?: ['']) as $video): ?>
                <div data-video-row class="flex gap-2">
                    <input type="url" name="video_urls[]" value="<?= htmlspecialchars((string)$video) ?>" placeholder="https://www.youtube.com/watch?v=..." class="<?= $fieldClass ?>">
                    <button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-600 hover:bg-rose-50 cursor-pointer" aria-label="Remove video link">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php 
    $fileName = 'product_images[]';
    $fileId = 'product-images';
    $fileLabel = 'Product images';
    $fileHint = 'Select one or more JPG, PNG or WebP images, each maximum 5MB';
    $fileAccept = 'image/png,image/jpeg,image/webp';
    $fileRequired = $editProduct === null;
    $fileCurrentUrl = '';
    $fileMultiple = true;
    require BASE_PATH . '/resources/views/components/base/file-input.php';
    ?>
    
    <?php if (!empty($secondaryImages)): ?>
        <div class="rounded-xl border border-slate-200 p-4">
            <span class="mb-3 block text-xs font-semibold text-secondary">Saved product images</span>
            <div class="grid grid-cols-3 gap-3">
                <?php foreach ($secondaryImages as $secImg): 
                    $imgPath = (string)$secImg['image_path'];
                    if (str_starts_with($imgPath, 'public/')) {
                        $imgPath = '/' . substr($imgPath, 7);
                    }
                ?>
                    <div class="relative group rounded-lg overflow-hidden border border-slate-100 aspect-square">
                        <img src="<?= htmlspecialchars($imgPath) ?>" class="h-full w-full object-cover">
                        <label class="absolute inset-0 bg-slate-900/65 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white cursor-pointer transition-opacity">
                            <input type="checkbox" name="delete_images[]" value="<?= (int)$secImg['id'] ?>" class="mr-1.5 accent-rose-600"> Remove
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2">
        <label><span class="mb-1.5 block text-xs font-semibold">Selling price *</span><input id="selling-price" required type="number" min="0" step="0.01" name="selling_price" value="<?= htmlspecialchars((string)($editProduct['base_price'] ?? '')) ?>" class="<?= $fieldClass ?>"></label>
        <label><span class="mb-1.5 block text-xs font-semibold">Buying price *</span><input id="buying-price" required type="number" min="0" step="0.01" name="buying_price" value="<?= htmlspecialchars((string)($editProduct['cost_price'] ?? '')) ?>" class="<?= $fieldClass ?>"></label>
        <label><span class="mb-1.5 block text-xs font-semibold">Profit</span><input id="product-profit" readonly value="0.00" class="<?= $fieldClass ?> bg-emerald-50 font-bold text-emerald-700"></label>
        <label><span class="mb-1.5 block text-xs font-semibold">Stock</span><input type="number" min="0" name="stock_quantity" value="<?= (int)($editProduct['stock_quantity'] ?? 0) ?>" class="<?= $fieldClass ?>"></label>
    </div>
    
    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex items-center justify-between rounded-xl border p-3 text-sm font-semibold">Featured<input type="checkbox" name="is_featured" value="1" <?= !empty($editProduct['is_featured']) ? 'checked' : '' ?> class="accent-primary"></label>
        <label class="flex items-center justify-between rounded-xl border p-3 text-sm font-semibold">Active<input type="checkbox" name="status" value="active" <?= !$editProduct || $editProduct['status'] === 'active' ? 'checked' : '' ?> class="accent-primary"></label>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dynamic Profit Calculation
    var selling = document.getElementById('selling-price'),
        buying = document.getElementById('buying-price'),
        profit = document.getElementById('product-profit');
        
    function calculateProfit() {
        profit.value = (Math.max(0, parseFloat(selling.value) || 0) - Math.max(0, parseFloat(buying.value) || 0)).toFixed(2);
    }
    selling?.addEventListener('input', calculateProfit);
    buying?.addEventListener('input', calculateProfit);
    calculateProfit();

    // YouTube Reference list wiring
    var list = document.querySelector('[data-video-list]');
    function bindRemove(button) {
        button.addEventListener('click', function () {
            var rows = list.querySelectorAll('[data-video-row]');
            if (rows.length > 1) {
                button.closest('[data-video-row]').remove();
            } else {
                button.closest('[data-video-row]').querySelector('input').value = '';
            }
        });
    }
    list?.querySelectorAll('[data-remove-video]').forEach(bindRemove);
    
    document.querySelector('[data-add-video]')?.addEventListener('click', function () {
        var row = document.createElement('div');
        row.dataset.videoRow = '';
        row.className = 'flex gap-2';
        row.innerHTML = '<input type="url" name="video_urls[]" placeholder="https://www.youtube.com/watch?v=..." class="<?= $fieldClass ?>">' +
            '<button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-600 hover:bg-rose-50" aria-label="Remove video link">&times;</button>';
        list.appendChild(row);
        bindRemove(row.querySelector('[data-remove-video]'));
    });

    // Tag Editor logic for Keywords
    var hiddenInput = document.getElementById('search-keywords-hidden');
    var tagsContainer = document.getElementById('keywords-tags-container');
    var tagInput = document.getElementById('keywords-tag-input');
    
    var tags = [];
    if (hiddenInput && hiddenInput.value.trim() !== '') {
        tags = hiddenInput.value.split(',').map(function(t) { return t.trim(); }).filter(Boolean);
    }
    
    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach(function (tag, index) {
            var badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1 rounded-lg bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary';
            var displayTag = tag.startsWith('#') ? tag : '#' + tag;
            badge.innerHTML = '<span>' + htmlEntities(displayTag) + '</span>' +
                '<button type="button" class="text-primary hover:text-secondary focus:outline-none font-bold text-xs cursor-pointer ml-1" data-tag-index="' + index + '">&times;</button>';
            tagsContainer.appendChild(badge);
        });
        hiddenInput.value = tags.join(', ');
    }
    
    function htmlEntities(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    
    tagsContainer.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-tag-index]');
        if (btn) {
            var index = parseInt(btn.getAttribute('data-tag-index'), 10);
            tags.splice(index, 1);
            renderTags();
        }
    });
    
    tagInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            var val = tagInput.value.trim();
            if (val !== '') {
                var cleanVal = val.replace(/^#+/, '');
                if (cleanVal !== '' && !tags.includes(cleanVal)) {
                    tags.push(cleanVal);
                    renderTags();
                }
            }
            tagInput.value = '';
        }
    });
    
    renderTags();
});
</script>
<?php 
$drawerBody = (string)ob_get_clean();
$drawerFooter = '<button type="button" data-drawer-close class="rounded-xl border px-4 py-2.5 text-sm font-semibold cursor-pointer">Cancel</button>' .
    '<button type="submit" form="product-form" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white cursor-pointer">' . ($editProduct ? 'Update product' : 'Add product') . '</button>';
$drawerId = 'product-drawer';
$drawerSide = 'right';
$drawerSize = 'lg';
$drawerTitle = $editProduct ? 'View & edit product' : 'Add product';
$drawerDescription = 'Product details, category, image and pricing.';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>

<!-- Global Confirm Delete Modal wiring -->
<form id="global-delete-form" method="post" action="/admin/products">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="delete-target-id" value="0">
</form>

<?php 
$modalId = 'global-delete-modal';
$modalTitle = 'Delete Product';
$modalDescription = 'Confirm permanently deleting this product listing.';
$modalBody = '<p class="text-sm text-slate-500">Are you sure you want to delete <strong id="delete-target-name" class="text-secondary"></strong>? This action cannot be undone and will immediately unlist it from the catalog.</p>';
$modalFooter = '<button type="button" data-modal-close class="rounded-xl border px-4 py-2.5 text-sm font-semibold cursor-pointer">Cancel</button>' .
    '<button type="submit" form="global-delete-form" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white cursor-pointer">Delete Listing</button>';
$modalSize = 'sm';
$modalStatic = false;
$modalCloseOnEsc = true;
$modalScrollable = false;
$modalShowCloseButton = true;
$modalTrigger = '<span class="hidden" aria-hidden="true"></span>';
require BASE_PATH . '/resources/views/components/base/modal.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete triggers click binding
    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('[data-delete-trigger]');
        if (trigger) {
            var id = trigger.getAttribute('data-id');
            var name = trigger.getAttribute('data-name');
            
            document.getElementById('delete-target-id').value = id;
            document.getElementById('delete-target-name').textContent = name;
            
            var modal = document.getElementById('global-delete-modal');
            if (modal && window.GiftVibeUI && window.GiftVibeUI.modal) {
                // Trigger modal opening
                var opener = document.createElement('button');
                opener.setAttribute('data-modal-open', 'global-delete-modal');
                document.body.appendChild(opener);
                opener.click();
                opener.remove();
            }
        }
    });
});
</script>

<?php if ($editProduct): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('[data-drawer-open="product-drawer"]')?.click();
    });
    </script>
<?php endif; ?>
