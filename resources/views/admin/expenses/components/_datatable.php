<?php declare(strict_types=1);
$tableRows=[];
foreach($expenses as $expense){
 $id=(int)$expense['id'];
 $meta=htmlspecialchars(json_encode(['id'=>$id,'title'=>(string)$expense['title'],'amount'=>(float)$expense['amount'],'expense_date'=>(string)$expense['expense_date'],'paid_source'=>(string)($expense['paid_source']??'company_cash'),'category'=>(string)($expense['category']??'other'),'description'=>(string)($expense['description']??'')],JSON_UNESCAPED_UNICODE),ENT_QUOTES);
 $name=htmlspecialchars((string)$expense['title'],ENT_QUOTES);
 $actions='<div class="flex items-center justify-end gap-2">'
 .'<button type="button" data-drawer-open="expense-drawer" data-expense-view="'.$id.'" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-secondary/60 hover:text-primary" title="View"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button>'
 .'<button type="button" data-drawer-open="expense-drawer" data-expense-edit="'.$id.'" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-secondary/60 hover:text-primary" title="Edit"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.862 4.487 19.5 7.125M4.5 20.25l4.106-1.027a2.25 2.25 0 0 0 1.006-.596l9.32-9.32a2.25 2.25 0 0 0 0-3.182l-1.64-1.64a2.25 2.25 0 0 0-3.182 0l-9.32 9.32a2.25 2.25 0 0 0-.596 1.006L4.5 20.25Z"/></svg></button>'
 .'<button type="button" data-delete-trigger data-modal-id="expense-delete-modal" data-id="'.$id.'" data-name="'.$name.'" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-100 text-rose-600 hover:bg-rose-50" title="Delete"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3.75 6h16.5M9.75 11.25v6m4.5-6v6M5.25 6l.75 14.25h12L18.75 6m-10.5 0 .75-2.25h6L15.75 6"/></svg></button></div>';
 $tableRows[]=['title'=>'<strong class="text-secondary">'.htmlspecialchars((string)$expense['title']).'</strong>','amount'=>'<span class="font-semibold text-rose-600">LKR '.number_format((float)$expense['amount'],2).'</span>','expense_date'=>htmlspecialchars((string)$expense['expense_date']),'_meta'=>$meta,'_actions'=>$actions];
}
$tableId='expenses-table';
$tableColumns=[['key'=>'title','label'=>'Expense name','html'=>true],['key'=>'amount','label'=>'Amount','html'=>true,'type'=>'number'],['key'=>'expense_date','label'=>'Date','type'=>'date'],['key'=>'_meta','label'=>'','class'=>'hidden'],['key'=>'_actions','label'=>'Action','html'=>true,'sortable'=>false,'align'=>'right']];
$tableSortable=true;$tablePaginated=true;$tablePerPage=10;$tableZebra=false;$tableEmptyMessage='No expenses yet.';
require BASE_PATH.'/resources/views/components/base/datatable.php';
