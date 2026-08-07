<?php
declare(strict_types=1);
?>
<div class="space-y-5">

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string) $flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Customer Directory</h1>
            <p class="mt-1 text-sm text-slate-500">View customer profiles, order history, and account status.</p>
        </div>
        <button type="button" data-drawer-open="customer-drawer"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary transition shadow-sm shadow-primary/20">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add customer
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_filter.php'; ?>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_datatable.php'; ?>
    </div>

</div>

<?php require __DIR__ . '/components/_drawer.php'; ?>
