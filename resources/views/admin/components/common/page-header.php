<?php
/**
 * Variables:
 * @var string $title
 * @var string|null $description
 * @var string|null $actions Slot containing HTML buttons or links
 */
?>
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?= e($title) ?></h1>
        <?php if (!empty($description)): ?>
            <p class="mt-2 text-sm text-slate-500"><?= e($description) ?></p>
        <?php endif; ?>
    </div>
    <?php if (!empty($actions)): ?>
        <div class="flex flex-wrap items-center gap-3">
            <?= $actions ?>
        </div>
    <?php endif; ?>
</div>