<?php
/**
 * Variables:
 * @var array $navigation
 */
?>
<div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white shadow-lg absolute top-full left-0 w-full z-40" data-mobile-menu>
    <div class="px-2 pt-2 pb-3 space-y-1">
        <?php foreach ($navigation as $item): 
            $hasChildren = !empty($item['children']);
            $activeClass = ($item['active'] ?? false) ? 'bg-primary-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
        ?>
            <?php if ($hasChildren): ?>
                <div data-accordion>
                    <button type="button" class="w-full flex items-center justify-between px-3 py-2 rounded-button text-base font-medium transition-colors <?= $activeClass ?>" data-accordion-trigger aria-controls="mobile-sub-<?= md5($item['label']) ?>" aria-expanded="false">
                        <span><?= e($item['label']) ?></span>
                        <?php component('public/components/common/icon', ['name' => 'chevron-down', 'size' => 'xs', 'data-accordion-icon' => true]); ?>
                    </button>
                    <div id="mobile-sub-<?= md5($item['label']) ?>" class="hidden pl-6 pr-3 py-1 space-y-1">
                        <?php foreach ($item['children'] as $child): ?>
                            <a href="<?= e($child['url']) ?>" class="block px-3 py-2 rounded-button text-sm text-slate-500 hover:bg-slate-50 hover:text-primary">
                                <?= e($child['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= e($item['url']) ?>" class="block px-3 py-2 rounded-button text-base font-medium transition-colors <?= $activeClass ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>