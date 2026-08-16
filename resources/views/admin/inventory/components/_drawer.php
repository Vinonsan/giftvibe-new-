<?php declare(strict_types=1);
$fc='w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10';
$editItem=$editItem??null;
ob_start(); ?>
<form id="inventory-item-form" method="post" action="/admin/inventory" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?=htmlspecialchars((string)$csrfToken)?>"><input type="hidden" name="action" value="save_item"><input type="hidden" name="id" value="<?=(int)($editItem['id']??0)?>">
    <label class="block"><span class="mb-1.5 block text-sm font-semibold text-secondary">Item *</span><input required name="name" maxlength="190" value="<?=htmlspecialchars((string)($editItem['name']??''))?>" class="<?=$fc?>" placeholder="Item name"></label>
    <?php if(!$editItem):?>
    <label class="block"><span class="mb-1.5 block text-sm font-semibold text-secondary">Product cost (LKR) *</span><input required type="number" min="0.01" step="0.01" name="cost_price" class="<?=$fc?>" placeholder="0.00"></label>
    <label class="block"><span class="mb-1.5 block text-sm font-semibold text-secondary">Number of stock *</span><input required type="number" min="1" step="1" name="quantity" class="<?=$fc?>" placeholder="1"></label>
    <?php else:?><input type="hidden" name="cost_price" value="<?=htmlspecialchars((string)$editItem['cost_price'])?>"><input type="hidden" name="quantity" value="<?=(int)$editItem['quantity']?>"><?php endif;?>
</form>
<?php
$drawerBody=(string)ob_get_clean();$drawerFooter='<button type="button" data-drawer-close class="rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-bold text-secondary hover:bg-slate-50">Cancel</button><button type="submit" form="inventory-item-form" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">'.($editItem?'Save changes':'Save item').'</button>';$drawerId='inventory-item-drawer';$drawerTitle=$editItem?'Edit inventory item':'Add inventory item';$drawerDescription=$editItem?'Update the inventory item name.':'Enter the item, product cost and opening stock.';$drawerTrigger='<button type="button" data-drawer-open="inventory-item-drawer" data-inventory-item-trigger class="hidden" tabindex="-1"></button>';$drawerSize='xl';require BASE_PATH.'/resources/views/components/base/drawer.php';
?>
<?php if($editItem):?><script>document.addEventListener('DOMContentLoaded',function(){document.querySelector('[data-inventory-item-trigger]')?.click();});</script><?php endif;?>
