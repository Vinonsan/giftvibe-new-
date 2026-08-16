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
    $orderMeta = $order;
    $orderMeta['items'] = $orderItemsByOrder[$id] ?? [];
    // Datatable escapes plain cell values while rendering. Keeping JSON raw here
    // avoids double-encoding quotes, which prevented the drawer from parsing it.
    $metaJson = json_encode($orderMeta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<button type="button" data-drawer-open="order-view-drawer" data-order-view data-order-target="summary" data-order-id="' . $id . '" title="View order details" aria-label="View order details" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:border-primary/30 hover:bg-primary/5 hover:text-primary">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></button>';
    $actions .= '<button type="button" data-drawer-open="order-view-drawer" data-order-view data-order-target="tracking" data-order-id="' . $id . '" title="Order status and tracking" aria-label="Order status and tracking" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:border-primary/30 hover:bg-primary/5 hover:text-primary">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h10.5v10.5H3.75zM14.25 9.75h3l3 3v4.5h-6z"/><circle cx="7.5" cy="18" r="1.5"/><circle cx="17.25" cy="18" r="1.5"/></svg></button>';

    $actions .= '</div>';

    $tableRows[] = [
        'customer' => '<strong class="text-secondary">' . htmlspecialchars((string) $order['customer_name']) . '</strong><small class="block text-slate-400">' . htmlspecialchars((string) $order['customer_phone']) . '</small>',
        'amount' => '<span class="font-semibold text-secondary">LKR ' . number_format((float) $order['grand_total'], 2) . '</span>',
        'payment_verification' => $statusBadge($pStatus, 'payment'),
        'status' => $statusBadge($oStatus, 'order'),
        '_meta' => $metaJson,
        '_actions' => $actions,
    ];
}

$tableId = 'orders-table';
$tableColumns = [
    ['key' => 'customer', 'label' => 'Customer', 'html' => true],
    ['key' => 'amount', 'label' => 'Total', 'html' => true, 'type' => 'number'],
    ['key' => 'payment_verification', 'label' => 'Payment', 'html' => true, 'sortable' => false],
    ['key' => 'status', 'label' => 'Order Status', 'html' => true, 'sortable' => false],
    ['key' => '_meta', 'label' => '', 'class' => 'hidden'],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No orders found.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
