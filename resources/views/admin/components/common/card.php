<?php
/**
 * Variables:
 * @var string $content
 * @var string|null $title
 * @var string|null $footer
 * @var string|null $class
 */
?>
<div class="bg-white rounded-card shadow-card border border-slate-200 overflow-hidden <?= $class ?? '' ?>">
    <?php if (!empty($title)): ?>
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-800"><?= e($title) ?></h3>
        </div>
    <?php endif; ?>
    <div class="px-6 py-5">
        <?= $content ?>
    </div>
    <?php if (!empty($footer)): ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>