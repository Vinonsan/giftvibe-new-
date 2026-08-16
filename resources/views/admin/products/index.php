<?php
declare(strict_types=1);

/*
 * Admin / Products
 *
 * Thin page orchestrator. Filters, datatable and every form step live in
 * dedicated components under components/.
 */

?>

<div class="space-y-5">

    <!-- Flash notice -->
    <?php if ($flash): ?><span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'], ENT_QUOTES) ?>"></span><?php endif; ?>

    <!-- Page header -->
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Products</h1>
            <p class="mt-1 text-sm text-slate-500">Manage products and assign them to categories.</p>
        </div>
        <button
            type="button"
            data-drawer-open="product-drawer"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary transition cursor-pointer shadow-sm shadow-primary/20"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add product
        </button>
    </div>

    <!-- Filter bar -->
    <?php require __DIR__ . '/components/_filter.php'; ?>

    <?php require __DIR__ . '/components/_datatable.php'; ?>

</div>

<!-- ── Add / Edit drawer (stepper) ───────────────────── -->
<?php require __DIR__ . '/components/_drawer.php'; ?>

<!-- ── Delete confirmation modal ─────────────────────── -->
<?php
$deleteModalId    = 'product-delete-modal';
$deleteFormAction = '/admin/products';
$deleteItemLabel  = 'product';
$deleteCsrfToken  = $csrfToken;
require BASE_PATH . '/resources/views/components/modal/delete-confirm.php';
?>
