<?php
declare(strict_types=1);
?>
<div class="space-y-5">

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Order Management</h1>
            <p class="mt-1 text-sm text-slate-500">Review orders, verify payments, and manage delivery tracking.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_filter.php'; ?>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_datatable.php'; ?>
    </div>

</div>

<?php require __DIR__ . '/components/_drawer.php'; ?>
