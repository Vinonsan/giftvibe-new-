<?php
/**
 * Variables:
 * @var string $phone Phone number in international format
 * @var string|null $label
 * @var bool|null $showLabel
 */
$label = $label ?? 'Chat on WhatsApp';
$showLabel = $showLabel ?? true;
$url = 'https://wa.me/' . preg_replace('/\D/', '', $phone);
?>
<a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-full shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2" aria-label="Contact us on WhatsApp">
    <?php component('public/components/common/icon', ['name' => 'whatsapp', 'size' => 'md', 'class' => 'w-5 h-5']); ?>
    <?php if ($showLabel): ?>
        <span><?= e($label) ?></span>
    <?php endif; ?>
</a>