<?php
/**
 * Variables:
 * @var string $src
 * @var string $alt
 * @var string|null $class
 * @var int $width
 * @var int $height
 * @var bool|null $lazy
 */
$lazy = $lazy ?? true;
?>
<img 
    src="<?= e($src) ?>" 
    alt="<?= e($alt) ?>" 
    width="<?= (int)$width ?>" 
    height="<?= (int)$height ?>" 
    loading="<?= $lazy ? 'lazy' : 'eager' ?>" 
    class="<?= $class ?? '' ?>"
>