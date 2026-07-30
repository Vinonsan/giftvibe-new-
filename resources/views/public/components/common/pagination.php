<?php
/**
 * Variables:
 * @var int $currentPage
 * @var int $totalPages
 * @var string $baseUrl Base URL prefix (will append page=N)
 */
$pageUrl = static function (int $page) use ($baseUrl): string {
    $separator = str_contains($baseUrl, '?') ? '&' : '?';
    $base = rtrim($baseUrl, '?&');
    return $base . $separator . 'page=' . $page;
};
?>
<nav class="flex items-center justify-between border-t border-slate-200 px-4 py-3 sm:px-0 mt-8" aria-label="Pagination">
    <!-- Mobile Pagination -->
    <div class="flex flex-1 justify-between sm:hidden">
        <?php component('public/components/common/button', [
            'label' => 'Previous',
            'href' => $currentPage > 1 ? $pageUrl($currentPage - 1) : null,
            'variant' => 'outline',
            'disabled' => $currentPage <= 1
        ]); ?>
        <?php component('public/components/common/button', [
            'label' => 'Next',
            'href' => $currentPage < $totalPages ? $pageUrl($currentPage + 1) : null,
            'variant' => 'outline',
            'disabled' => $currentPage >= $totalPages
        ]); ?>
    </div>
    <!-- Desktop Pagination -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">
                Showing Page <span class="font-bold text-slate-900"><?= (int)$currentPage ?></span> of <span class="font-bold text-slate-900"><?= (int)$totalPages ?></span>
            </p>
        </div>
        <div>
            <span class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
                <!-- Previous Button -->
                <a href="<?= $currentPage > 1 ? e($pageUrl($currentPage - 1)) : '#' ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 <?= $currentPage <= 1 ? 'pointer-events-none opacity-50' : '' ?>">
                    <span class="sr-only">Previous</span>
                    <?php component('public/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm', 'class' => 'rotate-180']); ?>
                </a>

                <?php for ($i = 1; $i <= $totalPages; $i++): 
                    $isActive = ($i === (int)$currentPage);
                    $pageClass = $isActive ? 'z-10 bg-primary border-primary text-white' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50';
                ?>
                    <a href="<?= e($pageUrl($i)) ?>" class="relative inline-flex items-center border px-4 py-2 text-sm font-medium focus:z-20 <?= $pageClass ?>"><?= $i ?></a>
                <?php endfor; ?>

                <!-- Next Button -->
                <a href="<?= $currentPage < $totalPages ? e($pageUrl($currentPage + 1)) : '#' ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 <?= $currentPage >= $totalPages ? 'pointer-events-none opacity-50' : '' ?>">
                    <span class="sr-only">Next</span>
                    <?php component('public/components/common/icon', ['name' => 'chevron-right', 'size' => 'sm']); ?>
                </a>
            </span>
        </div>
    </div>
</nav>
