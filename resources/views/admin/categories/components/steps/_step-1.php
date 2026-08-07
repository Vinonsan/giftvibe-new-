    <div id="gc-step-1" data-step-panel="1" class="space-y-4">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Category name <span class="text-primary">*</span></span>
            <input id="field-cat-name" required name="name" maxlength="160"
                   value="<?= htmlspecialchars((string) ($editCategory['name'] ?? '')) ?>"
                   placeholder="Birthday gifts" class="<?= $fc ?>">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Category link <span class="text-primary">*</span></span>
            <input id="field-cat-link" required name="link_url" maxlength="255"
                   value="<?= htmlspecialchars((string) ($editCategory['link_url'] ?? '')) ?>"
                   placeholder="/shop?category=birthday-gifts" class="<?= $fc ?>">
            <span class="mt-1.5 block text-xs text-slate-400">URL opened when the category card is clicked.</span>
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Description</span>
            <textarea id="field-cat-desc" name="description" rows="3"
                      placeholder="Short summary for listings and SEO"
                      class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCategory['description'] ?? '')) ?></textarea>
        </label>
    </div>
