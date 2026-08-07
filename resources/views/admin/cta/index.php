<?php
declare(strict_types=1);

$image=(string)($cta['image_path']??'');
$placements=['home_after_categories'=>'Home · After categories','home_between_products_combos'=>'Home · Between products & combos','about'=>'About page','contact'=>'Contact page'];
$renderInput=static function(array $options):void{
    $inputType=$options['type']??'text';$inputName=$options['name'];$inputId=$options['id']??$inputName;$inputValue=(string)($options['value']??'');$inputPlaceholder=$options['placeholder']??'';$inputLabel=$options['label']??'';$inputHint=$options['hint']??'';$inputError='';$inputSize=$options['size']??'lg';$inputState='default';$inputRequired=$options['required']??false;$inputAutocomplete='';$inputReadonly=false;$inputDisabled=false;$inputLeadingIcon='';$inputPrefix='';$inputTrailingIcon='';$inputSuffix=$options['suffix']??'';$inputAttributes=$options['attributes']??[];$inputClass=$options['class']??'';$inputWrapperClass=$options['wrapperClass']??'';
    require BASE_PATH.'/resources/views/components/base/input.php';
};
$renderButton=static function(array $options):void{
    $buttonLabel=$options['label']??'';$buttonVariant=$options['variant']??'solid';$buttonColor=$options['color']??'primary';$buttonSize=$options['size']??'md';$buttonType=$options['type']??'button';$buttonHref=$options['href']??'';$buttonName=$options['name']??'';$buttonValue=$options['value']??'';$buttonId=$options['id']??'';$buttonIcon=$options['icon']??'';$buttonIconTrailing=$options['iconTrailing']??'';$buttonIconOnly=false;$buttonFullWidth=$options['fullWidth']??false;$buttonDisabled=false;$buttonLoading=false;$buttonOnclick=$options['onclick']??'';$buttonClass=$options['class']??'';$buttonAttributes=$options['attributes']??[];
    require BASE_PATH.'/resources/views/components/base/button.php';
};
?>
<div class="space-y-6">
    <?php if($flash):?><div class="rounded-xl px-4 py-3 text-sm <?=$flash['type']==='error'?'bg-rose-50 text-rose-700':'bg-emerald-50 text-emerald-700'?>"><?=htmlspecialchars($flash['message'])?></div><?php endif;?>

    <div class="flex flex-col gap-3 rounded-2xl border border-primary/10 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-xl font-bold text-secondary">Page CTAs</h1><p class="mt-1 text-sm text-slate-500">Background colour, left-side content and right-side image banners.</p></div>
        <?php $renderButton(['label'=>'Add CTA','color'=>'secondary','size'=>'lg','attributes'=>['data-drawer-open'=>'cta-drawer']]); ?>
    </div>

    <div class="space-y-4">
        <div class="flex gap-3">
            <?php $renderInput(['name'=>'cta_search','type'=>'search','label'=>'','placeholder'=>'Search CTAs...','wrapperClass'=>'flex-1','attributes'=>['data-datatable-search'=>'','data-datatable-target'=>'ctas-table']]); ?>
            <?php $renderButton(['label'=>'Reset','variant'=>'outline','color'=>'secondary','size'=>'lg','attributes'=>['data-datatable-reset'=>'','data-datatable-target'=>'ctas-table']]); ?>
        </div>
        <?php
        $tableRows=[];
        foreach($ctas as $row){
            $id = (int)$row['id'];
            $editButton = '<a href="/admin/cta?edit=' . $id . '" title="View CTA" aria-label="View CTA" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary">'
                . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>';
            $deleteButton = '<form method="post" action="/admin/cta" class="inline-flex" onsubmit="return confirm(\'Delete this CTA?\')">'
                . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">'
                . '<input type="hidden" name="id" value="' . $id . '">'
                . '<input type="hidden" name="action" value="delete">'
                . '<button type="submit" title="Delete CTA" aria-label="Delete CTA" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50 hover:text-rose-600">'
                . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button></form>';
            
            $tableRows[]=['preview'=>'<div class="flex min-w-[240px] items-center gap-3"><img src="'.htmlspecialchars($row['image_path'],ENT_QUOTES).'" class="h-14 w-20 rounded-lg object-cover border border-primary/10" alt=""><strong class="line-clamp-2 text-secondary">'.htmlspecialchars($row['title']).'</strong></div>','placement'=>htmlspecialchars($placements[$row['placement']]??$row['placement']),'status'=>'<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold '.($row['status']==='active'?'bg-emerald-50 text-emerald-700':'bg-slate-100 text-slate-500').'">'.htmlspecialchars(ucfirst($row['status'])).'</span>','order'=>(int)$row['sort_order'],'_actions'=>'<div class="flex justify-end gap-2">'.$editButton.$deleteButton.'</div>'];
        }
        $tableId='ctas-table';$tableColumns=[['key'=>'preview','label'=>'CTA','html'=>true,'sortable'=>false],['key'=>'placement','label'=>'Placement'],['key'=>'status','label'=>'Status','html'=>true,'sortable'=>false],['key'=>'order','label'=>'Order','type'=>'number'],['key'=>'_actions','label'=>'Action','html'=>true,'sortable'=>false,'align'=>'right']];$tableSortable=true;$tableDefaultSort=['key'=>'order','dir'=>'asc'];$tablePaginated=true;$tablePerPage=10;$tableZebra=false;$tableEmptyMessage='No CTAs found.';require BASE_PATH.'/resources/views/components/base/datatable.php';
        ?>
    </div>
</div>

<?php ob_start(); ?>
        <form id="cta-form" method="post" action="/admin/cta" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrfToken)?>"><?php if($cta):?><input type="hidden" name="id" value="<?=(int)$cta['id']?>"><?php endif;?>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php $selectName='placement';$selectId='cta-placement';$selectOptions=$placements;$selectValue=(string)($cta['placement']??'home_between_products_combos');$selectPlaceholder='Choose placement';$selectLabel='Placement';$selectHint='Select where this CTA should appear.';$selectError='';$selectSize='lg';$selectState='default';$selectMultiple=false;$selectRequired=true;$selectDisabled=false;$selectAttributes=[];$selectClass='';require BASE_PATH.'/resources/views/components/base/select.php'; ?>
                <?php $renderInput(['name'=>'badge','label'=>'Badge','value'=>$cta['badge']??'','placeholder'=>'New collection','attributes'=>['maxlength'=>80]]); ?>
            </div>
            <?php $renderInput(['name'=>'title','label'=>'Title','value'=>$cta['title']??'','placeholder'=>'Make every moment unforgettable','required'=>true,'attributes'=>['maxlength'=>190]]); ?>
            <label class="block space-y-1.5"><span class="block text-sm font-medium text-secondary">Description</span><textarea name="description" rows="3" maxlength="500" class="w-full rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-base text-secondary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Short supporting content..."><?=htmlspecialchars((string)($cta['description']??''))?></textarea></label>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php $renderInput(['name'=>'button_label','label'=>'Button label (optional)','value'=>$cta['button_label']??'','placeholder'=>'Shop now']); ?>
                <?php $renderInput(['name'=>'link_url','type'=>'text','label'=>'Button link','value'=>$cta['link_url']??'','placeholder'=>'/shop','hint'=>'Use a site path such as /shop or a full URL.']); ?>
            </div>
            <?php $fileName='cta_image';$fileId='cta-image';$fileLabel='Right-side image';$fileHint='JPG, PNG or WebP · maximum 8MB';$fileAccept='image/png,image/jpeg,image/webp';$fileRequired=$cta===null;$fileCurrentUrl=$image;$fileMultiple=false;require BASE_PATH.'/resources/views/components/base/file-input.php';?>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-1.5"><span class="block text-sm font-medium text-secondary">Background colour</span><input type="color" name="background_color" value="<?=htmlspecialchars((string)($cta['background_color']??'#0B182E'))?>" class="h-11 w-full rounded-xl border border-primary/10 bg-white p-1"><span class="block text-xs text-slate-500">Base theme colour: #0B182E.</span></label>
                <?php $renderInput(['name'=>'sort_order','type'=>'number','label'=>'Sort order','value'=>$cta['sort_order']??0]); ?>
            </div>
            <?php $toggleName='status';$toggleId='cta-status';$toggleChecked=!$cta||$cta['status']==='active';$toggleLabel='Active';$toggleHint='Show this CTA on the selected page.';$toggleError='';$toggleSize='md';$toggleColor='primary';$toggleState='default';$toggleRequired=false;$toggleDisabled=false;$toggleAttributes=['value'=>'active'];$toggleClass='';require BASE_PATH.'/resources/views/components/base/toggle.php'; ?>
        </form>
<?php $drawerBody=(string)ob_get_clean();ob_start();$renderButton(['label'=>'Cancel','variant'=>'outline','color'=>'secondary','size'=>'lg','attributes'=>['data-drawer-close'=>'']]);$renderButton(['label'=>$cta?'Update CTA':'Create CTA','type'=>'submit','size'=>'lg','attributes'=>['form'=>'cta-form']]);$drawerFooter=(string)ob_get_clean();$drawerId='cta-drawer';$drawerSide='right';$drawerSize='lg';$drawerTitle=$cta?'View & edit CTA':'Add CTA';$drawerDescription='Manage left content, background colour and right-side image.';$drawerTrigger='<span class="hidden"></span>';$drawerStatic=false;$drawerCloseOnEsc=true;$drawerShowCloseButton=true;$drawerOverlay=true;require BASE_PATH.'/resources/views/components/base/drawer.php';?>
<?php if($cta):?><script>document.addEventListener('DOMContentLoaded',function(){document.querySelector('[data-drawer-open="cta-drawer"]')?.click();});</script><?php endif;?>
