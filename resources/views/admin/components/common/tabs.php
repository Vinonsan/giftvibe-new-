<?php
/**
 * Variables:
 * @var array $tabs Array: [['id' => 'tab1', 'label' => 'Tab 1', 'content' => 'HTML', 'active' => true]]
 */
?>
<div class="w-full">
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-6" role="tablist">
            <?php foreach ($tabs as $tab): 
                $isActive = $tab['active'] ?? false;
                $btnClass = $isActive ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300';
            ?>
                <button type="button" class="whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition-colors <?= $btnClass ?>" role="tab" aria-controls="admin-panel-<?= e($tab['id']) ?>" aria-selected="<?= $isActive ? 'true' : 'false' ?>" data-tab>
                    <?= e($tab['label']) ?>
                </button>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="mt-4">
        <?php foreach ($tabs as $tab): 
            $isActive = $tab['active'] ?? false;
        ?>
            <div id="admin-panel-<?= e($tab['id']) ?>" role="tabpanel" class="<?= $isActive ? '' : 'hidden' ?> focus:outline-none">
                <?= $tab['content'] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>