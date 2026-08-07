<?php
declare(strict_types=1);

/**
 * @var array       $customer
 * @var array|null  $profile
 * @var array       $stats
 * @var array       $customerOrders
 * @var array       $addresses
 * @var array       $notifications
 * @var string      $tab
 * @var array       $tabs
 * @var bool        $editMode
 * @var array|null  $flash
 * @var string      $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$customerId = (int) $customer['id'];
$viewBase = '/admin/customers/view?id=' . $customerId;

$tabLabels = [
    'overview'      => 'Overview',
    'orders'        => 'Orders',
    'addresses'     => 'Addresses',
    'notifications' => 'Notifications',
];

$avatarPaths = [
    'avatar_1' => '/assets/images/avatars/avatar-1.svg',
    'avatar_2' => '/assets/images/avatars/avatar-2.svg',
    'avatar_3' => '/assets/images/avatars/avatar-3.svg',
    'avatar_4' => '/assets/images/avatars/avatar-4.svg',
    'avatar_5' => '/assets/images/avatars/avatar-5.svg',
    'avatar_6' => '/assets/images/avatars/avatar-6.svg',
];
$avatarUrl = $avatarPaths[(string) ($customer['avatar'] ?? 'avatar_1')] ?? $avatarPaths['avatar_1'];
$fullName = trim(((string) $customer['first_name']) . ' ' . ((string) ($customer['last_name'] ?? '')));
$status = (string) ($customer['status'] ?? 'active');

$statusBadge = static function (string $s): string {
    $class = match ($s) {
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'blocked' => 'bg-rose-50 text-rose-700 ring-rose-100',
        default => 'bg-slate-100 text-slate-600 ring-slate-200',
    };
    return '<span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 ' . $class . '">' . htmlspecialchars(ucfirst($s)) . '</span>';
};

$field = static function (string $label, string $value, bool $empty = false): void {
    ?>
    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400"><?= htmlspecialchars($label) ?></p>
        <p class="mt-1 text-sm font-semibold <?= $empty ? 'text-slate-400 italic' : 'text-secondary' ?>">
            <?= $empty ? '—' : htmlspecialchars($value) ?>
        </p>
    </div>
    <?php
};

$orderStatusBadge = static function (string $orderStatus): string {
    $class = match ($orderStatus) {
        'delivered', 'confirmed' => 'bg-emerald-50 text-emerald-700',
        'cancelled' => 'bg-rose-50 text-rose-700',
        default => 'bg-amber-50 text-amber-700',
    };
    return '<span class="rounded-full px-2 py-0.5 text-[10px] font-bold ' . $class . '">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $orderStatus))) . '</span>';
};
?>

<div class="space-y-5">
    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-4">
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="" class="h-16 w-16 shrink-0 rounded-full border-2 border-slate-200 bg-white p-0.5">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-bold text-secondary"><?= htmlspecialchars($fullName) ?></h1>
                    <?= $statusBadge($status) ?>
                </div>
                <p class="mt-1 text-sm text-slate-500">
                    <?= htmlspecialchars((string) $customer['email']) ?> ·
                    <?= htmlspecialchars((string) ($customer['phone'] ?: 'No phone')) ?> ·
                    Member since <?= htmlspecialchars(substr((string) ($customer['created_at'] ?? ''), 0, 10)) ?>
                </p>
            </div>
        </div>
        <a href="/admin/customers" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-secondary hover:bg-slate-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to customers
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-200 px-3 pt-3" aria-label="Customer sections">
            <?php foreach ($tabLabels as $key => $label): ?>
                <a href="<?= htmlspecialchars($viewBase . '&tab=' . $key) ?>"
                   class="shrink-0 rounded-t-xl px-4 py-2.5 text-sm font-bold transition <?= $tab === $key ? 'border border-b-white border-slate-200 bg-white text-primary -mb-px' : 'text-slate-500 hover:bg-slate-50 hover:text-secondary' ?>"
                   aria-current="<?= $tab === $key ? 'page' : 'false' ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-secondary"><?= htmlspecialchars($tabLabels[$tab] ?? 'Details') ?></h2>
                <?php if ($tab === 'overview'): ?>
                    <?php if (!$editMode): ?>
                        <a href="<?= htmlspecialchars($viewBase . '&tab=overview&mode=edit') ?>" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 px-4 py-2 text-sm font-bold text-primary hover:bg-primary/10 transition">Edit notes</a>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($viewBase . '&tab=overview') ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-secondary hover:bg-slate-50 transition">Cancel</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php
            match ($tab) {
                'orders'        => require __DIR__ . '/components/view/_orders.php',
                'addresses'     => require __DIR__ . '/components/view/_addresses.php',
                'notifications' => require __DIR__ . '/components/view/_notifications.php',
                default         => require __DIR__ . '/components/view/_overview.php',
            };
            ?>
        </div>
    </div>
</div>
