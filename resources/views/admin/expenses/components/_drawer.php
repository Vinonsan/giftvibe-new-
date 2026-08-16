<?php declare(strict_types=1);
$fc='w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-secondary outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10';
ob_start(); ?>
<div id="expense-view" class="hidden space-y-4">
 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Expense name</p><h4 id="expense-view-title" class="mt-2 text-2xl font-bold text-secondary"></h4><span id="expense-view-source" class="mt-3 inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary"></span></div>
 <div class="rounded-2xl border border-slate-200 p-5"><p class="text-xs font-bold uppercase text-slate-400">Amount</p><p id="expense-view-amount" class="mt-2 text-xl font-bold text-secondary"></p></div>
</div>
<form id="expense-form" method="post" action="<?= htmlspecialchars(app_url('/admin/expenses')) ?>" class="space-y-5">
 <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)$csrfToken) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" id="expense-form-id" value="0"><input type="hidden" name="category" value="other"><input type="hidden" name="paid_source" id="expense-paid-source" value="company_cash">
 <div class="space-y-4">
  <label class="block"><span class="mb-1.5 block text-xs font-bold text-secondary">Expense name <span class="text-primary">*</span></span><input required name="title" id="expense-title" maxlength="190" class="<?= $fc ?>" placeholder="Enter expense name"></label>
  <label class="block"><span class="mb-1.5 block text-xs font-bold text-secondary">Money (LKR) <span class="text-primary">*</span></span><input required type="number" min="0.01" step="0.01" name="amount" id="expense-amount" class="<?= $fc ?>" placeholder="0.00"></label>
 </div>
</form>
<script>
document.addEventListener('DOMContentLoaded',function(){
 var meta={},form=document.getElementById('expense-form'),view=document.getElementById('expense-view'),cash=<?= json_encode((float)$cashOnHand) ?>,profit=<?= json_encode((float)$availableProfit) ?>;
 document.querySelectorAll('#expenses-table tbody tr[data-row]').forEach(function(row){var c=row.querySelector('td[data-key="_meta"]');if(c)try{var v=JSON.parse(c.textContent.trim());meta[String(v.id)]=v;}catch(e){}});
 function money(v){return 'LKR '+Number(v||0).toLocaleString('en-LK',{minimumFractionDigits:2,maximumFractionDigits:2});}
 function setTitle(t,d){var el=document.getElementById('expense-drawer-title');if(el)el.textContent=t;var p=el&&el.parentElement.querySelector('p');if(p)p.textContent=d||'';}
 function sync(source){var isProfit=source==='profit',toggle=document.getElementById('expense-profit-toggle');document.getElementById('expense-paid-source').value=isProfit?'profit':'company_cash';if(toggle)toggle.checked=isProfit;document.getElementById('expense-source-help').textContent=isProfit?'Available profit: '+money(profit):'Cash on hand: '+money(cash);}
 function fill(v){document.getElementById('expense-form-id').value=v.id||0;document.getElementById('expense-title').value=v.title||'';document.getElementById('expense-amount').value=v.amount||'';sync(v.paid_source||'company_cash');}
 function mode(kind,v){var isView=kind==='view';view.classList.toggle('hidden',!isView);form.classList.toggle('hidden',isView);document.getElementById('expense-save-btn').classList.toggle('hidden',isView);document.getElementById('expense-edit-btn').classList.toggle('hidden',!isView);document.getElementById('expense-profit-control').classList.toggle('hidden',isView);if(isView){document.getElementById('expense-edit-btn').dataset.id=v.id;document.getElementById('expense-view-title').textContent=v.title;document.getElementById('expense-view-amount').textContent=money(v.amount);document.getElementById('expense-view-source').textContent=v.paid_source==='profit'?'From profit':'From cash on hand';setTitle('Expense details','View expense information.');}else{fill(v||{});setTitle(v&&v.id?'Edit expense':'Add expense',v&&v.id?'Update and save the expense.':'Enter expense name and money.');document.getElementById('expense-save-btn').textContent=v&&v.id?'Save changes':'Save expense';}}
 document.addEventListener('click',function(e){var add=e.target.closest('[data-expense-add]'),vb=e.target.closest('[data-expense-view]'),eb=e.target.closest('[data-expense-edit]'),fe=e.target.closest('#expense-edit-btn');if(add)mode('edit',{paid_source:'company_cash'});else if(vb)mode('view',meta[vb.dataset.expenseView]);else if(eb)mode('edit',meta[eb.dataset.expenseEdit]);else if(fe)mode('edit',meta[fe.dataset.id]);});
 document.getElementById('expense-profit-toggle').addEventListener('change',function(){sync(this.checked?'profit':'company_cash');});
});
</script>
<?php $drawerBody=(string)ob_get_clean();
ob_start();
$toggleName='expense_profit_toggle';$toggleId='expense-profit-toggle';$toggleChecked=false;$toggleLabel='From profit';$toggleHint='';$toggleError='';$toggleSize='md';$toggleColor='primary';$toggleState='default';$toggleRequired=false;$toggleDisabled=false;$toggleAttributes=[];$toggleClass='';
require BASE_PATH.'/resources/views/components/base/toggle.php';
$profitToggle=(string)ob_get_clean();
$drawerFooter='<div id="expense-profit-control" class="mr-auto">'.$profitToggle.'<p id="expense-source-help" class="mt-1 text-xs text-slate-500"></p></div><button type="button" data-drawer-close class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-secondary hover:bg-slate-50">Cancel</button><button id="expense-edit-btn" type="button" class="hidden rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white">Edit</button><button id="expense-save-btn" type="submit" form="expense-form" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Save expense</button>';
$drawerId='expense-drawer';$drawerSide='right';$drawerSize='xl';$drawerTitle='Add expense';$drawerDescription='Enter expense name and money.';$drawerTrigger='<span class="hidden"></span>';$drawerStatic=false;$drawerCloseOnEsc=true;$drawerShowCloseButton=true;$drawerOverlay=true;$drawerHeaderBottom='';require BASE_PATH.'/resources/views/components/base/drawer.php'; ?>
