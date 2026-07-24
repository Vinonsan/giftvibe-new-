<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<nav class="hidden md:flex items-center gap-6" aria-label="Main Navigation">
    <?php foreach ($navigation as $item): 
        $hasChildren = !empty($item['children']);
        $activeClass = ($item['active'] ?? false) ? 'text-primary border-primary-500 font-semibold' : 'text-slate-600 border-transparent hover:text-slate-900 hover:border-slate-300';
    ?>
        <?php if ($hasChildren): ?>
            <div class="relative group" data-dropdown>
                <button type="button" class="inline-flex items-center gap-1 py-5 border-b-2 text-sm font-medium transition-colors <?= $activeClass ?>" aria-expanded="false" data-dropdown-trigger>
                    <span><?= e($item['label']) ?></span>
                    <?php component('public/components/common/icon', ['name' => 'chevron-down', 'size' => 'xs', 'class' => 'group-hover:rotate-180 transition-transform']); ?>
                </button>
                <div class="absolute left-0 top-full mt-1 w-56 bg-white border border-slate-200 rounded-card shadow-lg py-2 hidden group-hover:block z-50" data-dropdown-menu>
                    <?php foreach ($item['children'] as $child): ?>
                        <a href="<?= e($child['url']) ?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors focus:bg-slate-50 focus:outline-none">
                            <?= e($child['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= e($item['url']) ?>" class="inline-flex items-center py-5 border-b-2 text-sm font-medium transition-colors <?= $activeClass ?>">
                <?= e($item['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>