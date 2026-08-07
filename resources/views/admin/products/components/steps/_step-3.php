    <div id="gv-step-3" data-step-panel="3" class="space-y-4 hidden">

        <?php
        $fileName       = 'product_images[]';
        $fileId         = 'product-images';
        $fileLabel      = 'Product images';
        $fileHint       = 'JPG, PNG or WebP · max 5 MB each';
        $fileAccept     = 'image/png,image/jpeg,image/webp';
        $fileRequired   = $editProduct === null;
        $fileCurrentUrl = '';
        $fileMultiple   = true;
        require BASE_PATH . '/resources/views/components/base/file-input.php';
        ?>

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Image Alt Text (SEO)</span>
            <input name="image_alt_text"
                   value="<?= htmlspecialchars((string)($editProduct['alt_text'] ?? '')) ?>"
                   placeholder="e.g. Pink Rose Bouquet Gift Sri Lanka"
                   class="<?= $fc ?>">
        </label>

        <?php if (!empty($secondaryImages)): ?>
            <div class="rounded-xl border border-slate-200 p-3">
                <span class="mb-2 block text-xs font-bold text-secondary">
                    Saved images
                    <span class="font-normal text-slate-400 ml-1">(hover → check to remove)</span>
                </span>
                <div class="grid grid-cols-4 gap-2">
                    <?php foreach ($secondaryImages as $si):
                        $sp = (string)$si['image_path'];
                        if (str_starts_with($sp, 'public/')) $sp = '/' . substr($sp, 7);
                    ?>
                        <div class="group relative aspect-square overflow-hidden rounded-lg border border-slate-100">
                            <img src="<?= htmlspecialchars($sp) ?>" class="h-full w-full object-cover">
                            <label class="absolute inset-0 flex cursor-pointer items-center justify-center bg-slate-900/60 text-[10px] font-semibold text-white opacity-0 transition-opacity group-hover:opacity-100">
                                <input type="checkbox" name="delete_images[]"
                                       value="<?= (int)$si['id'] ?>"
                                       class="mr-1 accent-rose-500"> Remove
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
