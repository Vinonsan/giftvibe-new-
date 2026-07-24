<?php
/**
 * Variables:
 * @var array $items Array of items: [['label' => 'Home', 'url' => '/'], ['label' => 'Products']]
 */
?>
<nav class="flex py-3 text-slate-500 text-sm" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="/" class="inline-flex items-center text-slate-500 hover:text-primary">
                <?php component('public/components/common/icon', ['name' => 'gift', 'size' => 'xs', 'class' => 'mr-1']); ?>
                Home
            </a>
        </li>
        <?php foreach ($items as $index => $item): 
            $isLast = ($index === count($items) - 1);
        ?>
            <li class="flex items-center">
                <?php component('public/components/common/icon', ['name' => 'chevron-right', 'size' => 'xs', 'class' => 'text-slate-300 mx-1']); ?>
                <?php if ($isLast || empty($item['url'])): ?>
                    <span class="text-slate-800 font-medium" aria-current="page"><?= e($item['label']) ?></span>
                <?php else: ?>
                    <a href="<?= e($item['url']) ?>" class="hover:text-primary transition-colors"><?= e($item['label']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>