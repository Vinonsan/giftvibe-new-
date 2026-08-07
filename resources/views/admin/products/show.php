<?php
declare(strict_types=1);

/**
 * @var array       $product
 * @var array       $editProduct
 * @var array       $categories
 * @var array       $editCategoryIds
 * @var array       $categoryNames
 * @var array       $productImages
 * @var array       $secondaryImages
 * @var array       $productVideos
 * @var array       $productSocialLinks
 * @var string      $tab
 * @var bool        $editMode
 * @var array|null  $flash
 * @var string      $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$productId = (int) $product['id'];
$tabLabels = [
    'basic'    => 'Basic Info',
    'keywords' => 'Keywords',
    'images'   => 'Images',
    'social'   => 'Social Links',
    'pricing'  => 'Pricing',
];

$normalizeImage = static function (string $path): string {
    if ($path === '') return '/assets/images/hero_slide_1.jpg';
    if (str_starts_with($path, 'public/')) return '/' . substr($path, 7);
    return $path;
};

$primaryImage = $normalizeImage((string) ($product['image_path'] ?? ''));
$profit = (float) ($product['base_price'] ?? 0) - (float) ($product['cost_price'] ?? 0);
$keywords = array_values(array_filter(array_map('trim', explode(',', (string) ($product['search_keywords'] ?? '')))));
$isActive = ($product['status'] ?? '') === 'active';

$field = static function (string $label, string $value, bool $empty = false): void {
    ?>
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($label) ?></p>
        <p class="mt-1 text-sm font-semibold <?= $empty ? 'text-slate-400 italic' : 'text-secondary' ?>">
            <?= $empty ? '—' : htmlspecialchars($value) ?>
        </p>
    </div>
    <?php
};

$tabEditPartials = [
    'basic'    => '_edit-basic.php',
    'keywords' => '_edit-keywords.php',
    'images'   => '_edit-images.php',
    'social'   => '_edit-social.php',
    'pricing'  => '_edit-pricing.php',
];
?>

<div class="space-y-5">
    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-4">
            <img src="<?= htmlspecialchars($primaryImage) ?>" alt="" class="h-16 w-20 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-bold text-secondary"><?= htmlspecialchars((string) $product['name']) ?></h1>
                    <?php if ($isActive): ?>
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">Active</span>
                    <?php else: ?>
                        <span class="rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-bold text-secondary/60">Draft</span>
                    <?php endif; ?>
                </div>
                <p class="mt-1 text-sm text-slate-500">SKU: <span class="font-semibold text-secondary"><?= htmlspecialchars((string) $product['sku']) ?></span> · ID #<?= $productId ?></p>
            </div>
        </div>
        <a href="/admin/products" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to products
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-3 pt-3" aria-label="Product sections">
            <?php foreach ($tabLabels as $key => $label): ?>
                <a
                    href="/admin/products/view?id=<?= $productId ?>&tab=<?= htmlspecialchars($key) ?>"
                    class="shrink-0 rounded-t-xl px-4 py-2.5 text-sm font-bold transition <?= $tab === $key ? 'border border-b-white border-slate-200 bg-white text-primary -mb-px' : 'text-slate-500 hover:bg-slate-50 hover:text-secondary' ?>"
                    aria-current="<?= $tab === $key ? 'page' : 'false' ?>"
                ><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-secondary"><?= htmlspecialchars($tabLabels[$tab] ?? 'Details') ?></h2>
                <?php if (!$editMode): ?>
                    <a href="/admin/products/view?id=<?= $productId ?>&tab=<?= htmlspecialchars($tab) ?>&mode=edit" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/10 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        Edit
                    </a>
                <?php else: ?>
                    <a href="/admin/products/view?id=<?= $productId ?>&tab=<?= htmlspecialchars($tab) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Cancel</a>
                <?php endif; ?>
            </div>

            <?php if ($editMode): ?>
                <form method="post" action="/admin/products" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="section" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="id" value="<?= $productId ?>">
                    <?php require __DIR__ . '/components/view/' . $tabEditPartials[$tab]; ?>
                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary transition">Save changes</button>
                    </div>
                </form>
            <?php elseif ($tab === 'basic'): ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php $field('Product name', (string) $product['name']); ?>
                    <?php $field('SKU', (string) $product['sku']); ?>
                    <?php $field('Categories', $categoryNames ? implode(', ', $categoryNames) : '', !$categoryNames); ?>
                    <?php $field('Slug', (string) ($product['slug'] ?? ''), trim((string) ($product['slug'] ?? '')) === ''); ?>
                </div>
                <div class="mt-4 grid gap-4">
                    <?php $field('Short description', (string) ($product['short_description'] ?? ''), trim((string) ($product['short_description'] ?? '')) === ''); ?>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Full description</p>
                        <div class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-secondary">
                            <?= trim((string) ($product['description'] ?? '')) !== '' ? nl2br(htmlspecialchars((string) $product['description'])) : '<span class="italic text-slate-400">—</span>' ?>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <?php $field('Meta title', (string) ($product['meta_title'] ?? ''), trim((string) ($product['meta_title'] ?? '')) === ''); ?>
                        <?php $field('Meta description', (string) ($product['meta_description'] ?? ''), trim((string) ($product['meta_description'] ?? '')) === ''); ?>
                    </div>
                </div>

            <?php elseif ($tab === 'keywords'): ?>
                <?php if ($keywords): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($keywords as $keyword): ?>
                            <span class="inline-flex rounded-lg bg-primary/10 px-3 py-1.5 text-xs font-bold text-primary">#<?= htmlspecialchars(ltrim($keyword, '#')) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm italic text-slate-400">No search keywords added.</p>
                <?php endif; ?>

            <?php elseif ($tab === 'images'): ?>
                <?php if ($productImages): ?>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        <?php foreach ($productImages as $image): ?>
                            <?php $src = $normalizeImage((string) $image['image_path']); ?>
                            <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars((string) ($image['alt_text'] ?? '')) ?>" class="aspect-square w-full object-cover">
                                <figcaption class="border-t border-slate-100 px-3 py-2 text-[11px] text-slate-500">
                                    <?= !empty($image['is_primary']) ? '<span class="font-bold text-primary">Primary</span> · ' : '' ?>
                                    <?= htmlspecialchars((string) ($image['alt_text'] ?: 'No alt text')) ?>
                                </figcaption>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm italic text-slate-400">No images uploaded.</p>
                <?php endif; ?>

            <?php elseif ($tab === 'social'): ?>
                <div class="space-y-6">
                    <div>
                        <h3 class="mb-3 text-sm font-bold text-secondary">YouTube / Video links</h3>
                        <?php if ($productVideos): ?>
                            <ul class="space-y-2">
                                <?php foreach ($productVideos as $videoUrl): ?>
                                    <?php if (trim((string) $videoUrl) === '') continue; ?>
                                    <li><a href="<?= htmlspecialchars((string) $videoUrl) ?>" target="_blank" rel="noopener" class="text-sm font-medium text-primary hover:underline break-all"><?= htmlspecialchars((string) $videoUrl) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm italic text-slate-400">No video links.</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="mb-3 text-sm font-bold text-secondary">Social &amp; website links</h3>
                        <?php if ($productSocialLinks): ?>
                            <ul class="space-y-2">
                                <?php foreach ($productSocialLinks as $link): ?>
                                    <li class="rounded-xl border border-slate-200 px-4 py-3">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars(ucfirst((string) ($link['platform'] ?? 'other'))) ?></p>
                                        <a href="<?= htmlspecialchars((string) $link['url']) ?>" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-medium text-primary hover:underline break-all"><?= htmlspecialchars((string) $link['url']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm italic text-slate-400">No social links.</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($tab === 'pricing'): ?>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $field('Selling price', 'LKR ' . number_format((float) ($product['base_price'] ?? 0), 2)); ?>
                    <?php $field('Cost / buying price', 'LKR ' . number_format((float) ($product['cost_price'] ?? 0), 2)); ?>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Profit per unit</p>
                        <p class="mt-1 text-lg font-black <?= $profit >= 0 ? 'text-emerald-600' : 'text-rose-500' ?>">LKR <?= number_format($profit, 2) ?></p>
                    </div>
                    <?php $field('Stock quantity', (string) (int) ($product['stock_quantity'] ?? 0)); ?>
                    <?php $field('Featured product', !empty($product['is_featured']) ? 'Yes' : 'No'); ?>
                    <?php $field('Status', $isActive ? 'Active / Visible' : 'Draft'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($editMode): require __DIR__ . '/components/view/_view-scripts.php'; endif; ?>
