<?php
$money = static fn ($amount) => 'LKR ' . number_format((float) $amount, 2);
$label = static fn ($status) => ucwords(str_replace('_', ' ', (string) $status));
$imageUrl = static function (?string $path): string {
    if (!$path) {
        return asset('/public/assets/images/hero_gift_box.jpg');
    }
    return str_starts_with($path, 'http://') || str_starts_with($path, 'https://') ? $path : asset('/' . ltrim($path, '/'));
};
$productJson = static function (array $product): string {
    return e(json_encode([
        'id' => (int) $product['id'],
        'name' => (string) $product['name'],
        'unit' => (string) ($product['unit'] ?? ''),
        'base_price' => (string) ($product['base_price'] ?? '0'),
        'sale_price' => (string) ($product['sale_price'] ?? ''),
        'offer_type' => (string) ($product['offer_type'] ?? 'none'),
        'offer_value' => (string) ($product['offer_value'] ?? ''),
        'stock_quantity' => (int) ($product['stock_quantity'] ?? 0),
        'short_description' => (string) ($product['short_description'] ?? ''),
        'description' => (string) ($product['description'] ?? ''),
        'is_featured' => (int) ($product['is_featured'] ?? 0),
        'status' => (string) ($product['status'] ?? 'active'),
        'category_ids' => array_values(array_filter(array_map('intval', explode(',', (string) ($product['category_ids'] ?? ''))))),
    ], JSON_HEX_APOS | JSON_HEX_QUOT));
};
?>
<div class="space-y-6" data-products-page data-base-url="<?= e(BASE_URL) ?>">
    <!-- Page Header -->
    <section class="overflow-hidden rounded-card border border-slate-200 bg-white shadow-card">
        <div class="flex flex-col gap-5 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Catalog control</p>
                <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Products</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">Create published gifts, control stock, and watch review signals from one operational surface.</p>
            </div>
            <div class="flex flex-col items-start gap-3 lg:items-end">
                <?php if (!empty($error)): ?><div class="w-full rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
                <button
                    type="button"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-900 focus:ring-offset-2"
                    data-product-add
                    aria-label="Add product"
                >
                    <span class="text-xl font-light leading-none">+</span>
                    Add product
                </button>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2">
        <?php foreach (($products ?? []) as $product): ?>
            <article class="group grid gap-4 rounded-card border border-primary-900/10 bg-white p-4 shadow-card transition duration-300 hover:-translate-y-0.5 hover:border-primary-900/20 hover:shadow-[0_18px_48px_-28px_rgba(12,43,78,0.45)] sm:grid-cols-[7.5rem_1fr]" data-product-card data-product='<?= $productJson($product) ?>'>
                <div class="h-[7.5rem] w-[7.5rem] shrink-0 overflow-hidden rounded-button border border-primary-900/10 bg-light-100">
                    <img
                        src="<?= e($imageUrl($product['image_path'] ?? null)) ?>"
                        alt="<?= e($product['name']) ?> product image"
                        class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-125"
                        width="120"
                        height="120"
                        loading="lazy"
                    >
                </div>
                <div class="min-w-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= e($product['sku']) ?></p>
                            <h2 class="text-lg font-extrabold text-slate-950"><?= e($product['name']) ?></h2>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-500"><?= e($product['short_description'] ?: 'Published GiftVibe product.') ?></p>
                        </div>
                        <strong class="text-primary"><?= e($money($product['sale_price'] ?: $product['base_price'])) ?></strong>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                        <span class="rounded-button bg-slate-50 px-3 py-2 font-bold text-slate-600"><?= (int) $product['stock_quantity'] ?> stock</span>
                        <span class="rounded-button bg-primary-50 px-3 py-2 font-bold text-primary"><?= number_format((float) $product['average_rating'], 1) ?>/5</span>
                        <span class="rounded-button bg-slate-50 px-3 py-2 font-bold text-slate-600"><?= (int) $product['reviews_count'] ?> reviews</span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="<?= e(url('/admin/products/' . $product['id'])) ?>" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-primary-100 bg-primary-50 text-primary-900 transition hover:bg-primary-900 hover:text-white" title="View product" aria-label="View product">
                            <?php component('admin/components/common/icon', ['name' => 'eye', 'size' => 'sm']); ?>
                        </a>
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-secondary-100 bg-secondary-50 text-secondary-800 transition hover:bg-secondary-800 hover:text-white" data-product-edit title="Edit product" aria-label="Edit product">
                            <?php component('admin/components/common/icon', ['name' => 'edit', 'size' => 'sm']); ?>
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($products)): ?><div class="rounded-card border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 lg:col-span-2">No products yet. Add the first gift above.</div><?php endif; ?>
    </section>

    <!-- Drawer overlay -->
    <div class="fixed inset-0 z-[9998] hidden bg-primary-950/50 opacity-0 backdrop-blur-sm transition-opacity duration-200" data-product-drawer-overlay></div>

    <!-- Add Product Drawer -->
    <aside
        class="fixed inset-y-0 right-0 z-[9999] flex h-screen h-dvh max-h-screen max-h-dvh w-full translate-x-full flex-col border-l border-primary-900/10 bg-white shadow-[0_24px_80px_-30px_rgba(12,43,78,0.45)] transition-transform duration-300 ease-out sm:max-w-[38rem]"
        style="height: 100vh; height: 100dvh; max-height: 100vh; max-height: 100dvh;"
        data-product-drawer
        aria-hidden="true"
        aria-labelledby="product-drawer-title"
    >
        <div class="border-b border-primary-900/10 bg-white px-5 py-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Catalog</p>
                    <h2 id="product-drawer-title" class="mt-1 text-xl font-extrabold tracking-tight text-primary-900" data-product-drawer-title>Add Product</h2>
                </div>
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-button border border-slate-200 bg-white text-slate-500 shadow-card transition duration-200 hover:bg-white hover:text-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    data-product-drawer-close
                    aria-label="Close product drawer"
                >
                    <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto bg-white px-5 py-6">
            <form method="POST" action="<?= e(url('/admin/products')) ?>" enctype="multipart/form-data" class="space-y-5" data-product-form>
                <?= csrf_field() ?>

                <div class="space-y-4">
                    <!-- Product Name -->
                    <div class="space-y-2">
                        <label for="product-name" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                            Gift name <span class="text-danger-600">*</span>
                        </label>
                        <input id="product-name" name="name" required
                            class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-name-input
                            placeholder="e.g. Birthday Surprise Box">
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Category</span>
                        <details class="group relative" data-product-category-dropdown>
                            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 marker:hidden focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                <span data-product-category-summary>Select categories</span>
                                <span class="text-xs text-slate-400 transition group-open:rotate-180">v</span>
                            </summary>
                            <div class="absolute left-0 right-0 top-[calc(100%+0.35rem)] z-20 max-h-64 overflow-y-auto rounded-button border border-primary-900/10 bg-white p-2 shadow-card">
                                <?php foreach (($categories ?? []) as $cat): ?>
                                    <label class="flex cursor-pointer items-center gap-2 rounded-button px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-primary-50">
                                        <input type="checkbox" name="category_ids[]" value="<?= e((string) $cat['id']) ?>" data-product-category-option class="h-4 w-4 rounded border-slate-300 text-primary-900 focus:ring-primary-500">
                                        <span><?= e($cat['name']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </details>
                        <?php if (empty($categories)): ?>
                            <p class="text-sm text-slate-400">No categories available.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Unit -->
                    <div class="space-y-2">
                        <label for="product-unit" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Unit</label>
                        <input id="product-unit" name="unit"
                            class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-unit-input
                            placeholder="e.g. Per Box, Per Piece, Per Dozen">
                    </div>

                    <!-- Pricing section -->
                    <div class="rounded-card border border-primary-900/10 bg-slate-50 p-4 space-y-4">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Pricing</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="product-base-price" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Unit original cost</label>
                                <input id="product-base-price" name="base_price" type="number" step="0.01" min="0"
                                    class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                                    data-product-base-price-input
                                    placeholder="0.00">
                            </div>
                            <div class="space-y-2">
                                <label for="product-sale-price" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Unit selling price</label>
                                <input id="product-sale-price" name="sale_price" type="number" step="0.01" min="0"
                                    class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                                    data-product-sale-price-input
                                    placeholder="0.00">
                            </div>
                        </div>

                        <!-- Offer / Discount section -->
                        <div class="rounded-card border border-amber-200 bg-amber-50/50 p-4 space-y-3">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-amber-700">Offer / Discount</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="product-offer-type" class="block text-xs font-medium text-amber-800">Offer type</label>
                                    <select id="product-offer-type" name="offer_type"
                                        class="min-h-12 w-full rounded-button border border-amber-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                                        onchange="toggleOfferValue()">
                                        <option value="none">No offer</option>
                                        <option value="fixed">Fixed amount (LKR)</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label for="product-offer-value" class="block text-xs font-medium text-amber-800">Offer value</label>
                                    <input id="product-offer-value" name="offer_value" type="number" step="0.01" min="0"
                                        class="min-h-12 w-full rounded-button border border-amber-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                                        placeholder="0.00" disabled>
                                </div>
                            </div>
                            <p id="offer-preview" class="hidden text-sm font-bold text-amber-700"></p>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="space-y-2">
                        <label for="product-stock" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Stock quantity</label>
                        <input id="product-stock" name="stock_quantity" type="number" min="0" value="10"
                            class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-stock-input
                            placeholder="10">
                    </div>

                    <!-- Product image -->
                    <div class="space-y-2">
                        <label for="product-main-image" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">
                            Product image <span class="text-danger-600">*</span>
                        </label>
                        <label class="flex min-h-12 cursor-pointer items-center rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-700 outline-none transition hover:border-primary-300">
                            <span class="truncate" data-product-main-file-label>Upload product image</span>
                            <input id="product-main-image" name="product_image" type="file" accept="image/jpeg,image/png,image/webp" required class="sr-only" data-product-main-image>
                        </label>
                    </div>

                    <!-- Short Description -->
                    <div class="space-y-2">
                        <label for="product-description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Short description</label>
                        <textarea id="product-description" name="short_description" rows="2"
                            class="w-full rounded-button border border-primary-900/10 bg-white px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-short-description-input
                            placeholder="Short description for this gift"></textarea>
                    </div>

                    <!-- Full Description -->
                    <div class="space-y-2">
                        <label for="product-full-description" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Full description</label>
                        <textarea id="product-full-description" name="description" rows="5"
                            class="w-full rounded-button border border-primary-900/10 bg-white px-4 py-3 text-sm leading-6 text-slate-800 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-description-input
                            placeholder="Detailed product description, features, dimensions, etc."></textarea>
                    </div>

                    <!-- Featured -->
                    <div class="flex items-center gap-3">
                        <label class="flex cursor-pointer items-center gap-2 text-sm font-bold text-slate-700">
                            <input type="checkbox" name="is_featured" class="h-4 w-4 rounded border-slate-300 text-primary-900 focus:ring-primary-500" data-product-featured-input>
                            <span data-product-featured-label>Featured</span>
                        </label>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label for="product-status" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Status</label>
                        <select id="product-status" name="status"
                            class="min-h-12 w-full rounded-button border border-primary-900/10 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition duration-200 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/10"
                            data-product-status-input>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= e($status) ?>" <?= $status === 'active' ? 'selected' : '' ?>><?= e($label($status)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ── Product Variants (Multi Color / Design) ── -->
                    <div class="rounded-card border border-primary-900/10 bg-white p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Variants (Color / Design)</p>
                            <button type="button" onclick="addVariantRow()"
                                class="inline-flex items-center gap-1 rounded-button bg-primary-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-primary-800">
                                + Add variant
                            </button>
                        </div>
                        <p class="text-xs text-slate-400">Add different colors, designs, or sizes for this product.</p>
                        <div id="variants-container" class="space-y-3">
                            <!-- Variant rows added via JS -->
                        </div>
                        <template id="variant-row-template">
                            <div class="variant-row rounded-button border border-slate-200 bg-slate-50 p-3 space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-500 variant-index-label">Variant #1</span>
                                    <button type="button" onclick="removeVariantRow(this)"
                                        class="text-xs font-bold text-red-500 hover:text-red-700">Remove</button>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="variant_name[]" placeholder="Product name" required
                                        class="min-h-10 w-full rounded-button border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-950 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                                    <input name="variant_color[]" placeholder="Description"
                                        class="min-h-10 w-full rounded-button border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-950 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <label class="flex min-h-10 cursor-pointer items-center rounded-button border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 outline-none transition hover:border-primary-300">
                                        <span class="truncate" data-variant-file-label>Upload image</span>
                                        <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" data-variant-image-file>
                                    </label>
                                    <input name="variant_price[]" type="number" step="0.01" placeholder="Price adj."
                                        class="min-h-10 w-full rounded-button border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-950 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                                    <input name="variant_stock[]" type="number" min="0" value="0" placeholder="Stock"
                                        class="min-h-10 w-full rounded-button border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-950 outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-end border-t border-primary-900/10 bg-white px-5 py-4 shadow-[0_-18px_54px_-44px_rgba(12,43,78,0.35)]">
            <button
                type="button"
                class="inline-flex min-h-11 min-w-28 items-center justify-center rounded-button bg-primary-900 px-5 text-sm font-bold text-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500"
                data-product-save
            >
                <span data-product-save-label>Save</span>
            </button>
        </div>
    </aside>
</div>

<script>
function toggleOfferValue() {
    const type = document.getElementById('product-offer-type');
    const value = document.getElementById('product-offer-value');
    const preview = document.getElementById('offer-preview');
    const basePrice = document.getElementById('product-base-price');

    if (type.value === 'none') {
        value.disabled = true;
        value.value = '';
        preview.classList.add('hidden');
    } else {
        value.disabled = false;
        value.placeholder = type.value === 'fixed' ? 'Discount in LKR' : 'Discount %';
    }
}

document.getElementById('product-offer-type')?.addEventListener('change', toggleOfferValue);
document.getElementById('product-offer-value')?.addEventListener('input', showOfferPreview);
document.getElementById('product-base-price')?.addEventListener('input', showOfferPreview);

function showOfferPreview() {
    const type = document.getElementById('product-offer-type');
    const value = document.getElementById('product-offer-value');
    const basePrice = document.getElementById('product-base-price');
    const preview = document.getElementById('offer-preview');

    const base = parseFloat(basePrice.value) || 0;
    const val = parseFloat(value.value) || 0;

    if (type.value === 'none' || !val || !base) {
        preview.classList.add('hidden');
        return;
    }

    let finalPrice;
    if (type.value === 'fixed') {
        finalPrice = base - val;
        preview.textContent = 'Original: LKR ' + base.toFixed(2) + ' → Offer: LKR ' + val.toFixed(2) + ' off → Final: LKR ' + Math.max(0, finalPrice).toFixed(2);
    } else {
        finalPrice = base - (base * val / 100);
        preview.textContent = 'Original: LKR ' + base.toFixed(2) + ' → ' + val.toFixed(0) + '% off → Final: LKR ' + Math.max(0, finalPrice).toFixed(2);
    }
    preview.classList.remove('hidden');
}

function addVariantRow() {
    const template = document.getElementById('variant-row-template');
    const container = document.getElementById('variants-container');
    const clone = template.content.cloneNode(true);
    container.appendChild(clone);
    refreshVariantRows();
}

function removeVariantRow(button) {
    button.closest('.variant-row')?.remove();
    refreshVariantRows();
}

function refreshVariantRows() {
    document.querySelectorAll('#variants-container .variant-row').forEach((row, index) => {
        const label = row.querySelector('.variant-index-label');
        const fileInput = row.querySelector('[data-variant-image-file]');
        if (label) label.textContent = 'Variant #' + (index + 1);
        if (fileInput) fileInput.name = 'variant_image_file_' + index;
    });
}

document.addEventListener('change', (event) => {
    const productImageInput = event.target.closest('[data-product-main-image]');
    if (productImageInput) {
        const label = productImageInput.closest('label')?.querySelector('[data-product-main-file-label]');
        if (label) label.textContent = productImageInput.files?.[0]?.name || 'Upload product image';
        return;
    }

    const fileInput = event.target.closest('[data-variant-image-file]');
    if (!fileInput) return;
    const label = fileInput.closest('label')?.querySelector('[data-variant-file-label]');
    if (label) label.textContent = fileInput.files?.[0]?.name || 'Upload image';
});
</script>
