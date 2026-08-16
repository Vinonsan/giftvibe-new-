<?php
declare(strict_types=1);

/**
 * @var array       $category
 * @var array       $editCategory
 * @var string      $tab
 * @var bool        $editMode
 * @var array|null  $flash
 * @var string      $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$categoryId = (int) $category['id'];
$tabLabels = ['basic' => 'Basic Info', 'image' => 'Image', 'settings' => 'Settings'];
$tabEditPartials = ['basic' => '_edit-basic.php', 'image' => '_edit-image.php', 'settings' => '_edit-settings.php'];

$normalizeImage = static function (string $path): string {
    if ($path === '') return '/assets/images/hero_slide_1.jpg';
    if (str_starts_with($path, 'public/')) return '/' . substr($path, 7);
    if (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) return '/' . $path;
    return $path;
};

$imagePath = $normalizeImage((string) ($category['image_path'] ?? ''));
$isActive = ($category['status'] ?? 'active') === 'active';

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

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-4">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="" class="h-16 w-20 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-bold text-secondary"><?= htmlspecialchars((string) $category['name']) ?></h1>
                    <?php if ($isActive): ?>
                        <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">Active</span>
                    <?php else: ?>
                        <span class="rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-bold text-secondary/60">Inactive</span>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <a href="/admin/categories" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to categories
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-3 pt-3" aria-label="Category sections">
            <?php foreach ($tabLabels as $key => $label): ?>
                <a href="/admin/categories/view?id=<?= $categoryId ?>&tab=<?= htmlspecialchars($key) ?>"
                   class="shrink-0 rounded-t-xl px-4 py-2.5 text-sm font-bold transition <?= $tab === $key ? 'border border-b-white border-slate-200 bg-white text-primary -mb-px' : 'text-slate-500 hover:bg-slate-50 hover:text-secondary' ?>"
                   aria-current="<?= $tab === $key ? 'page' : 'false' ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-secondary"><?= htmlspecialchars($tabLabels[$tab] ?? 'Details') ?></h2>
                <?php if (!$editMode): ?>
                    <a href="/admin/categories/view?id=<?= $categoryId ?>&tab=<?= htmlspecialchars($tab) ?>&mode=edit" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/10 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        Edit
                    </a>
                <?php else: ?>
                    <a href="/admin/categories/view?id=<?= $categoryId ?>&tab=<?= htmlspecialchars($tab) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Cancel</a>
                <?php endif; ?>
            </div>

            <?php if ($editMode): ?>
                <form method="post" action="/admin/categories" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="save_section">
                    <input type="hidden" name="section" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                    <input type="hidden" name="id" value="<?= $categoryId ?>">
                    <?php require __DIR__ . '/components/view/' . $tabEditPartials[$tab]; ?>
                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary transition">Save changes</button>
                    </div>
                </form>
            <?php elseif ($tab === 'basic'): ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php $field('Category name', (string) $category['name']); ?>
                    <?php $field('Slug', (string) ($category['slug'] ?? ''), trim((string) ($category['slug'] ?? '')) === ''); ?>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Category link</p>
                        <a href="<?= htmlspecialchars((string) ($category['link_url'] ?? '/shop')) ?>" target="_blank" class="mt-1 block break-all text-sm font-semibold text-primary hover:underline"><?= htmlspecialchars((string) ($category['link_url'] ?? '/shop')) ?></a>
                    </div>
                </div>
                <div class="mt-4 grid gap-4">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</p>
                        <div class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-secondary">
                            <?= trim((string) ($category['description'] ?? '')) !== '' ? nl2br(htmlspecialchars((string) $category['description'])) : '<span class="italic text-slate-400">—</span>' ?>
                        </div>
                    </div>
                </div>

            <?php elseif ($tab === 'image'): ?>
                <figure class="max-w-xs overflow-hidden rounded-xl border border-slate-200 bg-white">
                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars((string) $category['name']) ?>" class="aspect-[4/5] w-full object-cover">
                </figure>

            <?php elseif ($tab === 'settings'): ?>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php $field('Display order', (string) (int) ($category['sort_order'] ?? 0)); ?>
                    <?php $field('Status', $isActive ? 'Active / Visible' : 'Inactive'); ?>
                    <?php $field('Meta title', (string) ($category['meta_title'] ?? ''), trim((string) ($category['meta_title'] ?? '')) === ''); ?>
                    <?php $field('Meta description', (string) ($category['meta_description'] ?? ''), trim((string) ($category['meta_description'] ?? '')) === ''); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
