<?php
declare(strict_types=1);
?>
<div class="space-y-4">
    <label class="flex items-center justify-between rounded-xl border p-3 text-sm font-bold border-slate-200 bg-white">
        <div>
            <p class="text-secondary">Active Status</p>
            <p class="text-[11px] font-normal text-slate-500">Is this combo visible to customers?</p>
        </div>
        <input type="checkbox" name="status" value="active" <?= (!$editCombo || ($editCombo['status'] ?? 'active') === 'active') ? 'checked' : '' ?> class="accent-primary">
    </label>
</div>
