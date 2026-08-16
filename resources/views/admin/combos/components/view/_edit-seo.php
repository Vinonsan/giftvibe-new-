<?php
declare(strict_types=1);
?>
<div class="space-y-4">
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">SEO Title</span>
        <input name="meta_title" maxlength="190" value="<?= htmlspecialchars((string) ($editCombo['meta_title'] ?? '')) ?>" class="<?= $fc ?>">
    </label>
    
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">SEO Description</span>
        <textarea name="meta_description" maxlength="255" rows="2" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCombo['meta_description'] ?? '')) ?></textarea>
    </label>
    
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Search Keywords</span>
        <input name="search_keywords" value="<?= htmlspecialchars((string) ($editCombo['search_keywords'] ?? '')) ?>" class="<?= $fc ?>">
        <p class="mt-1 text-[11px] text-slate-400">Comma-separated tags or hashtags to help customers find this combo.</p>
    </label>
</div>
