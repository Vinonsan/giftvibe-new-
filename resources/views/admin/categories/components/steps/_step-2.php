    <div id="gc-step-2" data-step-panel="2" class="space-y-4 hidden">
        <?php
        $imageValue = (string) ($editCategory['image_path'] ?? $editCategory['image'] ?? '');
        if (str_starts_with($imageValue, 'public/')) $imageValue = '/' . substr($imageValue, 7);
        $fileName = 'category_image';
        $fileId = 'category-image';
        $fileLabel = 'Category image';
        $fileHint = 'JPG, PNG or WebP · max 5 MB · recommended 800 × 900px';
        $fileAccept = 'image/png,image/jpeg,image/webp';
        $fileRequired = $editCategory === null;
        $fileCurrentUrl = $imageValue;
        $fileMultiple = false;
        require BASE_PATH . '/resources/views/components/base/file-input.php';
        ?>
    </div>
