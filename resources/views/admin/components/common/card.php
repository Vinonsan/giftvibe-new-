<?php
/**
 * Variables:
 * @var string $content
 * @var string|null $title
 * @var string|null $footer
 * @var string|null $class
 */
?>
<div class="overflow-hidden rounded-card border border-primary-900/10 bg-white shadow-card <?= $class ?? '' ?>">
    <?php if (!empty($title)): ?>
        <div class="border-b border-primary-900/10 px-5 py-4 sm:px-6">
            <h3 class="text-base font-extrabold text-primary"><?= e($title) ?></h3>
        </div>
    <?php endif; ?>
    <div class="px-5 py-5 sm:px-6">
        <?= $content ?>
    </div>
    <?php if (!empty($footer)): ?>
        <div class="border-t border-primary-900/10 bg-light px-5 py-4 sm:px-6">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
