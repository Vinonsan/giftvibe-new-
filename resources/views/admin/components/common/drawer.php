<?php
/**
 * Variables:
 * @var string $id
 * @var string $title
 * @var string $content
 * @var string|null $position
 */
$position = $position ?? 'right';
$posClasses = $position === 'left' ? 'left-0 -translate-x-full' : 'right-0 translate-x-full';
?>
<div id="<?= e($id) ?>" class="fixed inset-y-0 z-50 flex max-w-full pointer-events-none" data-drawer>
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity pointer-events-auto" data-drawer-overlay onclick="toggleDrawer('#<?= e($id) ?>')"></div>
    <div class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col h-full absolute transition-transform duration-300 ease-in-out <?= $posClasses ?> border-l border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800"><?= e($title) ?></h3>
            <button type="button" class="text-slate-400 hover:text-slate-600 focus:outline-none" onclick="toggleDrawer('#<?= e($id) ?>')">
                <span class="sr-only">Close Panel</span>
                <?php component('admin/components/common/icon', ['name' => 'close', 'size' => 'sm']); ?>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 text-slate-600 text-sm">
            <?= $content ?>
        </div>
    </div>
</div>