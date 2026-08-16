<?php declare(strict_types=1); ?>
<div class="space-y-5">
 <?php if($flash): ?><span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'],ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'],ENT_QUOTES) ?>"></span><?php endif; ?>
 <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
  <div><h1 class="text-xl font-bold text-secondary">Expenses</h1><p class="mt-1 text-sm text-slate-500">Manage business expenses and personal profit withdrawals.</p></div>
  <button type="button" data-drawer-open="expense-drawer" data-expense-add class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-secondary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Add expense</button>
 </div>
 <div class="grid gap-4 md:grid-cols-3">
  <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Cash on hand</p><p class="mt-2 text-2xl font-black text-emerald-700">LKR <?= number_format((float)$cashOnHand,2) ?></p><p class="mt-1 text-xs text-emerald-700/70">Total actual cash currently available</p></div>
  <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Usable cash</p><p class="mt-2 text-2xl font-black text-amber-700">LKR <?= number_format((float)$usableCash,2) ?></p><p class="mt-1 text-xs text-amber-700/70">For business and order-related expenses</p></div>
  <div class="rounded-2xl border border-primary/15 bg-primary/5 p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wider text-primary">Available profit</p><p class="mt-2 text-2xl font-black text-primary">LKR <?= number_format((float)$availableProfit,2) ?></p><p class="mt-1 text-xs text-primary/70">Reserved for personal withdrawals</p></div>
 </div>
 <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><?php require __DIR__.'/components/_filter.php'; ?></div>
 <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><?php require __DIR__.'/components/_datatable.php'; ?></div>
</div>
<?php require __DIR__.'/components/_drawer.php';
$deleteModalId='expense-delete-modal';$deleteFormAction='/admin/expenses';$deleteItemLabel='expense';$deleteCsrfToken=$csrfToken;require BASE_PATH.'/resources/views/components/modal/delete-confirm.php';
?>
