<?php
declare(strict_types=1);
?>
<div class="space-y-4">
    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Combo Name <span class="text-primary">*</span></span>
        <input required name="name" maxlength="160" value="<?= htmlspecialchars((string) ($editCombo['name'] ?? '')) ?>" class="<?= $fc ?>">
    </label>

    <label class="block">
        <span class="mb-1.5 block text-xs font-bold text-secondary">Description</span>
        <textarea name="description" rows="5" class="<?= $fc ?>"><?= htmlspecialchars((string) ($editCombo['description'] ?? '')) ?></textarea>
    </label>
    

</div>
