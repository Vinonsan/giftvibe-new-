<?php
declare(strict_types=1);
?>
<div class="space-y-6">
    <div class="space-y-4">
        <?php
        $fileName = 'combo_images[]';
        $fileId = 'combo-images';
        $fileLabel = 'Add New Images';
        $fileHint = 'Select one or more images, each max 5MB (JPG/PNG/WebP)';
        $fileAccept = 'image/png,image/jpeg,image/webp';
        $fileRequired = false;
        $fileCurrentUrl = '';
        $fileMultiple = true;
        require BASE_PATH . '/resources/views/components/base/file-input.php';
        ?>

        <?php if ($comboImages): ?>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:grid-cols-5">
                <?php foreach ($comboImages as $image): ?>
                    <label class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200">
                        <img src="<?= htmlspecialchars($normalizeImage($image['image_path'])) ?>" class="h-full w-full object-cover">
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
            <p class="text-xs text-slate-500 italic">Select an image to mark it for removal upon saving.</p>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var videoList = document.querySelector('[data-video-list]');
    function makeVideoRow() {
        var row = document.createElement('div');
        row.dataset.videoRow = '';
        row.className = 'flex gap-2';
        row.innerHTML = '<input type="url" name="video_urls[]" placeholder="https://www.youtube.com/watch?v=…" class="<?= $fc ?>">' +
            '<button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 transition hover:bg-rose-50 cursor-pointer">&times;</button>';
        return row;
    }
    function bindVideoRemove(btn) {
        btn.addEventListener('click', function () {
            var rows = videoList ? videoList.querySelectorAll('[data-video-row]') : [];
            if (rows.length > 1) btn.closest('[data-video-row]').remove();
            else { var inp = btn.closest('[data-video-row]').querySelector('input'); if(inp) inp.value = ''; }
        });
    }
    videoList && videoList.querySelectorAll('[data-remove-video]').forEach(bindVideoRemove);
    document.querySelector('[data-add-video]')?.addEventListener('click', function () {
        var row = makeVideoRow();
        videoList.appendChild(row);
        bindVideoRemove(row.querySelector('[data-remove-video]'));
    });
});
</script>
