<?php
/**
 * Variables:
 * @var string|null $backUrl
 * @var string|null $submitLabel
 */
?>
<div class="flex justify-end gap-3 border-t border-slate-200 pt-5 mt-6">
    <?php if (!empty($backUrl)): ?>
        <?php component('admin/components/common/button', [
            'label' => 'Cancel',
            'href' => $backUrl,
            'variant' => 'outline',
            'size' => 'sm'
        ]); ?>
    <?php endif; ?>
    <?php component('admin/components/common/button', [
        'label' => $submitLabel ?? 'Save Changes',
        'type' => 'submit',
        'variant' => 'primary',
        'size' => 'sm'
    ]); ?>
</div>