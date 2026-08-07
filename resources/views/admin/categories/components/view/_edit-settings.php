<?php declare(strict_types=1); ?>
<div class="space-y-4">
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Display order</span>
        <input type="number" min="0" name="sort_order" value="<?= (int) ($editCategory['sort_order'] ?? 0) ?>" class="<?= $fc ?>">
    </label>
    <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 bg-white p-4 text-sm font-semibold text-secondary">
        Active / Visible
        <input type="checkbox" name="status" value="active" <?= ($editCategory['status'] ?? 'active') === 'active' ? 'checked' : '' ?> class="h-4 w-4 accent-primary">
    </label>
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Meta title</span>
        <input name="meta_title" value="<?= htmlspecialchars((string) ($editCategory['meta_title'] ?? '')) ?>" class="<?= $fc ?>">
    </label>
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Meta description</span>
        <textarea name="meta_description" rows="2" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCategory['meta_description'] ?? '')) ?></textarea>
    </label>
</div>
