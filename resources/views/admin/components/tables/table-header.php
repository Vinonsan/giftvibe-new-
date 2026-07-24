<?php
/**
 * Variables:
 * @var string $title
 * @var string|null $count
 */
?>
<div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
    <div class="flex items-center gap-2">
        <h3 class="text-sm font-bold text-slate-800"><?= e($title) ?></h3>
        <?php if (isset($count)): ?>
            <span class="px-2 py-0.5 rounded-full text-2xs bg-slate-100 text-slate-600 font-bold border border-slate-200"><?= (int)$count ?></span>
        <?php endif; ?>
    </div>
</div>