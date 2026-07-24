<?php
/**
 * Variables:
 * @var string $name
 * @var string $url
 * @var string $image
 * @var int|null $itemCount
 * @var string|null $description
 */
?>
<a href="<?= e($url) ?>" class="block rounded-card overflow-hidden shadow-card hover:shadow-lg border border-slate-100 group relative bg-white">
    <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
        <?php component('public/components/common/image', [
            'src' => $image,
            'alt' => $name,
            'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
            'width' => 400,
            'height' => 300
        ]); ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-4 text-white">
            <h3 class="text-lg font-bold"><?= e($name) ?></h3>
            <?php if (isset($itemCount)): ?>
                <p class="text-xs text-slate-200"><?= (int)$itemCount ?> Items</p>
            <?php endif; ?>
        </div>
    </div>
</a>