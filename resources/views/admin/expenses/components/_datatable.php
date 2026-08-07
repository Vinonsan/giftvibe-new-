<?php
declare(strict_types=1);

$statusBadge = static function (string $status): string {
    $label = ucfirst($status);
    $class = match ($status) {
        'approved', 'paid' => 'bg-emerald-50 text-emerald-700',
        'rejected' => 'bg-rose-50 text-rose-700',
        default => 'bg-amber-50 text-amber-700',
    };
    return '<span class="rounded-full px-2.5 py-0.5 text-xs font-bold ' . $class . '">' . htmlspecialchars($label) . '</span>';
};

$tableRows = [];
foreach ($expenses as $expense) {
    $id = (int) $expense['id'];
    $catKey = (string) $expense['category'];
    $catLabel = $categories[$catKey] ?? ucfirst(str_replace('_', ' ', $catKey));

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<button type="button" data-drawer-open="expense-drawer" data-expense-edit="' . $id . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 hover:text-primary" title="Edit">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.862 4.487 19.5 7.125M4.5 20.25l4.106-1.027a2.25 2.25 0 0 0 1.006-.596l9.32-9.32a2.25 2.25 0 0 0 0-3.182l-1.64-1.64a2.25 2.25 0 0 0-3.182 0l-9.32 9.32a2.25 2.25 0 0 0-.596 1.006L4.5 20.25Z"/></svg></button>'
        . '<button type="button" data-delete-trigger data-modal-id="expense-delete-modal" data-id="' . $id . '" data-name="' . htmlspecialchars((string) $expense['title'], ENT_QUOTES) . '" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50" title="Delete">&times;</button>'
        . '</div>';

    $tableRows[] = [
        'expense_number' => '<strong class="text-secondary">' . htmlspecialchars((string) $expense['expense_number']) . '</strong>',
        'title' => '<strong class="text-secondary">' . htmlspecialchars((string) $expense['title']) . '</strong>',
        'category' => '<span data-filter-match="' . htmlspecialchars($catKey) . '">' . htmlspecialchars($catLabel) . '</span>',
        'amount' => '<span class="font-semibold text-rose-600">LKR ' . number_format((float) $expense['amount'], 2) . '</span>',
        'expense_date' => htmlspecialchars((string) $expense['expense_date']),
        'status' => $statusBadge((string) $expense['status']),
        '_meta' => htmlspecialchars(json_encode([
            'id' => $id,
            'category' => $catKey,
            'title' => (string) $expense['title'],
            'description' => (string) ($expense['description'] ?? ''),
            'amount' => (float) $expense['amount'],
            'expense_date' => (string) $expense['expense_date'],
        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES),
        '_actions' => $actions,
    ];
}

$tableId = 'expenses-table';
$tableColumns = [
    ['key' => 'expense_number', 'label' => 'Reference', 'html' => true],
    ['key' => 'title', 'label' => 'Title', 'html' => true],
    ['key' => 'category', 'label' => 'Category', 'html' => true],
    ['key' => 'amount', 'label' => 'Amount', 'html' => true, 'type' => 'number'],
    ['key' => 'expense_date', 'label' => 'Date', 'type' => 'date'],
    ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false],
    ['key' => '_meta', 'label' => '', 'class' => 'hidden'],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No expenses yet. Add product purchases and other business costs.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
