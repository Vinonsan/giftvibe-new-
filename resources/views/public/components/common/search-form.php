<?php
/**
 * Variables:
 * @var string|null $placeholder
 * @var string|null $query
 */
?>
<form action="/search" method="GET" class="relative max-w-lg w-full">
    <div class="relative">
        <input type="text" name="q" value="<?= e($query ?? '') ?>" placeholder="<?= e($placeholder ?? 'Search for gifts, occasions, categories...') ?>" class="w-full bg-slate-100 text-slate-800 placeholder-slate-400 pl-10 pr-4 py-2 rounded-button border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-colors" required>
        <div class="absolute left-3 top-2.5 text-slate-400">
            <?php component('public/components/common/icon', ['name' => 'search', 'size' => 'sm']); ?>
        </div>
    </div>
</form>