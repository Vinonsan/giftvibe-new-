<?php
declare(strict_types=1);

$tableRows = [];
foreach ($items as $item) {
    $id = (int) $item['id'];
    $isCatalog = !empty($item['product_id']);
    $qty = (int) $item['quantity'];
    $low = (int) $item['low_stock_threshold'];
    $sourceType = (string) ($item['source_type'] ?? 'stock');
    $sellingPrice = isset($item['selling_price']) ? (float) $item['selling_price'] : null;
    $profit = $sellingPrice !== null ? $sellingPrice - (float) $item['cost_price'] : null;

    $typeBadge = $isCatalog
        ? '<span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">Shop product</span>'
        : '<span class="rounded-full bg-violet-50 px-2.5 py-0.5 text-xs font-bold text-violet-700">Extra stock</span>';

    $qtyClass = $qty <= $low ? 'text-rose-600 font-black' : 'text-secondary font-bold';

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<button type="button" data-drawer-open="inventory-adjust-drawer" data-adjust-id="' . $id . '" data-adjust-name="' . htmlspecialchars((string) $item['name'], ENT_QUOTES) . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 hover:text-primary" title="Adjust stock">±</button>';

    if (!$isCatalog) {
        $actions .= '<button type="button" data-drawer-open="inventory-item-drawer" data-inventory-edit="' . $id . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 hover:text-primary" title="Edit">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.862 4.487 19.5 7.125M4.5 20.25l4.106-1.027a2.25 2.25 0 0 0 1.006-.596l9.32-9.32a2.25 2.25 0 0 0 0-3.182l-1.64-1.64a2.25 2.25 0 0 0-3.182 0l-9.32 9.32a2.25 2.25 0 0 0-.596 1.006L4.5 20.25Z"/></svg></button>'
            . '<button type="button" data-delete-trigger data-modal-id="inventory-delete-modal" data-id="' . $id . '" data-name="' . htmlspecialchars((string) $item['name'], ENT_QUOTES) . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50">&times;</button>';
    } else {
        $actions .= '<a href="/admin/products/view?id=' . (int) $item['product_id'] . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 hover:text-primary" title="View product">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>';
    }
    $actions .= '</div>';

    $tableRows[] = [
        'name' => '<strong class="text-secondary">' . htmlspecialchars((string) $item['name']) . '</strong>',
        'sku' => htmlspecialchars((string) ($item['sku'] ?? '—')),
        'source_type' => '<span data-filter-match="' . htmlspecialchars($sourceType) . '">' . $typeBadge . '</span>',
        'quantity' => '<span class="' . $qtyClass . '">' . number_format($qty) . ' ' . htmlspecialchars((string) ($item['unit'] ?? 'pcs')) . '</span>',
        'cost_price' => 'LKR ' . number_format((float) $item['cost_price'], 2),
        'selling_price' => $sellingPrice !== null ? 'LKR ' . number_format($sellingPrice, 2) : '—',
        'profit' => $profit !== null ? '<span class="' . ($profit >= 0 ? 'font-bold text-emerald-600' : 'font-bold text-rose-600') . '">LKR ' . number_format($profit, 2) . '</span>' : '—',
        'low_stock_threshold' => (string) $low,
        '_meta' => htmlspecialchars(json_encode([
            'id' => $id,
            'name' => (string) $item['name'],
            'sku' => (string) ($item['sku'] ?? ''),
            'quantity' => $qty,
            'cost_price' => (float) $item['cost_price'],
            'unit' => (string) ($item['unit'] ?? 'pcs'),
            'low_stock_threshold' => $low,
            'notes' => (string) ($item['notes'] ?? ''),
        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES),
        '_actions' => $actions,
    ];
}

$tableId = 'inventory-table';
$tableColumns = [
    ['key' => 'name', 'label' => 'Item', 'html' => true],
    ['key' => 'sku', 'label' => 'SKU'],
    ['key' => 'source_type', 'label' => 'Type', 'html' => true],
    ['key' => 'quantity', 'label' => 'In stock', 'html' => true, 'type' => 'number'],
    ['key' => 'cost_price', 'label' => 'Cost / unit', 'type' => 'number'],
    ['key' => 'selling_price', 'label' => 'Selling price', 'type' => 'number'],
    ['key' => 'profit', 'label' => 'Profit / unit', 'html' => true, 'type' => 'number'],
    ['key' => 'low_stock_threshold', 'label' => 'Low at', 'type' => 'number'],
    ['key' => '_meta', 'label' => '', 'class' => 'hidden'],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableEmptyMessage = 'No inventory items yet. Add a stock item or record a product purchase expense.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
