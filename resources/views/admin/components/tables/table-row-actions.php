<?php
/**
 * Variables:
 * @var string|int $rowId
 * @var string|null $editUrl
 * @var string|null $deleteUrl
 */
?>
<div class="flex items-center justify-end gap-1.5">
    <?php if (!empty($editUrl)): ?>
        <?php component('admin/components/common/icon-button', [
            'icon' => 'edit',
            'label' => 'Edit item ' . $rowId,
            'href' => $editUrl,
            'variant' => 'ghost',
            'size' => 'xs'
        ]); ?>
    <?php endif; ?>
    <?php if (!empty($deleteUrl)): ?>
        <form action="<?= e($deleteUrl) ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
            <?= csrf_field() ?>
            <button type="submit" class="p-1 rounded-full text-red-500 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400" aria-label="Delete item <?= e($rowId) ?>">
                <?php component('admin/components/common/icon', ['name' => 'trash', 'size' => 'xs']); ?>
            </button>
        </form>
    <?php endif; ?>
</div>
