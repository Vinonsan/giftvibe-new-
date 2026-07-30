<?php
/**
 * Variables:
 * @var string|null $size
 * @var string|null $class
 */
$size = $size ?? 'md';
$sizeMap = [
    'sm' => 'h-6',
    'md' => 'h-8',
    'lg' => 'h-10',
    'xl' => 'h-14',
];
$logoHeight = $sizeMap[$size] ?? 'h-8';
?>
<a href="<?= BASE_URL ?>" class="flex items-center gap-2 font-bold text-primary focus:outline-none focus:ring-2 focus:ring-primary-500 rounded <?= $class ?? '' ?>">
    <img src="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>" alt="Gift Vibe LK logo" class="<?= $logoHeight ?> w-auto shrink-0 object-contain">
</a>
