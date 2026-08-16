<?php
declare(strict_types=1);
?>
<div id="gv-step-1" data-step-panel="1" class="space-y-4">
    <div class="space-y-4">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Combo name <span class="text-primary">*</span></span>
            <input id="field-name" required name="name" maxlength="160" value="<?= htmlspecialchars((string) ($editCombo['name'] ?? '')) ?>" class="<?= $fc ?>">
        </label>
        
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Description</span>
            <textarea id="field-desc" name="description" rows="4" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCombo['description'] ?? '')) ?></textarea>
        </label>
        

        <label class="flex items-center justify-between rounded-xl border p-3 text-sm font-bold border-slate-200">
            Active
            <input type="checkbox" name="status" value="active" <?= !$editCombo || $editCombo['status'] === 'active' ? 'checked' : '' ?> class="accent-primary">
        </label>
    </div>
</div>
