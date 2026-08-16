<?php
declare(strict_types=1);
?>
<div id="gv-step-3" data-step-panel="3" class="hidden space-y-6">
    <div class="space-y-4">
        <?php
        $fileName = 'combo_images[]';
        $fileId = 'combo-images';
        $fileLabel = 'Combo images';
        $fileHint = 'Select one or more images, each max 5MB (JPG/PNG/WebP)';
        $fileAccept = 'image/png,image/jpeg,image/webp';
        $fileRequired = $editCombo === null;
        $fileCurrentUrl = '';
        $fileMultiple = true;
        require BASE_PATH . '/resources/views/components/base/file-input.php';
        ?>

        <?php if ($comboImages): ?>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                <?php foreach ($comboImages as $image): ?>
                    <label class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200">
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" class="h-full w-full object-cover">
                        <span class="absolute inset-0 flex cursor-pointer items-center justify-center bg-black/60 text-xs font-bold text-white opacity-0 transition group-hover:opacity-100">
                            <input type="checkbox" name="delete_images[]" value="<?= (int) $image['id'] ?>" class="mr-2 accent-rose-500">
                            Remove
                        </span>
                        <?php if ($image['is_primary']): ?>
                            <span class="absolute left-1.5 top-1.5 rounded bg-primary px-1.5 py-0.5 text-[10px] font-bold text-white shadow-sm">Primary</span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <hr class="border-slate-100">

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-sm font-bold text-secondary">Video Links</span>
            <button type="button" data-add-video class="text-xs font-bold text-primary hover:text-secondary">+ Add link</button>
        </div>
        <div data-video-list class="space-y-2">
            <?php foreach (($comboVideos ?: ['']) as $videoUrl): ?>
                <div data-video-row class="flex gap-2">
                    <input type="url" name="video_urls[]" value="<?= htmlspecialchars((string) $videoUrl) ?>" placeholder="https://www.youtube.com/watch?v=..." class="<?= $fc ?>">
                    <button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 transition hover:bg-rose-50 cursor-pointer">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <hr class="border-slate-100">

    <div class="space-y-4">
        <h4 class="text-sm font-bold text-secondary">SEO & Discovery</h4>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">SEO title</span>
            <input name="meta_title" maxlength="190" value="<?= htmlspecialchars((string) ($editCombo['meta_title'] ?? '')) ?>" placeholder="Leave empty to use combo name" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">SEO description</span>
            <textarea name="meta_description" maxlength="255" rows="2" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCombo['meta_description'] ?? '')) ?></textarea>
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Search keywords</span>
            <input name="search_keywords" value="<?= htmlspecialchars((string) ($editCombo['search_keywords'] ?? '')) ?>" placeholder="e.g. #giftcombo, #chocolate" class="<?= $fc ?>">
        </label>
    </div>
</div>
