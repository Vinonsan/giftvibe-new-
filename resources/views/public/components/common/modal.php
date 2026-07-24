<?php
/**
 * Variables:
 * @var string $id Unique modal element ID
 * @var string $title
 * @var string $content
 * @var string|null $footer
 */
?>
<div id="<?= e($id) ?>" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true" aria-labelledby="<?= e($id) ?>-title" data-modal>
    <!-- Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" data-modal-overlay></div>

    <!-- Container -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-white rounded-card shadow-xl max-w-lg w-full overflow-hidden border border-slate-200 focus:outline-none" tabindex="0">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800" id="<?= e($id) ?>-title"><?= e($title) ?></h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 focus:outline-none" onclick="closeModal('#<?= e($id) ?>')">
                    <span class="sr-only">Close Modal</span>
                    <?php component('public/components/common/icon', ['name' => 'close', 'size' => 'md']); ?>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 text-sm text-slate-600">
                <?= $content ?>
            </div>

            <!-- Footer -->
            <?php if (!empty($footer)): ?>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <?= $footer ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>