<?php declare(strict_types=1); ?>
<div class="space-y-4">
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Category name <span class="text-primary">*</span></span>
        <input id="edit-category-name" required name="name" maxlength="160" value="<?= htmlspecialchars((string) ($editCategory['name'] ?? '')) ?>" class="<?= $fc ?>">
    </label>
</div>
