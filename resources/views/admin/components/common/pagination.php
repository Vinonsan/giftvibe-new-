<?php
/**
 * Variables:
 * @var int $currentPage
 * @var int $totalPages
 * @var string $baseUrl
 */
?>
<div class="flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
        <a href="<?= $currentPage > 1 ? e($baseUrl . '?page=' . ($currentPage - 1)) : '#' ?>" class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Previous</a>
        <a href="<?= $currentPage < $totalPages ? e($baseUrl . '?page=' . ($currentPage + 1)) : '#' ?>" class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Next</a>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">
                Page <span class="font-semibold text-slate-800"><?= (int)$currentPage ?></span> of <span class="font-semibold text-slate-800"><?= (int)$totalPages ?></span>
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <a href="<?= $currentPage > 1 ? e($baseUrl . '?page=' . ($currentPage - 1)) : '#' ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 <?= $currentPage <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                    <span class="sr-only">Previous</span>
                    <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm', 'class' => 'rotate-180']); ?>
                </a>
                <?php for ($i = 1; $i <= $totalPages; $i++): 
                    $isActive = ($i === (int)$currentPage);
                    $tabClass = $isActive ? 'z-10 bg-primary text-white focus-visible:outline-primary-600' : 'text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-offset-0';
                ?>
                    <a href="<?= e($baseUrl . '?page=' . $i) ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 <?= $tabClass ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="<?= $currentPage < $totalPages ? e($baseUrl . '?page=' . ($currentPage + 1)) : '#' ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-50' : '' ?>">
                    <span class="sr-only">Next</span>
                    <?php component('admin/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                </a>
            </nav>
        </div>
    </div>
</div>