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
    <svg class="<?= $logoHeight ?> w-auto text-primary" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="100" height="100" rx="20" fill="currentColor"/>
        <path d="M50 20L75 45H25L50 20Z" fill="white"/>
        <rect x="35" y="45" width="30" height="35" fill="white"/>
        <rect x="25" y="55" width="50" height="8" fill="#e4738e"/>
    </svg>
    <span class="text-lg tracking-tight font-extrabold text-slate-800">Gift Vibe <span class="text-primary">LK</span></span>
</a>