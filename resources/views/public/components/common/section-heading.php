<?php
/**
 * Variables:
 * @var string $title
 * @var string|null $subtitle
 * @var string|null $align (center, left)
 */
$align = $align ?? 'center';
$alignClass = $align === 'center' ? 'text-center' : 'text-left';
?>
<div class="mb-8 md:mb-12 <?= $alignClass ?>">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl"><?= e($title) ?></h2>
    <?php if (!empty($subtitle)): ?>
        <p class="mt-3 max-w-2xl mx-auto text-xl text-slate-500"><?= e($subtitle) ?></p>
    <?php endif; ?>
</div>