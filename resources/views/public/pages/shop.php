<section class="bg-white py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Published catalog</p>
                <h1 class="mt-2 text-3xl font-black text-slate-950"><?= e($heading ?? 'Shop Gifts') ?></h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600"><?= e($description ?? 'Browse GiftVibe.lk products.') ?></p>
            </div>
            <form method="GET" action="<?= e(url('/shop')) ?>" class="grid gap-3 rounded-card border border-slate-200 bg-slate-50 p-3 sm:grid-cols-2 lg:grid-cols-5">
                <input name="q" value="<?= e($filters['q'] ?? '') ?>" type="search" class="rounded-button border border-slate-300 px-3 py-2 text-sm" placeholder="Search products">
                <select name="category" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    <?php foreach (($categories ?? []) as $category): ?>
                        <option value="<?= e($category['slug']) ?>" <?= ($filters['category'] ?? '') === $category['slug'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="stock" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Any stock</option>
                    <option value="in_stock" <?= ($filters['stock'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In stock</option>
                    <option value="out_of_stock" <?= ($filters['stock'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of stock</option>
                </select>
                <label class="flex items-center gap-2 rounded-button border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-600">
                    <input type="checkbox" name="offers" value="1" <?= !empty($filters['offers']) ? 'checked' : '' ?>> Offers
                </label>
                <button class="rounded-button bg-primary px-4 py-2 text-sm font-bold text-white hover:bg-primary-700">Filter</button>
            </form>
        </div>

        <div class="mt-8">
            <?php component('public/partials/product-grid', ['products' => $products ?? []]); ?>
        </div>

        <?php if (($totalPages ?? 1) > 1): ?>
            <?php component('public/components/common/pagination', [
                'currentPage' => $currentPage ?? 1,
                'totalPages' => $totalPages ?? 1,
                'baseUrl' => $baseUrl ?? url('/shop?'),
            ]); ?>
        <?php endif; ?>
    </div>
</section>
