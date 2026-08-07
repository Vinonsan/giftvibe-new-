<?php declare(strict_types=1); ?>
<div class="space-y-4">
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Category name <span class="text-primary">*</span></span>
        <input required name="name" maxlength="160" value="<?= htmlspecialchars((string) ($editCategory['name'] ?? '')) ?>" class="<?= $fc ?>">
    </label>
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Category link <span class="text-primary">*</span></span>
        <input required name="link_url" maxlength="255" value="<?= htmlspecialchars((string) ($editCategory['link_url'] ?? '')) ?>" class="<?= $fc ?>">
    </label>
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Description</span>
        <textarea name="description" rows="3" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCategory['description'] ?? '')) ?></textarea>
    </label>
</div>
