<?php
declare(strict_types=1);
/** Finance breakdown datatable. */

$monthNames = [
    '01' => 'january', '02' => 'february', '03' => 'march', '04' => 'april',
    '05' => 'may', '06' => 'june', '07' => 'july', '08' => 'august',
    '09' => 'september', '10' => 'october', '11' => 'november', '12' => 'december',
];

$tableRows = [];
foreach ($orderReports as $rep) {
    $pMargin = $rep['sales'] > 0 ? ($rep['profit'] / $rep['sales']) * 100 : 0;
    $parts = explode('-', (string) $rep['date']);
    $yr = $parts[0] ?? '';
    $mo = $monthNames[$parts[1] ?? ''] ?? '';

    $refCell = '<strong class="text-secondary">' . htmlspecialchars((string) $rep['order_number']) . '</strong>';
    if (($rep['source'] ?? '') === 'order' && !empty($rep['order_id'])) {
        $refCell = '<a href="/admin/orders/view?id=' . (int) $rep['order_id'] . '" class="font-bold text-primary hover:underline">' . htmlspecialchars((string) $rep['order_number']) . '</a>';
    }

    $actions = '';
    if (($rep['source'] ?? '') === 'order' && !empty($rep['order_id'])) {
        $actions = '<div class="flex items-center justify-end">'
            . '<a href="/admin/orders/view?id=' . (int) $rep['order_id'] . '" title="View order" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:text-primary">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
            . '</div>';
    }

    $tableRows[] = [
        '_attrs' => [
            'sales' => (float) $rep['sales'],
            'cost' => (float) $rep['cost'],
            'profit' => (float) $rep['profit'],
        ],
        'order_number' => $refCell,
        'customer_name' => '<strong class="text-secondary">' . htmlspecialchars((string) $rep['customer_name']) . '</strong>',
        'sales' => 'LKR ' . number_format((float) $rep['sales'], 2),
        'cost' => 'LKR ' . number_format((float) $rep['cost'], 2),
        'profit' => '<strong class="text-primary">LKR ' . number_format((float) $rep['profit'], 2) . '</strong>',
        'margin' => '<span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold text-primary">' . number_format($pMargin, 1) . '%</span>',
        'date' => '<span class="text-xs text-slate-500">' . htmlspecialchars((string) $rep['date']) . '</span>',
        'year' => $yr,
        'month' => $mo,
        '_actions' => $actions,
    ];
}

$tableId = 'finance-table';
$tableColumns = [
    ['key' => 'order_number', 'label' => 'Reference #', 'html' => true],
    ['key' => 'customer_name', 'label' => 'Description / source', 'html' => true],
    ['key' => 'sales', 'label' => 'Income', 'type' => 'number'],
    ['key' => 'cost', 'label' => 'Expenses', 'type' => 'number'],
    ['key' => 'profit', 'label' => 'Net profit', 'html' => true, 'type' => 'number'],
    ['key' => 'margin', 'label' => 'Margin', 'html' => true, 'type' => 'number', 'sortable' => false],
    ['key' => 'date', 'label' => 'Date', 'html' => true, 'type' => 'date'],
    ['key' => 'year', 'label' => 'Year', 'class' => 'hidden'],
    ['key' => 'month', 'label' => 'Month', 'class' => 'hidden'],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No delivered orders match the current filter.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
