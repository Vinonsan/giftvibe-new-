<?php
/**
 * Variables:
 * @var string $content Content of filter fields (selects, inputs)
 */
?>
<div class="border border-slate-200 rounded-card p-4 mb-4 bg-slate-50 flex flex-wrap items-center gap-3">
    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filters:</span>
    <div class="flex flex-wrap items-center gap-3 flex-1">
        <?= $content ?>
    </div>
    <?php component('admin/components/common/button', [
        'label' => 'Clear',
        'variant' => 'ghost',
        'size' => 'xs'
    ]); ?>
</div>