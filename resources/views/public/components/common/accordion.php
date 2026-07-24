<?php
/**
 * Variables:
 * @var array $items Array: [['title' => 'Title', 'content' => 'HTML', 'active' => false]]
 */
?>
<div class="space-y-4">
    <?php foreach ($items as $index => $item): 
        $isActive = $item['active'] ?? false;
        $uid = 'acc-' . $index . '-' . uniqid();
    ?>
        <div class="border border-slate-200 rounded-card overflow-hidden bg-white" data-accordion>
            <button type="button" class="w-full flex items-center justify-between px-5 py-4 text-left font-bold text-slate-800 bg-slate-50/50 hover:bg-slate-50 transition-colors" data-accordion-trigger aria-controls="<?= $uid ?>" aria-expanded="<?= $isActive ? 'true' : 'false' ?>">
                <span><?= e($item['title']) ?></span>
                <?php component('public/components/common/icon', ['name' => 'chevron-down', 'size' => 'sm', 'class' => 'transform transition-transform ' . ($isActive ? 'rotate-180' : ''), 'data-accordion-icon' => true]); ?>
            </button>
            <div id="<?= $uid ?>" class="px-5 py-4 border-t border-slate-100 <?= $isActive ? '' : 'hidden' ?>">
                <div class="text-sm text-slate-600 leading-relaxed"><?= $item['content'] ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>