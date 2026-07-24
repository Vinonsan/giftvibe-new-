<?php
/**
 * Variables:
 * @var string|null $type (card, list, text)
 */
$type = $type ?? 'card';
?>
<div class="animate-pulse space-y-4 w-full">
    <?php if ($type === 'card'): ?>
        <div class="aspect-square bg-slate-200 rounded-card"></div>
        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
        <div class="h-4 bg-slate-200 rounded w-1/2"></div>
    <?php elseif ($type === 'list'): ?>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                <div class="h-3 bg-slate-200 rounded w-1/4"></div>
            </div>
        </div>
    <?php else: ?>
        <div class="h-4 bg-slate-200 rounded w-full"></div>
        <div class="h-4 bg-slate-200 rounded w-5/6"></div>
        <div class="h-4 bg-slate-200 rounded w-2/3"></div>
    <?php endif; ?>
</div>