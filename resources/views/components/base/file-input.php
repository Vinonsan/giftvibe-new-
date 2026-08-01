<?php

declare(strict_types=1);

$fileName = (string) ($fileName ?? 'file');
$fileId = (string) ($fileId ?? $fileName);
$fileLabel = (string) ($fileLabel ?? 'Upload file');
$fileHint = (string) ($fileHint ?? 'PNG, JPG or WebP up to 5MB');
$fileAccept = (string) ($fileAccept ?? 'image/png,image/jpeg,image/webp');
$fileRequired = (bool) ($fileRequired ?? false);
$fileCurrentUrl = (string) ($fileCurrentUrl ?? '');
?>
<div data-file-input class="space-y-2">
    <label for="<?= htmlspecialchars($fileId) ?>" class="block text-xs font-semibold text-slate-600">
        <?= htmlspecialchars($fileLabel) ?><?= $fileRequired ? ' <span class="text-rose-500">*</span>' : '' ?>
    </label>
    <label for="<?= htmlspecialchars($fileId) ?>" class="group relative flex min-h-40 cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition hover:border-primary/40 hover:bg-primary/[0.03] focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10">
        <input id="<?= htmlspecialchars($fileId) ?>" name="<?= htmlspecialchars($fileName) ?>" type="file" accept="<?= htmlspecialchars($fileAccept) ?>" <?= $fileRequired ? 'required' : '' ?> class="sr-only" data-file-control>
        <img src="<?= htmlspecialchars($fileCurrentUrl) ?>" alt="Image preview" class="absolute inset-0 h-full w-full object-cover <?= $fileCurrentUrl === '' ? 'hidden' : '' ?>" data-file-preview>
        <span class="relative z-10 flex flex-col items-center px-5 py-6 text-center <?= $fileCurrentUrl !== '' ? 'rounded-xl bg-white/90 shadow-sm backdrop-blur-sm' : '' ?>" data-file-placeholder>
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-primary shadow-sm ring-1 ring-slate-200">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 002 2h10a2 2 0 002-2v-4"/></svg>
            </span>
            <span class="mt-3 text-sm font-semibold text-secondary">Choose an image</span>
            <span class="mt-1 text-xs text-slate-500"><?= htmlspecialchars($fileHint) ?></span>
            <span class="mt-2 max-w-56 truncate text-xs font-medium text-primary" data-file-name></span>
        </span>
    </label>
</div>

<script>
(function () {
    document.querySelectorAll('[data-file-input]').forEach(function (wrapper) {
        if (wrapper.dataset.ready === 'true') return;
        var control = wrapper.querySelector('[data-file-control]');
        var preview = wrapper.querySelector('[data-file-preview]');
        var fileName = wrapper.querySelector('[data-file-name]');

        control.addEventListener('change', function () {
            var file = control.files && control.files[0];
            if (!file) return;
            fileName.textContent = file.name;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        });

        wrapper.dataset.ready = 'true';
    });
})();
</script>
