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
    'images'   => 'Images',
    'social'   => 'Social Links',
    'pricing'  => 'Pricing',
    'options'  => 'Options',
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
    'images'   => '_edit-images.php',
    'social'   => '_edit-social.php',
    'pricing'  => '_edit-pricing.php',
    'options'  => '_edit-options.php',
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
                <p class="mt-1 text-sm text-slate-500">Product ID #<?= $productId ?></p>
            </div>
        </div>
    </div>

    <div class="space-y-3" data-product-accordion>
            <?php foreach ($tabLabels as $key => $label): ?>
            <details class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" <?= $tab === $key ? 'open' : '' ?>>
                <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 text-sm font-bold <?= $tab === $key ? 'text-primary' : 'text-secondary' ?>" <?php if($tab !== $key): ?>onclick="event.preventDefault();window.location.href='<?= htmlspecialchars(app_url('/admin/products/view?id='.$productId.'&tab='.$key.'&drawer=1'), ENT_QUOTES) ?>'"<?php endif; ?>>
                    <span><?= htmlspecialchars($label) ?></span>
                    <svg class="h-4 w-4 transition-transform [[open]>&]:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
                </summary>
                <?php if ($tab === $key): ?>
                <div class="border-t border-slate-200 p-5">
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

                <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search keywords</p>
                    <?php if ($keywords): ?>
                        <div class="mt-2 flex flex-wrap gap-2"><?php foreach ($keywords as $keyword): ?><span class="inline-flex rounded-lg bg-primary/10 px-3 py-1.5 text-xs font-bold text-primary">#<?= htmlspecialchars(ltrim($keyword, '#')) ?></span><?php endforeach; ?></div>
                    <?php else: ?><p class="mt-1 text-sm italic text-slate-400">No search keywords added.</p><?php endif; ?>
                </div>

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
                    <?php $field('Product source', ($product['procurement_type'] ?? 'handcrafted') === 'purchased' ? 'Purchased externally — deducted from cash' : 'Handcrafted — no cash deduction'); ?>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Profit per unit</p>
                        <p class="mt-1 text-lg font-black <?= $profit >= 0 ? 'text-emerald-600' : 'text-rose-500' ?>">LKR <?= number_format($profit, 2) ?></p>
                    </div>
                    <?php $field('Stock quantity', (string) (int) ($product['stock_quantity'] ?? 0)); ?>
                    <?php $field('Featured product', !empty($product['is_featured']) ? 'Yes' : 'No'); ?>
                    <?php $field('Status', $isActive ? 'Active / Visible' : 'Draft'); ?>
                </div>
            <?php elseif ($tab === 'options'): ?>
                <?php if ($productOptions ?? []): ?>
                    <div class="space-y-6">
                        <?php foreach ($productOptions as $opt): ?>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-secondary"><?= htmlspecialchars((string) $opt['name']) ?></h3>
                                    <span class="rounded-lg bg-white px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 border border-slate-200"><?= htmlspecialchars((string) $opt['type']) ?> <?= $opt['is_required'] ? '(Required)' : '' ?></span>
                                </div>
                                <?php if ($opt['values'] ?? []): ?>
                                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        <?php foreach ($opt['values'] as $val): ?>
                                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3">
                                                <?php if ($opt['type'] === 'image_select' && ($val['image_path'] ?? '')): ?>
                                                    <img src="<?= htmlspecialchars($normalizeImage((string) $val['image_path'])) ?>" alt="" class="h-10 w-10 shrink-0 rounded-lg object-cover border border-slate-100">
                                                <?php endif; ?>
                                                <div class="flex-1 min-w-0">
                                                    <p class="truncate text-xs font-semibold text-secondary"><?= htmlspecialchars((string) $val['label']) ?></p>
                                                    <?php if ((float)($val['price_adjustment'] ?? 0) > 0): ?>
                                                        <p class="text-[10px] font-bold text-emerald-600">+ LKR <?= number_format((float)$val['price_adjustment'], 2) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm italic text-slate-400">No product options configured.</p>
                <?php endif; ?>
            <?php endif; ?>
                </div>
                <?php endif; ?>
            </details>
            <?php endforeach; ?>
    </div>
</div>

<?php if ($editMode): require __DIR__ . '/components/view/_view-scripts.php'; endif; ?>
