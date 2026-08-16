<?php
declare(strict_types=1);

$tableRows = [];
foreach ($combos as $item) {
    $id = (int) $item['id'];
    $image = (string) ($item['image_path'] ?? '/assets/images/hero_slide_1.jpg');
    if (str_starts_with($image, 'public/')) $image = '/' . substr($image, 7);
    $active = $item['status'] === 'active';
    $tableRows[] = [
        'combo' => '<div class="flex items-center gap-3"><img src="' . htmlspecialchars($image, ENT_QUOTES) . '" class="h-12 w-14 shrink-0 rounded-lg object-cover"><div><p class="font-semibold leading-snug text-secondary">' . htmlspecialchars($item['name']) . '</p><p class="mt-0.5 text-[11px] text-secondary/60">' . (int)$item['product_count'] . ' products</p></div></div>',
        'price' => '<span class="font-semibold text-secondary">LKR ' . number_format((float) $item['price'], 2) . '</span>',
        'status' => $active ? '<span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">Active</span>' : '<span class="rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-bold text-secondary/60">Draft</span>',
        '_actions' => '<div class="flex items-center justify-end gap-2">'
            . '<a href="/admin/combos/view?id=' . $id . '" title="View" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
            . '<button type="button" data-delete-trigger data-modal-id="combo-delete-modal" data-id="' . $id . '" data-name="' . htmlspecialchars($item['name'], ENT_QUOTES) . '" class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border border-rose-200 text-rose-500 transition hover:bg-rose-50" title="Delete"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button></div>',
    ];
}

$tableId = 'combos-table';
$tableColumns = [
    ['key' => 'combo', 'label' => 'Combo', 'html' => true, 'sortable' => false],
    ['key' => 'price', 'label' => 'Price', 'html' => true],
    ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tableDefaultSort = null;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No combos found.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
