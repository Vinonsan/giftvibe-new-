<?php
declare(strict_types=1);

/**
 * @var array       $combo
 * @var array       $editCombo
 * @var array       $products
 * @var array       $comboProducts
 * @var array       $selectedProductIds
 * @var array       $comboImages
 * @var array       $comboVideos
 * @var string      $tab
 * @var array       $tabs
 * @var bool        $editMode
 * @var array|null  $flash
 * @var string      $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$comboId = (int) $combo['id'];

$tabLabels = [
    'basic'    => 'Basic Info',
    'products' => 'Products & Pricing',
    'images'   => 'Images & Media',
    'seo'      => 'SEO',
    'settings' => 'Settings',
];

$tabEditPartials = [
    'basic'    => '_edit-basic.php',
    'products' => '_edit-products.php',
    'images'   => '_edit-images.php',
    'seo'      => '_edit-seo.php',
    'settings' => '_edit-settings.php',
];

$normalizeImage = static function (string $path): string {
    if ($path === '') return '/assets/images/hero_slide_1.jpg';
    if (str_starts_with($path, 'public/')) return '/' . substr($path, 7);
    if (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) return '/' . $path;
    return $path;
};

$primaryImage = $normalizeImage((string) ($combo['primary_image'] ?? ''));
$isActive = ($combo['status'] ?? 'active') === 'active';

$field = static function (string $label, string $value, bool $empty = false): void {
    ?>
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($label) ?></p>
        <p class="mt-1 text-sm font-semibold <?= $empty ? 'text-slate-400 italic' : 'text-secondary' ?>"><?= $empty ? '—' : htmlspecialchars($value) ?></p>
    </div>
    <?php
};
?>

<div class="space-y-5">
    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <!-- ── Header ───────────────────────────────────────── -->
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-4">
            <img src="<?= htmlspecialchars($primaryImage) ?>" alt="" class="h-16 w-16 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-bold text-secondary"><?= htmlspecialchars((string) $combo['name']) ?></h1>
                    <?php if ($isActive): ?>
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">Active</span>
                    <?php else: ?>
                        <span class="rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-bold text-secondary/60">Draft</span>
                    <?php endif; ?>
                </div>
                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg> LKR <?= number_format((float) $combo['price'], 2) ?></span>
                    <span>&middot;</span>
                    <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg> <?= count($comboProducts) ?> Products</span>
                </div>
            </div>
        </div>
        <a href="/admin/combos" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to combos
        </a>
    </div>

    <!-- ── Main Content ─────────────────────────────────── -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-3 pt-3" aria-label="Product sections">
            <?php foreach ($tabLabels as $key => $label): ?>
                <a href="/admin/combos/view?id=<?= $comboId ?>&tab=<?= htmlspecialchars($key) ?>"
                   class="shrink-0 rounded-t-xl px-4 py-2.5 text-sm font-bold transition <?= $tab === $key ? 'border border-b-white border-slate-200 bg-white text-primary -mb-px' : 'text-slate-500 hover:bg-slate-50 hover:text-secondary' ?>"
                   aria-current="<?= $tab === $key ? 'page' : 'false' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-secondary"><?= htmlspecialchars($tabLabels[$tab] ?? 'Details') ?></h2>
                <?php if (!$editMode): ?>
                    <a href="/admin/combos/view?id=<?= $comboId ?>&tab=<?= htmlspecialchars($tab) ?>&mode=edit" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/10 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        Edit
                    </a>
                <?php else: ?>
                    <a href="/admin/combos/view?id=<?= $comboId ?>&tab=<?= htmlspecialchars($tab) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Cancel</a>
                <?php endif; ?>
            </div>

            <?php if ($editMode): ?>
                <form method="post" action="/admin/combos" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="section" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="id" value="<?= $comboId ?>">
                    <input type="hidden" name="redirect_to" value="/admin/combos/view?id=<?= $comboId ?>&tab=<?= htmlspecialchars($tab) ?>">

                    <?php require __DIR__ . '/components/view/' . $tabEditPartials[$tab]; ?>

                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary transition">Save changes</button>
                    </div>
                </form>

            <?php elseif ($tab === 'basic'): ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php $field('Combo Name', (string) $combo['name']); ?>
                    <?php $field('Combo Price', 'LKR ' . number_format((float) $combo['price'], 2)); ?>
                    <?php $field('Slug', (string) ($combo['slug'] ?? ''), trim((string) ($combo['slug'] ?? '')) === ''); ?>
                </div>
                <div class="mt-4 grid gap-4">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</p>
                        <div class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-secondary">
                            <?= trim((string) ($combo['description'] ?? '')) !== '' ? nl2br(htmlspecialchars((string) $combo['description'])) : '<span class="italic text-slate-400">—</span>' ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($tab === 'products'): ?>
                <div class="rounded-xl border border-slate-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">SKU</th>
                                <th class="px-4 py-3 font-semibold">Product Name</th>
                                <th class="px-4 py-3 font-semibold text-right">Cost</th>
                                <th class="px-4 py-3 font-semibold text-right">Base Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                            $totalCost = 0;
                            $totalSelling = 0;
                            foreach ($comboProducts as $p): 
                                $c = (float) ($p['cost_price'] ?? 0);
                                $b = (float) ($p['base_price'] ?? 0);
                                $totalCost += $c;
                                $totalSelling += $b;
                            ?>
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500"><?= htmlspecialchars((string) $p['sku']) ?></td>
                                    <td class="px-4 py-3 font-semibold text-secondary"><?= htmlspecialchars((string) $p['name']) ?></td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 text-right"><?= number_format($c, 2) ?></td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 text-right"><?= number_format($b, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($comboProducts)): ?>
                                <tr><td colspan="4" class="p-4 text-center text-slate-500 italic">No products assigned.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <hr class="border-slate-100 my-6">
                <h3 class="text-sm font-bold text-secondary mb-4">Pricing Calculation</h3>
                
                <?php
                $otherCost = (float) ($combo['other_cost'] ?? 0);
                $finalCost = $totalCost + $otherCost;
                $sellingPrice = (float) ($combo['price'] ?? 0);
                $profit = $sellingPrice - $finalCost;
                ?>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Products Cost</p>
                        <p class="mt-1 text-sm font-semibold text-secondary">LKR <?= number_format($totalCost, 2) ?></p>
                    </div>
                    <?php $field('Other Cost (Packing, etc)', 'LKR ' . number_format($otherCost, 2)); ?>
                    <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Total Cost</p>
                        <p class="mt-1 text-sm font-semibold text-amber-900">LKR <?= number_format($finalCost, 2) ?></p>
                    </div>
                    <?php $field('Selling Price (Combo Price)', 'LKR ' . number_format($sellingPrice, 2)); ?>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Calculated Profit</p>
                        <p class="mt-1 text-lg font-black <?= $profit >= 0 ? 'text-emerald-600' : 'text-rose-500' ?>">LKR <?= number_format($profit, 2) ?></p>
                    </div>
                </div>

            <?php elseif ($tab === 'images'): ?>
                <div class="space-y-6">
                    <div>
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Combo Images</p>
                        <?php if ($comboImages): ?>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:grid-cols-5">
                                <?php foreach ($comboImages as $img): ?>
                                    <figure class="relative aspect-square overflow-hidden rounded-xl border border-slate-200 bg-white">
                                        <img src="<?= htmlspecialchars($normalizeImage($img['image_path'])) ?>" class="h-full w-full object-cover">
                                        <?php if ($img['is_primary']): ?>
                                            <span class="absolute left-1.5 top-1.5 rounded bg-primary px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">Primary</span>
                                        <?php endif; ?>
                                    </figure>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-sm italic text-slate-400">No images uploaded.</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($comboVideos): ?>
                        <div>
                            <p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Video Links</p>
                            <ul class="list-inside list-disc space-y-1 text-sm text-secondary">
                                <?php foreach ($comboVideos as $v): ?>
                                    <li><a href="<?= htmlspecialchars((string)$v) ?>" target="_blank" class="text-primary hover:underline break-all"><?= htmlspecialchars((string)$v) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($tab === 'seo'): ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php $field('Meta Title', (string) ($combo['meta_title'] ?? ''), trim((string) ($combo['meta_title'] ?? '')) === ''); ?>
                    <?php $field('Search Keywords', (string) ($combo['search_keywords'] ?? ''), trim((string) ($combo['search_keywords'] ?? '')) === ''); ?>
                </div>
                <div class="mt-4">
                    <?php $field('Meta Description', (string) ($combo['meta_description'] ?? ''), trim((string) ($combo['meta_description'] ?? '')) === ''); ?>
                </div>

            <?php elseif ($tab === 'settings'): ?>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $field('Status', $isActive ? 'Active / Visible' : 'Draft / Hidden'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
