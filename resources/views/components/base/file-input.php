<?php

declare(strict_types=1);

$fileName = (string) ($fileName ?? 'file');
$fileId = (string) ($fileId ?? $fileName);
$fileLabel = (string) ($fileLabel ?? 'Upload file');
$fileHint = (string) ($fileHint ?? 'PNG, JPG or WebP up to 5MB');
$fileAccept = (string) ($fileAccept ?? 'image/png,image/jpeg,image/webp');
$fileRequired = (bool) ($fileRequired ?? false);
$fileCurrentUrl = (string) ($fileCurrentUrl ?? '');
$fileMultiple = (bool) ($fileMultiple ?? false);
?>
<div data-file-input class="space-y-2">
    <label for="<?= htmlspecialchars($fileId) ?>" class="block text-xs font-semibold text-slate-600">
        <?= htmlspecialchars($fileLabel) ?><?= $fileRequired ? ' <span class="text-rose-500">*</span>' : '' ?>
    </label>
    <label for="<?= htmlspecialchars($fileId) ?>" class="group relative flex min-h-36 cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition hover:border-primary/40 hover:bg-primary/[0.03] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10">
        <input id="<?= htmlspecialchars($fileId) ?>" name="<?= htmlspecialchars($fileName) ?>" type="file" accept="<?= htmlspecialchars($fileAccept) ?>" <?= $fileRequired ? 'required' : '' ?> <?= $fileMultiple ? 'multiple' : '' ?> class="sr-only" data-file-control>
        <span class="relative z-10 flex flex-col items-center px-5 py-6 text-center" data-file-placeholder>
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-primary shadow-sm ring-1 ring-slate-200">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg>
            </span>
            <span class="mt-3 text-sm font-semibold text-secondary"><?= $fileMultiple ? 'Choose images' : 'Choose an image' ?></span>
            <span class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($fileHint) ?></span>
            <span class="mt-2 max-w-56 truncate text-xs font-medium text-primary" data-file-name></span>
        </span>
    </label>
    <div class="<?= $fileCurrentUrl === '' ? 'hidden' : '' ?> overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" data-file-preview-wrap>
        <p class="mb-2 px-1 text-xs font-semibold text-slate-500">Image preview</p>
        <img src="<?= htmlspecialchars($fileCurrentUrl) ?>" alt="Image preview" class="h-44 w-full rounded-xl object-cover" data-file-preview>
    </div>
    <?php if ($fileMultiple): ?>
        <div class="hidden grid-cols-3 gap-2 rounded-2xl border border-slate-200 bg-white p-2" data-file-multiple-preview></div>
        <div class="hidden flex-wrap items-center gap-2" data-file-actions>
            <button type="button" data-file-add-more class="inline-flex items-center gap-1.5 rounded-xl border border-primary/20 bg-primary/5 px-3 py-2 text-xs font-bold text-primary transition hover:bg-primary/10 cursor-pointer">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add more images
            </button>
            <button type="button" data-file-clear-all class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-50 cursor-pointer">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                Clear all
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
(function () {
    document.querySelectorAll('[data-file-input]').forEach(function (wrapper) {
        if (wrapper.dataset.ready === 'true') return;
        var control = wrapper.querySelector('[data-file-control]');
        var preview = wrapper.querySelector('[data-file-preview]');
        var previewWrap = wrapper.querySelector('[data-file-preview-wrap]');
        var fileName = wrapper.querySelector('[data-file-name]');
        var multiplePreview = wrapper.querySelector('[data-file-multiple-preview]');
        var actionsRow = wrapper.querySelector('[data-file-actions]');
        var addMoreBtn = wrapper.querySelector('[data-file-add-more]');
        var clearAllBtn = wrapper.querySelector('[data-file-clear-all]');
        var isMultiple = !!multiplePreview;

        /* Keep a running list of chosen files so picking more appends instead of replacing. */
        var selectedFiles = [];

        function filesToArray(fileList) {
            var out = [];
            if (fileList) Array.prototype.forEach.call(fileList, function (f) { out.push(f); });
            return out;
        }

        function syncInputFiles() {
            if (!('DataTransfer' in window)) return;
            var transfer = new DataTransfer();
            selectedFiles.forEach(function (f) { transfer.items.add(f); });
            control.files = transfer.files;
        }

        function mergeNewFiles() {
            var seen = {};
            selectedFiles.forEach(function (f) { seen[f.name + '|' + f.size + '|' + (f.lastModified || 0)] = true; });
            filesToArray(control.files).forEach(function (f) {
                var key = f.name + '|' + f.size + '|' + (f.lastModified || 0);
                if (!seen[key]) { seen[key] = true; selectedFiles.push(f); }
            });
            syncInputFiles();
        }

        function renderMultipleFiles() {
            multiplePreview.innerHTML = '';
            fileName.textContent = selectedFiles.length
                ? selectedFiles.length + ' image' + (selectedFiles.length === 1 ? '' : 's') + ' selected'
                : '';
            selectedFiles.forEach(function (selectedFile, index) {
                var card = document.createElement('div');
                card.className = 'relative overflow-hidden rounded-lg border border-slate-200';
                var image = document.createElement('img');
                image.src = URL.createObjectURL(selectedFile);
                image.alt = selectedFile.name;
                image.className = 'h-24 w-full rounded-lg object-cover';
                var remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'absolute right-1.5 top-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-600 text-lg leading-none text-white shadow cursor-pointer';
                remove.setAttribute('aria-label', 'Remove ' + selectedFile.name);
                remove.innerHTML = '&times;';
                remove.addEventListener('click', function () {
                    selectedFiles.splice(index, 1);
                    syncInputFiles();
                    renderMultipleFiles();
                });
                card.appendChild(image);
                card.appendChild(remove);
                multiplePreview.appendChild(card);
            });
            multiplePreview.classList.toggle('hidden', selectedFiles.length === 0);
            multiplePreview.classList.toggle('grid', selectedFiles.length > 0);
            if (actionsRow) actionsRow.classList.toggle('hidden', selectedFiles.length === 0);
        }

        if (isMultiple) {
            control.addEventListener('change', function () {
                mergeNewFiles();
                renderMultipleFiles();
            });
            if (addMoreBtn) {
                addMoreBtn.addEventListener('click', function () {
                    control.click();
                });
            }
            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', function () {
                    selectedFiles = [];
                    syncInputFiles();
                    renderMultipleFiles();
                });
            }
        } else {
            control.addEventListener('change', function () {
                var file = control.files && control.files[0];
                if (!file) return;
                fileName.textContent = file.name;
                preview.src = URL.createObjectURL(file);
                previewWrap.classList.remove('hidden');
            });
        }

        wrapper.dataset.ready = 'true';
    });
})();
</script>
