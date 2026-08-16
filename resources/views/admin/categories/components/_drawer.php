<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$viewCategory = $viewCategory ?? null;
$isAddRequested = ($_GET['add'] ?? '') === '1';
$drawerCategory = $editCategory ?: $viewCategory;
$drawerMode = $editCategory ? 'edit' : ($viewCategory ? 'view' : 'create');
$imageValue = (string) ($drawerCategory['image_path'] ?? '');
if (str_starts_with($imageValue, 'public/')) $imageValue = '/' . substr($imageValue, 7);

ob_start();
?>
<?php if ($drawerMode === 'view'): ?>
<div class="space-y-6">
    <div>
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Category name</p>
        <p class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-base font-bold text-secondary"><?= htmlspecialchars((string) $viewCategory['name']) ?></p>
    </div>
    <div>
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Category image</p>
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
            <img src="<?= htmlspecialchars($imageValue) ?>" alt="<?= htmlspecialchars((string) $viewCategory['image_alt_text']) ?>" class="max-h-[28rem] w-full object-contain">
        </div>
    </div>
    <div>
        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Image alt words</p>
        <p class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-secondary"><?= htmlspecialchars((string) $viewCategory['image_alt_text']) ?></p>
    </div>
</div>
<?php else: ?>
<form id="category-form" method="post" action="/admin/categories" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editCategory): ?><input type="hidden" name="id" value="<?= (int) $editCategory['id'] ?>"><?php endif; ?>

    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Category name <span class="text-primary">*</span></span>
        <input required name="name" maxlength="160" value="<?= htmlspecialchars((string) ($editCategory['name'] ?? '')) ?>" placeholder="Birthday gifts" class="<?= $fc ?>">
    </label>

    <div>
        <?php
        $fileName = 'category_image';
        $fileId = 'category-image';
        $fileLabel = 'Category image';
        $fileHint = 'JPG, PNG or WebP · max 5 MB · recommended 800 × 900px';
        $fileAccept = 'image/png,image/jpeg,image/webp';
        $fileRequired = $editCategory === null;
        $fileCurrentUrl = $imageValue;
        $fileMultiple = false;
        require BASE_PATH . '/resources/views/components/base/file-input.php';
        ?>
    </div>

    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Image alt words <span class="text-primary">*</span></span>
        <input required name="image_alt_text" maxlength="255" value="<?= htmlspecialchars((string) ($editCategory['image_alt_text'] ?? '')) ?>" placeholder="Birthday gift collection" class="<?= $fc ?>">
        <span class="mt-1.5 block text-xs text-slate-400">Describe the image briefly for accessibility and search engines.</span>
    </label>
</form>
<?php endif; ?>
<?php
$drawerBody = (string) ob_get_clean();
if ($drawerMode === 'view') {
    $drawerFooter = '<a href="/admin/categories?edit=' . (int) $viewCategory['id'] . '" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Edit</a>';
} elseif ($drawerMode === 'edit') {
    $drawerFooter = '<a href="/admin/categories?view=' . (int) $editCategory['id'] . '" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-bold text-secondary hover:bg-slate-50">Cancel</a>'
        . '<button type="submit" form="category-form" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Save Changes</button>';
} else {
    $drawerFooter = '<button type="button" data-drawer-close class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-bold text-secondary hover:bg-slate-50">Cancel</button>'
        . '<button type="submit" form="category-form" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Save Category</button>';
}
$drawerId = 'category-drawer';
$drawerSide = 'right';
$drawerSize = 'xl';
$drawerTitle = $drawerMode === 'view' ? (string) $viewCategory['name'] : ($drawerMode === 'edit' ? 'Edit category' : 'Add category');
$drawerDescription = '';
$drawerTrigger = '<button type="button" data-drawer-open="category-drawer" class="hidden" aria-hidden="true" tabindex="-1"></button>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
$drawerHeaderBottom = '';
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if ($drawerCategory || $isAddRequested): ?>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelector('[data-drawer-open="category-drawer"]')?.click();});</script>
<?php endif; ?>
