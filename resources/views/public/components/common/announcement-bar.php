<?php
/**
 * Variables:
 * @var string|null $message
 * @var string|null $link
 * @var string|null $linkLabel
 */
$message = $message ?? 'ðŸŽ Free delivery in Colombo district for orders above LKR 5,000!';
?>
<div class="bg-primary text-white text-xs font-semibold px-4 py-2 text-center relative flex items-center justify-center gap-2">
    <span><?= e($message) ?></span>
    <?php if (!empty($link) && !empty($linkLabel)): ?>
        <a href="<?= e($link) ?>" class="underline hover:text-primary-100 transition-colors"><?= e($linkLabel) ?> &rarr;</a>
    <?php endif; ?>
</div>