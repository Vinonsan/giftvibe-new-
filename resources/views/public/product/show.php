<?php
declare(strict_types=1);
$productName=(string)$product['name'];
$basePrice=(float)$product['base_price'];
$salePrice=null;
$hasSale=false;
$currentPrice=$basePrice;
$primaryImage=(string)($product['image_path']??'/assets/images/hero_slide_1.jpg');
$normalize=static function(string $path):string { if(str_starts_with($path,'public/'))return '/'.substr($path,7); if($path!==''&&!str_starts_with($path,'/')&&!str_starts_with($path,'http'))return '/'.$path; return $path; };
$primaryImage=$normalize($primaryImage);
$images=[$primaryImage];
foreach($gallery as $galleryImage){$path=$normalize((string)$galleryImage['image_path']);if($path!==''&&!in_array($path,$images,true))$images[]=$path;}
$isMadeToOrder=(string)($product['procurement_type']??'')==='handcrafted';
$inStock=$isMadeToOrder||(int)$product['stock_quantity']>0;
$detailBackUrl=(string)($detailBackUrl??'/shop');
$relatedHeading=(string)($relatedHeading??'You may also like');
$relatedDescription=(string)($relatedDescription??'');
$itemType=(string)($product['_type']??'product');
$productVariants=$productVariants??[];
$productSocialLinks=$productSocialLinks??[];
$videos=[];
foreach(($productVideos??[]) as $videoUrl){$videoUrl=trim((string)$videoUrl);if($videoUrl==='')continue;$youtubeId='';if(preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i',$videoUrl,$videoMatch))$youtubeId=$videoMatch[1];$videos[]=['url'=>$videoUrl,'youtube_id'=>$youtubeId];}
$socialEmbeds=[];
foreach($productSocialLinks as $socialLink){
    $url=trim((string)($socialLink['url']??''));
    $platform=strtolower((string)($socialLink['platform']??'other'));
    $embedUrl='';
    $portrait=false;
    if($platform==='youtube'&&preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~i',$url,$match)){
        $embedUrl='https://www.youtube-nocookie.com/embed/'.$match[1];
    }elseif($platform==='facebook'&&preg_match('~facebook\.com/.+/(?:videos|reel)/~i',$url)){
        $embedUrl='https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=false';
    }elseif($platform==='instagram'&&preg_match('~instagram\.com/(?:p|reel|tv)/([A-Za-z0-9_-]+)~i',$url,$match)){
        $embedUrl='https://www.instagram.com/p/'.$match[1].'/embed/';
        $portrait=true;
    }elseif($platform==='tiktok'&&preg_match('~tiktok\.com/@[^/]+/video/(\d+)~i',$url,$match)){
        $embedUrl='https://www.tiktok.com/player/v1/'.$match[1].'?autoplay=0&loop=0';
        $portrait=true;
    }
    $socialEmbeds[]=['platform'=>$platform,'url'=>$url,'embed_url'=>$embedUrl,'portrait'=>$portrait];
}
?>
<div data-product-detail class="mx-auto max-w-7xl py-6">
    <button type="button" onclick="goToPreviousPage()" class="mb-6 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-secondary shadow-sm transition hover:border-primary hover:text-primary" aria-label="Go back to previous page">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
        Back
    </button>
    <nav class="mb-7 flex items-center gap-2 text-xs font-semibold text-slate-400" aria-label="Breadcrumb"><a href="/" class="hover:text-primary">Home</a><span>/</span><a href="/shop" class="hover:text-primary">Shop</a><span>/</span><span class="truncate text-secondary"><?=htmlspecialchars($productName)?></span></nav>

    <div class="grid gap-10 lg:grid-cols-2 lg:gap-14">
        <div>
            <button type="button" onclick="openLightbox(currentGalleryIndex)" class="relative block aspect-square w-full cursor-zoom-in overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-sm" aria-label="Open product image fullscreen"><img id="main-product-image" src="<?=htmlspecialchars($primaryImage)?>" alt="<?=htmlspecialchars($productName)?>" class="h-full w-full object-cover transition-opacity duration-200"><span class="absolute bottom-4 right-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white backdrop-blur-sm"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M8.25 3.75h-4.5v4.5m12-4.5h4.5v4.5m0 7.5v4.5h-4.5m-7.5 0h-4.5v-4.5"/></svg></span></button>
            <?php if(count($images)>1):?><div class="mt-4 flex gap-3 overflow-x-auto pb-1"><?php foreach($images as $index=>$image):?><button type="button" data-product-thumb onclick="swapMainImage('<?=htmlspecialchars($image,ENT_QUOTES)?>',this)" class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border-2 <?=$index===0?'border-primary':'border-slate-200'?>"><img src="<?=htmlspecialchars($image)?>" alt="" class="h-full w-full object-cover"></button><?php endforeach;?></div><?php endif;?>
        </div>

        <div class="flex flex-col justify-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary"><?=htmlspecialchars((string)($product['category_names']??'GiftVibe collection'))?></p>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl lg:text-5xl"><?=htmlspecialchars($productName)?></h1>
            <div class="mt-4 flex items-center gap-2 text-sm font-semibold <?=$inStock?'text-emerald-600':'text-rose-600'?>"><span class="h-2 w-2 rounded-full <?=$inStock?'bg-emerald-500':'bg-rose-500'?>"></span><?=$isMadeToOrder?'Made to order':($inStock?'In stock · Ready to deliver':'Out of stock')?></div>
            <div class="mt-7 flex flex-wrap items-baseline gap-3"><span class="text-3xl font-extrabold text-secondary">LKR <?=number_format($currentPrice,2)?></span><?php if($hasSale):?><span class="text-base text-slate-400 line-through">LKR <?=number_format($basePrice,2)?></span><?php endif;?></div>
            <p class="mt-6 text-base leading-7 text-slate-600"><?=nl2br(htmlspecialchars((string)($product['short_description']?:'A thoughtful GiftVibe selection, carefully prepared for a memorable celebration.')))?></p>
            
            <?php if($itemType==='product' && ($productOptions ?? [])):?>
                <div class="mt-6 space-y-5" id="custom-options-container">
                    <?php foreach($productOptions as $opt): ?>
                        <fieldset class="custom-option-group" data-option-id="<?=(int)$opt['id']?>" data-required="<?=$opt['is_required']?'true':'false'?>">
                            <legend class="text-sm font-bold text-secondary"><?=htmlspecialchars((string)$opt['name'])?> <?=$opt['is_required']?'<span class="text-rose-500">*</span>':''?></legend>
                            <?php if ($opt['type'] === 'image_select'): ?>
                                <div class="mt-3 flex flex-wrap gap-3">
                                    <?php foreach($opt['values'] as $index => $val): ?>
                                        <?php $valPrice = (float) $val['price_adjustment']; ?>
                                        <label class="cursor-pointer">
                                            <input class="peer sr-only custom-option-input" type="radio" name="option_<?= $opt['id'] ?>" value="<?= (int) $val['id'] ?>" data-option-id="<?= (int) $opt['id'] ?>" data-option-name="<?= htmlspecialchars($opt['name'], ENT_QUOTES) ?>" data-val-label="<?= htmlspecialchars($val['label'], ENT_QUOTES) ?>" data-val-price="<?= $valPrice ?>" <?= $opt['is_required'] && $index === 0 ? 'checked' : '' ?>>
                                            <span class="block overflow-hidden rounded-xl border-2 border-slate-200 bg-white p-1 peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 transition group">
                                                <img src="<?= htmlspecialchars($normalize((string) $val['image_path'])) ?>" alt="<?= htmlspecialchars((string) $val['label']) ?>" class="h-16 w-16 md:h-20 md:w-20 rounded-lg object-cover group-hover:opacity-90">
                                            </span>
                                            <span class="mt-2 block text-center text-[10px] font-bold uppercase tracking-wide text-secondary"><?= htmlspecialchars((string) $val['label']) ?></span>
                                            <?php if($valPrice > 0): ?><span class="block text-center text-[10px] text-slate-400">+LKR <?=number_format($valPrice)?></span><?php endif; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="mt-3 text-sm italic text-slate-400">Option type unsupported in UI currently.</div>
                            <?php endif; ?>
                        </fieldset>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if(trim((string)($product['description']??''))!==''):?>
                <div class="mt-5 border-t border-slate-200 pt-5">
                    <h2 class="text-sm font-bold text-secondary">Product details</h2>
                    <div class="relative mt-3" id="product-description-container">
                        <div id="product-description-content" class="line-clamp-4 whitespace-pre-line text-sm leading-6 text-slate-600 transition-all duration-300 overflow-hidden relative">
                            <?=htmlspecialchars((string)$product['description'])?>
                            <div id="product-description-fade" class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                        </div>
                        <button type="button" onclick="
                            var content = document.getElementById('product-description-content');
                            var fade = document.getElementById('product-description-fade');
                            if (content.classList.contains('line-clamp-4')) {
                                content.classList.remove('line-clamp-4');
                                fade.classList.add('hidden');
                                this.innerHTML = 'Read less &uarr;';
                            } else {
                                content.classList.add('line-clamp-4');
                                fade.classList.remove('hidden');
                                this.innerHTML = 'Read more &darr;';
                            }
                        " class="mt-2 text-xs font-bold text-primary hover:underline">Read more &darr;</button>
                    </div>
                </div>
            <?php endif;?>


            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="flex h-12 items-center justify-between rounded-xl border border-slate-200 bg-white px-2 sm:w-36"><button type="button" onclick="adjustQuantity(-1)" class="h-9 w-9 rounded-lg text-xl text-slate-500 hover:bg-slate-100">−</button><input id="product-quantity" value="1" readonly class="w-10 border-0 bg-transparent text-center font-bold text-secondary outline-none"><button type="button" onclick="adjustQuantity(1)" class="h-9 w-9 rounded-lg text-xl text-slate-500 hover:bg-slate-100">+</button></div>
                    <button type="button" onclick='detailAddCart(<?= htmlspecialchars(json_encode(['name'=>$productName,'slug'=>(string)$product['slug'],'type'=>$itemType,'price'=>$currentPrice,'image'=>$primaryImage], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT), ENT_QUOTES) ?>)' <?=$inStock?'':'disabled'?> class="h-12 flex-1 rounded-xl border border-primary bg-white px-5 text-sm font-bold text-primary transition hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-40">Add to cart</button>
                    <button type="button" onclick="detailBuyNow('<?=htmlspecialchars((string)$product['slug'],ENT_QUOTES)?>','<?=htmlspecialchars($itemType,ENT_QUOTES)?>')" <?=$inStock?'':'disabled'?> class="h-12 flex-1 rounded-xl bg-primary px-5 text-sm font-bold text-white transition hover:bg-secondary disabled:cursor-not-allowed disabled:opacity-40">Buy now</button>
                </div>
            </div>

            <div class="mt-7 grid grid-cols-3 divide-x divide-slate-200 rounded-2xl border border-slate-200 bg-white py-4 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500"><span>Islandwide delivery</span><span>Quality checked</span><span>Secure checkout</span></div>
        </div>
    </div>

    <?php if($videos):?>
        <section class="mt-12 border-t border-slate-200 pt-10" aria-labelledby="product-video-heading">
            <div class="mb-5 flex items-center gap-3"><span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-rose-600 text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span><div><p class="text-xs font-bold uppercase tracking-wider text-rose-600">Video reference</p><h2 id="product-video-heading" class="text-xl font-extrabold text-secondary">See this gift in detail</h2></div></div>
            <div class="grid gap-5 lg:grid-cols-2"><?php foreach($videos as $index=>$video):?><?php if($video['youtube_id']!==''):?><div class="aspect-video overflow-hidden rounded-3xl border border-slate-200 bg-black shadow-lg relative group cursor-pointer" onclick="this.innerHTML='<iframe src=\'https://www.youtube-nocookie.com/embed/<?=htmlspecialchars($video['youtube_id'])?>?autoplay=1\' class=\'h-full w-full\' allow=\'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\' allowfullscreen></iframe>'"><img src="https://i.ytimg.com/vi/<?=htmlspecialchars($video['youtube_id'])?>/hqdefault.jpg" alt="Video cover" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"><div class="absolute inset-0 flex items-center justify-center bg-black/20 transition-colors group-hover:bg-transparent pointer-events-none"><div class="flex h-16 w-16 items-center justify-center rounded-full bg-rose-600/90 text-white backdrop-blur-sm shadow-xl transition-transform group-hover:scale-110"><svg class="h-8 w-8 ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></div></div></div><?php else:?><a href="<?=htmlspecialchars($video['url'])?>" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-28 items-center justify-center gap-2 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-600 hover:text-white">Open product video <?=($index+1)?> <span aria-hidden="true">&rarr;</span></a><?php endif;?><?php endforeach;?></div>
        </section>
    <?php endif;?>

    <?php if (!empty($productSocialLinks)): ?>
        <section class="mt-12 border-t border-slate-200 pt-10" aria-labelledby="product-social-heading">
            <div class="mb-6 flex items-center gap-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Connect with us</p>
                    <h2 id="product-social-heading" class="text-2xl font-extrabold text-secondary">Social &amp; Website Links</h2>
                </div>
            </div>
            <div class="grid items-start gap-5 md:grid-cols-2">
                <?php foreach ($socialEmbeds as $embed): ?>
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3">
                            <span class="text-xs font-black uppercase tracking-widest text-primary"><?=htmlspecialchars($embed['platform'])?></span>
                            <a href="<?=htmlspecialchars($embed['url'])?>" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-slate-500 hover:text-primary">Open post &nearr;</a>
                        </div>
                        <?php if($embed['embed_url']!==''):?>
                            <div class="<?=$embed['portrait']?'mx-auto aspect-[9/16] max-h-[680px] max-w-sm':'aspect-video'?> w-full bg-black">
                                <iframe src="<?=htmlspecialchars($embed['embed_url'])?>" title="<?=htmlspecialchars(ucfirst($embed['platform']))?> video" class="h-full w-full border-0" loading="lazy" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        <?php else:?>
                            <a href="<?=htmlspecialchars($embed['url'])?>" target="_blank" rel="noopener noreferrer" class="flex min-h-36 items-center justify-center p-6 text-center text-sm font-bold text-primary hover:bg-blue-50">This link is not a video post. Open <?=htmlspecialchars(ucfirst($embed['platform']))?> &rarr;</a>
                        <?php endif;?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php if($relatedProducts):?><section class="mt-16 border-t border-slate-200 pt-12"><h2 class="text-2xl font-extrabold text-secondary"><?=htmlspecialchars($relatedHeading)?></h2><?php if($relatedDescription!==''):?><p class="mt-2 text-sm text-slate-500"><?=htmlspecialchars($relatedDescription)?></p><?php endif;?><div class="mt-7 grid grid-cols-2 gap-4 md:grid-cols-4"><?php foreach($relatedProducts as $product)require BASE_PATH.'/resources/views/components/base/product-card.php';?></div></section><?php endif;?>
</div>

<div id="product-lightbox" class="pointer-events-none fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-4 opacity-0 transition-opacity" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Product image gallery">
    <button type="button" onclick="closeLightbox()" class="absolute right-5 top-5 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20" aria-label="Close fullscreen gallery">&times;</button>
    <?php if(count($images)>1):?><button type="button" onclick="moveLightbox(-1)" class="absolute left-4 top-1/2 z-10 inline-flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20" aria-label="Previous image">&#8249;</button><?php endif;?>
    <img id="lightbox-image" src="<?=htmlspecialchars($primaryImage)?>" alt="<?=htmlspecialchars($productName)?>" class="max-h-[90vh] max-w-[92vw] rounded-xl object-contain shadow-2xl">
    <?php if(count($images)>1):?><button type="button" onclick="moveLightbox(1)" class="absolute right-4 top-1/2 z-10 inline-flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20" aria-label="Next image">&#8250;</button><?php endif;?>
    <p id="lightbox-counter" class="absolute bottom-5 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white"></p>
</div>
<script>
var productGalleryImages=<?=json_encode(array_values($images),JSON_UNESCAPED_SLASHES|JSON_HEX_TAG)?>;
var currentGalleryIndex=0;
var selectedProductImage=<?=json_encode($primaryImage,JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
function displayImage(src){if(!src)return;var image=document.getElementById('main-product-image');if(image)image.src=src;selectedProductImage=src;}
function goToPreviousPage(){if(document.referrer&&new URL(document.referrer).host===window.location.host){window.history.back();}else{window.location.href=<?=json_encode($detailBackUrl,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT)?>;}}
function swapMainImage(src,button){currentGalleryIndex=Math.max(0,productGalleryImages.indexOf(src));selectedProductImage=src;var image=document.getElementById('main-product-image');image.style.opacity='0';setTimeout(function(){image.src=src;image.style.opacity='1';},150);document.querySelectorAll('[data-product-thumb]').forEach(function(item){item.classList.remove('border-primary');item.classList.add('border-slate-200');});button.classList.remove('border-slate-200');button.classList.add('border-primary');}
function updateLightbox(){document.getElementById('lightbox-image').src=productGalleryImages[currentGalleryIndex];document.getElementById('lightbox-counter').textContent=(currentGalleryIndex+1)+' / '+productGalleryImages.length;}
function openLightbox(index){currentGalleryIndex=index||0;updateLightbox();var box=document.getElementById('product-lightbox');box.classList.remove('pointer-events-none','opacity-0');box.classList.add('pointer-events-auto','opacity-100');box.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';}
function closeLightbox(){var box=document.getElementById('product-lightbox');box.classList.add('pointer-events-none','opacity-0');box.classList.remove('pointer-events-auto','opacity-100');box.setAttribute('aria-hidden','true');document.body.style.overflow='';}
function moveLightbox(step){currentGalleryIndex=(currentGalleryIndex+step+productGalleryImages.length)%productGalleryImages.length;updateLightbox();}
document.addEventListener('keydown',function(event){var box=document.getElementById('product-lightbox');if(box.getAttribute('aria-hidden')==='true')return;if(event.key==='Escape')closeLightbox();if(event.key==='ArrowLeft')moveLightbox(-1);if(event.key==='ArrowRight')moveLightbox(1);});
function adjustQuantity(change){var input=document.getElementById('product-quantity');input.value=Math.max(1,Math.min(10,(parseInt(input.value,10)||1)+change));}
function getSelectedOptions(){
    var options = [];
    var valid = true;
    var optsStrParts = [];
    document.querySelectorAll('.custom-option-group').forEach(function(group){
        var isRequired = group.dataset.required === 'true';
        var checked = group.querySelector('.custom-option-input:checked');
        if (isRequired && !checked) {
            valid = false;
        } else if (checked) {
            options.push({
                optionId: Number(checked.dataset.optionId),
                optionName: checked.dataset.optionName,
                valueId: Number(checked.value),
                valueLabel: checked.dataset.valLabel,
                priceAdjustment: Number(checked.dataset.valPrice)
            });
            optsStrParts.push(checked.dataset.optionId + '.' + checked.value);
        }
    });
    return { valid: valid, options: options, optStr: optsStrParts.join('-') };
}
function saveToCart(product){
    var quantity=parseInt(document.getElementById('product-quantity').value,10)||1;
    var opts = getSelectedOptions();
    if(!opts.valid){window.GiftVibeToast.show('Please complete all required options.','error');return {q:0};}
    product.options = opts.options;
    product.optStr = opts.optStr;
    product.image = selectedProductImage || product.image || '';
    var cart=JSON.parse(localStorage.getItem('giftvibe_cart')||'[]');
    var existing=cart.find(function(item){return item.slug===product.slug&&(item.type||'product')===(product.type||'product')&&Number(item.variantId||0)===Number(product.variantId||0)&&(item.optStr||'')===(product.optStr||'');});
    if(existing)existing.qty=(Number(existing.qty)||1)+quantity;else cart.push(Object.assign({},product,{qty:quantity}));
    localStorage.setItem('giftvibe_cart',JSON.stringify(cart));
    if(window.updateCartUI)window.updateCartUI();
    return {q:quantity, optStr:opts.optStr};
}
function detailAddCart(product){var res=saveToCart(product);if(res.q>0)window.GiftVibeToast.show(res.q+' × '+product.name+' added to cart.');}
function detailBuyNow(slug,type){
    var quantity=parseInt(document.getElementById('product-quantity').value,10)||1;
    var opts = getSelectedOptions();
    if(!opts.valid){window.GiftVibeToast.show('Please complete all required options.','error');return;}
    var itemStr = (type==='combo'?'c~':'p~')+encodeURIComponent(slug)+'@0'+(opts.optStr?'$'+opts.optStr:'')+':'+quantity+(selectedProductImage?'|'+selectedProductImage:'');
    window.giftRequireAuth('/checkout?items='+itemStr);
}
</script>
