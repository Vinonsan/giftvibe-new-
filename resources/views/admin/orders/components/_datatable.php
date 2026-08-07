<?php
declare(strict_types=1);

$statusBadge = static function (string $status, string $type = 'order'): string {
    $label = ucfirst(str_replace('_', ' ', $status));
    if ($type === 'payment') {
        $class = match ($status) {
            'paid' => 'bg-emerald-50 text-emerald-700',
            'failed', 'cancelled' => 'bg-rose-50 text-rose-700',
            default => 'bg-amber-50 text-amber-700',
        };
    } else {
        $class = match ($status) {
            'delivered', 'confirmed' => 'bg-emerald-50 text-emerald-700',
            'cancelled', 'refunded' => 'bg-rose-50 text-rose-700',
            default => 'bg-amber-50 text-amber-700',
        };
    }
    return '<span class="rounded-full px-2.5 py-0.5 text-xs font-bold ' . $class . '">' . htmlspecialchars($label) . '</span>';
};

$tableRows = [];
foreach ($orders as $order) {
    $id = (int) $order['id'];
    $oStatus = (string) $order['order_status'];
    $pStatus = (string) $order['payment_status'];

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<a href="/admin/orders/view?id=' . $id . '" title="View order" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:text-primary">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>';

    if (in_array($oStatus, ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'], true)) {
        $actions .= '<a href="/admin/orders/receipt?id=' . $id . '" target="_blank" title="Print receipt" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:text-primary">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75m0 0a1.5 1.5 0 0 1-3 0h6a1.5 1.5 0 0 1-3 0Z"/></svg></a>';
    }

    $actions .= '</div>';

    $tableRows[] = [
        'order_number' => '<strong class="text-secondary">' . htmlspecialchars((string) $order['order_number']) . '</strong>',
        'customer' => '<strong class="text-secondary">' . htmlspecialchars((string) $order['customer_name']) . '</strong><small class="block text-slate-400">' . htmlspecialchars((string) $order['customer_phone']) . '</small>',
        'amount' => '<span class="font-semibold text-secondary">LKR ' . number_format((float) $order['grand_total'], 2) . '</span>',
        'payment_verification' => $statusBadge($pStatus, 'payment'),
        'status' => $statusBadge($oStatus, 'order'),
        '_actions' => $actions,
    ];
}

$tableId = 'orders-table';
$tableColumns = [
    ['key' => 'order_number', 'label' => 'Order #', 'html' => true],
    ['key' => 'customer', 'label' => 'Customer', 'html' => true],
    ['key' => 'amount', 'label' => 'Total', 'html' => true, 'type' => 'number'],
    ['key' => 'payment_verification', 'label' => 'Payment', 'html' => true, 'sortable' => false],
    ['key' => 'status', 'label' => 'Order Status', 'html' => true, 'sortable' => false],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No orders found.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
