<?php
/**
 * Variables:
 * @var array $items Array: [['label' => 'Dashboard', 'url' => '/admin'], ['label' => 'Products']]
 */
?>
<nav class="flex py-2 text-slate-500 text-xs" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="/admin" class="inline-flex items-center text-slate-500 hover:text-primary">
                <?php component('admin/components/common/icon', ['name' => 'dashboard', 'size' => 'xs', 'class' => 'mr-1']); ?>
                Dashboard
            </a>
        </li>
        <?php foreach ($items as $index => $item): 
            $isLast = ($index === count($items) - 1);
        ?>
            <li class="flex items-center">
                <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'xs', 'class' => 'text-slate-300 mx-1']); ?>
                <?php if ($isLast || empty($item['url'])): ?>
                    <span class="text-slate-800 font-semibold" aria-current="page"><?= e($item['label']) ?></span>
                <?php else: ?>
                    <a href="<?= e($item['url']) ?>" class="hover:text-primary transition-colors"><?= e($item['label']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>