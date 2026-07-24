<?php
/**
 * Variables:
 * @var array $activities Array: [['time' => '10m ago', 'desc' => 'Order #1024 placed', 'icon' => 'shopping-bag']]
 */
?>
<div class="flow-root">
    <ul role="list" class="-mb-8">
        <?php foreach ($activities as $index => $act): 
            $isLast = ($index === count($activities) - 1);
        ?>
            <li>
                <div class="relative pb-8">
                    <?php if (!$isLast): ?>
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                    <?php endif; ?>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                <?php component('admin/components/common/icon', ['name' => $act['icon'] ?? 'bell', 'size' => 'sm']); ?>
                            </span>
                        </div>
                        <div class="flex-grow flex items-center justify-between gap-4 pt-1.5">
                            <p class="text-xs text-slate-600"><?= e($act['desc']) ?></p>
                            <span class="text-2xs text-slate-400 whitespace-nowrap"><?= e($act['time']) ?></span>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>