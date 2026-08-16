<?php declare(strict_types=1);
$tableRows=[];
foreach($items as $item){
    $id=(int)$item['id'];$qty=(int)$item['quantity'];
    $actions='<div class="flex items-center justify-end gap-2">'
        .'<a href="/admin/inventory?view='.$id.'" title="View inventory" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
        .'<button type="button" data-drawer-open="inventory-adjust-drawer" data-adjust-id="'.$id.'" data-adjust-name="'.htmlspecialchars((string)$item['name'],ENT_QUOTES).'" data-adjust-cost="'.htmlspecialchars((string)$item['cost_price'],ENT_QUOTES).'" title="Add stock" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg></button>';
    if(empty($item['product_id']))$actions.='<button type="button" data-delete-trigger data-modal-id="inventory-delete-modal" data-id="'.$id.'" data-name="'.htmlspecialchars((string)$item['name'],ENT_QUOTES).'" title="Delete item" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0H4.772m3.478-.397V4.5A1.5 1.5 0 0 1 9.75 3h4.5a1.5 1.5 0 0 1 1.5 1.5v.893"/></svg></button>';
    $actions.='</div>';
    $tableRows[]=[
        'name'=>'<strong class="text-secondary">'.htmlspecialchars((string)$item['name']).'</strong>',
        'cost_price'=>'LKR '.number_format((float)$item['cost_price'],2),
        'quantity'=>'<span class="font-bold text-secondary">'.number_format($qty).'</span>',
        '_actions'=>$actions,
    ];
}
$tableId='inventory-table';
$tableColumns=[
    ['key'=>'name','label'=>'Item','html'=>true],
    ['key'=>'cost_price','label'=>'Product cost','type'=>'number'],
    ['key'=>'quantity','label'=>'Number of stock','html'=>true,'type'=>'number'],
    ['key'=>'_actions','label'=>'Action','html'=>true,'sortable'=>false,'align'=>'right'],
];
$tableSortable=true;$tablePaginated=true;$tablePerPage=10;$tableEmptyMessage='No inventory items yet.';
require BASE_PATH.'/resources/views/components/base/datatable.php';
?>
