<?php declare(strict_types=1); ?>
<div class="space-y-5">
    <?php if ($flash): ?><span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'],ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'],ENT_QUOTES) ?>"></span><?php endif; ?>
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-xl font-bold text-secondary">Investments</h1><p class="mt-1 text-sm text-slate-500">Record money added as an investment. The amount increases cash on hand.</p></div>
        <button type="button" data-drawer-open="investment-drawer" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-secondary">Add investment</button>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-primary/15 bg-primary/[.04] p-5"><p class="text-[10px] font-bold uppercase tracking-wider text-primary">Total investments</p><p class="mt-2 text-2xl font-black text-secondary">LKR <?= number_format($totalInvestments,2) ?></p><p class="mt-1 text-xs text-slate-500">Total money added through investments</p></div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-5"><p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Current cash on hand</p><p class="mt-2 text-2xl font-black text-emerald-700">LKR <?= number_format($cashOnHand,2) ?></p><p class="mt-1 text-xs text-emerald-700/70">Sales + investments − external purchases − expenses</p></div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-4">Date</th><th class="px-5 py-4 text-right">Amount</th><th class="px-5 py-4 text-right">Action</th></tr></thead><tbody class="divide-y divide-slate-100">
        <?php foreach($investments as $investment): ?><tr><td class="px-5 py-4 text-slate-500"><?= htmlspecialchars((string)$investment['investment_date']) ?></td><td class="px-5 py-4 text-right font-bold text-emerald-700">+ LKR <?= number_format((float)$investment['amount'],2) ?></td><td class="px-5 py-4"><form method="post" action="/admin/investments" class="flex justify-end" onsubmit="return confirm('Delete this investment?')"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$investment['id'] ?>"><button class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button></form></td></tr><?php endforeach; ?>
        <?php if(!$investments): ?><tr><td colspan="3" class="px-5 py-14 text-center text-slate-400">No investments recorded yet.</td></tr><?php endif; ?>
        </tbody></table></div>
    </div>
</div>
<?php ob_start(); ?>
<form id="investment-form" method="post" action="/admin/investments" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"><input type="hidden" name="action" value="save">
    <label class="block"><span class="mb-1.5 block text-sm font-semibold text-secondary">Amount (LKR) *</span><input required type="number" min="0.01" step="0.01" name="amount" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-primary"></label>
</form>
<?php
$drawerBody=(string)ob_get_clean();
$drawerFooter='<button type="button" data-drawer-close class="rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-bold text-secondary hover:bg-slate-50">Cancel</button><button type="submit" form="investment-form" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Save investment</button>';
$drawerId='investment-drawer';$drawerSide='right';$drawerSize='xl';$drawerTitle='Add investment';$drawerDescription='The current date will be saved automatically.';$drawerTrigger='<span class="hidden"></span>';$drawerStatic=false;$drawerCloseOnEsc=true;$drawerShowCloseButton=true;$drawerOverlay=true;$drawerHeaderBottom='';
require BASE_PATH.'/resources/views/components/base/drawer.php';
?>
