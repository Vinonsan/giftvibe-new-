<?php declare(strict_types=1);
$imageValue = (string) ($editCategory['image_path'] ?? $editCategory['image'] ?? '');
if (str_starts_with($imageValue, 'public/')) $imageValue = '/' . substr($imageValue, 7);
?>
<div class="space-y-4">
    <?php
    $fileName = 'category_image';
    $fileId = 'category-image';
    $fileLabel = 'Category image';
    $fileHint = 'JPG, PNG or WebP · max 5 MB';
    $fileAccept = 'image/png,image/jpeg,image/webp';
    $fileRequired = false;
    $fileCurrentUrl = $imageValue;
    $fileMultiple = false;
    require BASE_PATH . '/resources/views/components/base/file-input.php';
    ?>
</div>
