<?php declare(strict_types=1);
$monthNames=['01'=>'january','02'=>'february','03'=>'march','04'=>'april','05'=>'may','06'=>'june','07'=>'july','08'=>'august','09'=>'september','10'=>'october','11'=>'november','12'=>'december'];
$dateParts=static function(string $date)use($monthNames):array{$parts=explode('-',$date);return[$parts[0]??'',$monthNames[$parts[1]??'']??''];};
$eye='<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>';
$tableRows=[];
foreach($orderReports as $row){
 [$year,$month]=$dateParts((string)$row['date']);
 $meta=json_encode(['kind'=>'order','reference'=>(string)$row['order_number'],'name'=>(string)$row['customer_name'],'date'=>(string)$row['date'],'sales'=>(float)$row['sales'],'cost'=>(float)$row['cost'],'profit'=>(float)$row['profit'],'amount'=>(float)$row['sales']],JSON_UNESCAPED_UNICODE)?:'{}';
 $tableRows[]=['name'=>'<strong class="text-secondary">'.htmlspecialchars((string)$row['customer_name']).'</strong>','type'=>'<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Order cash in</span>','amount'=>'<strong class="text-emerald-700">+ LKR '.number_format((float)$row['sales'],2).'</strong>','date'=>'<span class="text-xs text-slate-500">'.htmlspecialchars((string)$row['date']).'</span>','year'=>$year,'month'=>$month,'_meta'=>$meta,'_attrs'=>['kind'=>'order','sales'=>(float)$row['sales'],'cost'=>(float)$row['cost'],'profit'=>(float)$row['profit']], '_actions'=>'<button type="button" data-drawer-open="finance-view-drawer" data-finance-view class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-secondary/60 hover:border-primary/30 hover:text-primary" title="View">'.$eye.'</button>'];
}
foreach($procurementReports as $row){
 $date=substr((string)$row['created_at'],0,10);[$year,$month]=$dateParts($date);
 $meta=json_encode(['kind'=>'purchase','reference'=>(string)($row['sku']?:('PRODUCT-'.(int)$row['product_id'])),'name'=>(string)$row['name'],'date'=>$date,'unit_cost'=>(float)$row['unit_cost'],'quantity'=>(int)$row['quantity'],'amount'=>(float)$row['amount']],JSON_UNESCAPED_UNICODE)?:'{}';
 $tableRows[]=['name'=>'<strong class="text-secondary">'.htmlspecialchars((string)$row['name']).'</strong>','type'=>'<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Product cash out</span>','amount'=>'<strong class="text-rose-600">− LKR '.number_format((float)$row['amount'],2).'</strong>','date'=>'<span class="text-xs text-slate-500">'.htmlspecialchars($date).'</span>','year'=>$year,'month'=>$month,'_meta'=>$meta,'_attrs'=>['kind'=>'purchase','sales'=>0,'cost'=>(float)$row['amount'],'profit'=>-(float)$row['amount']], '_actions'=>'<button type="button" data-drawer-open="finance-view-drawer" data-finance-view class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-secondary/60 hover:border-primary/30 hover:text-primary" title="View">'.$eye.'</button>'];
}
usort($tableRows,static fn(array $a,array $b):int=>strcmp(strip_tags((string)$b['date']),strip_tags((string)$a['date'])));
$tableId='finance-table';
$tableColumns=[['key'=>'name','label'=>'Name','html'=>true],['key'=>'type','label'=>'Transaction','html'=>true,'sortable'=>false],['key'=>'amount','label'=>'Amount','html'=>true,'type'=>'number'],['key'=>'date','label'=>'Date','html'=>true,'type'=>'date'],['key'=>'year','label'=>'Year','class'=>'hidden'],['key'=>'month','label'=>'Month','class'=>'hidden'],['key'=>'_meta','label'=>'','class'=>'hidden'],['key'=>'_actions','label'=>'Action','html'=>true,'sortable'=>false,'align'=>'right']];
$tableSortable=true;$tablePaginated=true;$tablePerPage=10;$tableZebra=false;$tableEmptyMessage='No cash transactions yet.';
require BASE_PATH.'/resources/views/components/base/datatable.php';
