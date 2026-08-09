    <div id="gc-step-1" data-step-panel="1" class="space-y-4">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Category name <span class="text-primary">*</span></span>
            <input id="field-cat-name" required name="name" maxlength="160"
                   value="<?= htmlspecialchars((string) ($editCategory['name'] ?? '')) ?>"
                   placeholder="Birthday gifts" class="<?= $fc ?>">
        </label>
    </div>
