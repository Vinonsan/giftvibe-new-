<?php
/**
 * Variables:
 * @var array $actions Associative array: [['value' => 'delete', 'label' => 'Delete Selected']]
 */
?>
<div class="flex items-center gap-2">
    <select class="rounded-button border border-slate-300 px-3 py-1.5 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-primary-500">
        <option value="">Bulk Actions</option>
        <?php foreach ($actions as $action): ?>
            <option value="<?= e($action['value']) ?>"><?= e($action['label']) ?></option>
        <?php endforeach; ?>
    </select>
    <?php component('admin/components/common/button', [
        'label' => 'Apply',
        'variant' => 'outline',
        'size' => 'xs'
    ]); ?>
</div>