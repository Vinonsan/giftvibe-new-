<?php
declare(strict_types=1);

$avatarPaths = [];
foreach (['giftvibe-1','giftvibe-2','giftvibe-3','giftvibe-4','giftvibe-5','giftvibe-6'] as $avatarSeed) $avatarPaths[$avatarSeed] = dicebear_avatar_url($avatarSeed);

$statusBadge = static function (string $status): string {
    $class = match ($status) {
        'active' => 'bg-emerald-50 text-emerald-700',
        'blocked' => 'bg-rose-50 text-rose-700',
        default => 'bg-slate-100 text-slate-600',
    };
    return '<span class="rounded-full px-2.5 py-0.5 text-xs font-bold ' . $class . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
};

$tableRows = [];
foreach ($customers as $c) {
    $id = (int) $c['id'];
    $avatarKey = (string) ($c['avatar'] ?? 'giftvibe-1');
    $avatarUrl = dicebear_avatar_url($avatarKey);
    $fullName = trim(((string) $c['first_name']) . ' ' . ((string) ($c['last_name'] ?? '')));
    $ordersCount = (int) ($c['orders_count'] ?? 0);
    $lifetime = (float) ($c['lifetime_total'] ?? 0);
    $status = (string) ($c['status'] ?? 'active');

    $cityLine = trim(((string) ($c['city'] ?? '')) . (($c['district'] ?? '') ? ', ' . $c['district'] : ''));

    $actions = '<div class="flex items-center justify-end gap-2">'
        . '<a href="/admin/customers/view?id=' . $id . '" title="View customer" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:text-primary">'
        . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
        . '</div>';

    $tableRows[] = [
        'avatar' => '<img src="' . htmlspecialchars($avatarUrl, ENT_QUOTES) . '" alt="" class="h-10 w-10 rounded-full border border-slate-200 bg-white p-0.5">',
        'name' => '<div><p class="font-semibold text-secondary">' . htmlspecialchars($fullName) . '</p><p class="text-[11px] text-slate-400">ID #' . $id . '</p></div>',
        'contact' => '<p class="font-semibold text-secondary">' . htmlspecialchars((string) $c['email']) . '</p><p class="text-xs text-slate-400">' . htmlspecialchars((string) ($c['phone'] ?: '—')) . '</p>',
        'city' => '<span class="text-sm text-slate-600">' . htmlspecialchars($cityLine !== '' ? $cityLine : '—') . '</span>',
        'orders_count' => '<span class="font-semibold text-secondary">' . $ordersCount . '</span>',
        'lifetime' => '<span class="font-semibold text-secondary">LKR ' . number_format($lifetime, 2) . '</span>',
        'status' => $statusBadge($status),
        '_actions' => $actions,
    ];
}

$tableId = 'customers-table';
$tableColumns = [
    ['key' => 'avatar', 'label' => '', 'html' => true, 'sortable' => false, 'width' => 'w-14'],
    ['key' => 'name', 'label' => 'Customer', 'html' => true],
    ['key' => 'contact', 'label' => 'Contact', 'html' => true],
    ['key' => 'city', 'label' => 'Location', 'html' => true],
    ['key' => 'orders_count', 'label' => 'Orders', 'html' => true, 'type' => 'number'],
    ['key' => 'lifetime', 'label' => 'Lifetime spend', 'html' => true, 'type' => 'number'],
    ['key' => 'status', 'label' => 'Status', 'html' => true, 'sortable' => false],
    ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
];
$tableSortable = true;
$tablePaginated = true;
$tablePerPage = 10;
$tableZebra = false;
$tableEmptyMessage = 'No customers found.';
require BASE_PATH . '/resources/views/components/base/datatable.php';
