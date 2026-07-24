<?php
/**
 * Variables:
 * @var string $href
 * @var string $label
 * @var string|null $class
 * @var string|null $target
 * @var string|null $rel
 * @var bool|null $external
 */
$external = $external ?? false;
$target = $target ?? ($external ? '_blank' : null);
$rel = $rel ?? ($external ? 'noopener noreferrer' : null);
?>
<a href="<?= e($href) ?>" class="text-primary hover:text-primary-600 transition-colors inline-flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded <?= $class ?? '' ?>" <?= $target ? 'target="'.e($target).'"' : '' ?> <?= $rel ? 'rel="'.e($rel).'"' : '' ?>>
    <span><?= e($label) ?></span>
    <?php if ($external): ?>
        <?php component('public/components/common/icon', ['name' => 'external-link', 'size' => 'xs']); ?>
    <?php endif; ?>
</a>