<?php
/**
 * Variables:
 * @var string|null $size (sm, md, lg)
 * @var string|null $class Custom utility class
 */
$size = $size ?? 'md';
$sizeClasses = [
    'sm' => 'w-4 h-4 border-2',
    'md' => 'w-6 h-6 border-2',
    'lg' => 'w-8 h-8 border-3',
];
$spinnerClass = ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($class ?? '');
?>
<div class="inline-block animate-spin rounded-full border-t-transparent border-current <?= $spinnerClass ?>" role="status">
    <span class="sr-only">Loading...</span>
</div>