<?php
declare(strict_types=1);

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$imageValue = (string) ($editCategory['image_path'] ?? '');
if (str_starts_with($imageValue, 'public/')) $imageValue = '/' . substr($imageValue, 7);

ob_start();
?>
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
<?php
$drawerBody = (string) ob_get_clean();
$drawerFooter = '<button type="submit" form="category-form" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">' . ($editCategory ? 'Save Changes' : 'Save Category') . '</button>';
$drawerId = 'category-drawer';
$drawerSide = 'right';
$drawerSize = 'lg';
$drawerTitle = $editCategory ? 'Edit category' : 'Add category';
$drawerDescription = '';
$drawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
$drawerHeaderBottom = '';
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if ($editCategory): ?>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelector('[data-drawer-open="category-drawer"]')?.click();});</script>
<?php endif; ?>
