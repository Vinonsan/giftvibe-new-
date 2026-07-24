<?php
/**
 * Variables:
 * @var string|null $type (table, card, text)
 */
$type = $type ?? 'table';
?>
<div class="animate-pulse space-y-4 w-full">
    <?php if ($type === 'table'): ?>
        <div class="h-6 bg-slate-200 rounded w-full"></div>
        <div class="h-10 bg-slate-200 rounded w-full"></div>
        <div class="h-10 bg-slate-200 rounded w-full"></div>
    <?php elseif ($type === 'card'): ?>
        <div class="h-32 bg-slate-200 rounded-card"></div>
    <?php else: ?>
        <div class="h-4 bg-slate-200 rounded w-full"></div>
        <div class="h-4 bg-slate-200 rounded w-5/6"></div>
    <?php endif; ?>
</div>