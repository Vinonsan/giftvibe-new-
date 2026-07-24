<?php
/**
 * Variables:
 * @var string $message
 * @var string $type (success, warning, danger, info)
 */
?>
<div class="relative" data-toast>
    <?php component('admin/components/feedback/alert', ['message' => $message, 'type' => $type]); ?>
    <button type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 focus:outline-none" data-toast-dismiss>
        <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
    </button>
</div>