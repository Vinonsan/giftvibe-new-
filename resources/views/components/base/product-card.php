<?php
declare(strict_types=1);
$product = $product ?? [];
$productName = (string) ($product['name'] ?? 'Gift');
$productImage = (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg');
if (str_starts_with($productImage, 'public/')) $productImage = '/' . substr($productImage, 7);
if ($productImage !== '' && !str_starts_with($productImage, '/') && !str_starts_with($productImage, 'http')) $productImage = '/' . $productImage;
$basePrice = (float) ($product['base_price'] ?? 0);
$salePrice = null;
$hasSale = false;
$discountPercentage = $hasSale ? (int) round((($basePrice - $salePrice) / $basePrice) * 100) : 0;
/* Always derive the URL from this card's product; reused includes share scope. */
$productHref = isset($productCardHref) ? (string) $productCardHref : '/shop?product=' . rawurlencode((string) ($product['slug'] ?? ''));
unset($productCardHref);
$categoryName = (string) ($product['category_names'] ?? 'GiftVibe Collection');
$itemType = (string) ($product['_type'] ?? 'product');

?>
<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all duration-300 hover:shadow-lg">
    <!-- Image Wrapper -->
    <div class="relative aspect-square overflow-hidden bg-slate-50">
        <div class="img-skeleton h-full w-full">
            <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($productName) ?> - Buy Online from GiftVibe" width="600" height="600" loading="lazy" decoding="async" class="lazy-img h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-103" onload="this.classList.add('loaded'); this.parentElement.classList.remove('img-skeleton');">
        </div>
        
        <!-- Glassmorphism Quick View Overlay on Hover -->
        <div class="absolute inset-0 z-10 flex items-center justify-center bg-secondary/35 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-hover:opacity-100">
            <div class="flex translate-y-3 items-center justify-center gap-2 transition duration-300 group-hover:translate-y-0">
                <a href="<?=htmlspecialchars($productHref)?>" class="inline-flex h-10 items-center gap-2 rounded-full bg-white px-4 text-xs font-bold text-secondary shadow-lg transition hover:text-primary"><iconify-icon icon="heroicons:eye-solid" width="16" height="16" aria-hidden="true"></iconify-icon>View</a>
                <button type="button" data-favorite-slug="<?= htmlspecialchars($itemType . ':' . (string)($product['slug']??'')) ?>" onclick='giftToggleFavorite(event, this, <?= htmlspecialchars(json_encode(['name'=>$productName,'slug'=>(string)($product['slug']??''),'type'=>$itemType,'price'=>$basePrice,'image'=>$productImage,'href'=>$productHref], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT), ENT_QUOTES) ?>)' class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary shadow-lg transition hover:bg-accent hover:text-white" aria-label="Save <?=htmlspecialchars($productName)?> to favorites"><iconify-icon icon="heroicons:heart" width="18" height="18" aria-hidden="true"></iconify-icon></button>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="flex flex-1 flex-col p-4">
        <!-- Category (Uppercase and tracking) -->
        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">
            <?= htmlspecialchars($categoryName) ?>
        </span>

        <!-- Title -->
        <h3 class="mt-1.5 text-sm font-semibold leading-snug text-secondary line-clamp-2 min-h-10">
            <a href="<?= htmlspecialchars($productHref) ?>" class="transition-colors duration-200 hover:text-primary" title="Buy <?= htmlspecialchars($productName) ?>">
                <?= htmlspecialchars($productName) ?>
            </a>
        </h3>

        <!-- Price Panel -->
        <div class="mt-2.5 flex items-baseline gap-2">
            <span class="text-base font-bold text-secondary">
                LKR <?= number_format($hasSale ? $salePrice : $basePrice, 2) ?>
            </span>
            <?php if ($hasSale): ?>
                <span class="text-xs font-normal text-slate-400 line-through">
                    LKR <?= number_format($basePrice, 2) ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Button Actions (Apple Clean Style) -->
        <div class="mt-auto grid grid-cols-2 gap-2 pt-4">
            <button type="button" onclick='giftAddCart(event, <?= htmlspecialchars(json_encode(['name'=>$productName,'slug'=>(string)($product['slug']??''),'type'=>$itemType,'price'=>$basePrice,'image'=>$productImage], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT), ENT_QUOTES) ?>)' class="h-11 rounded-xl border border-primary bg-white px-2 text-xs font-bold text-primary transition hover:bg-primary hover:text-white">Add to cart</button>
            <button type="button" onclick="giftBuyNow(event, '<?= htmlspecialchars((string)($product['slug']??''), ENT_QUOTES) ?>', '<?= htmlspecialchars($itemType, ENT_QUOTES) ?>')" class="h-11 rounded-xl bg-primary px-2 text-xs font-bold text-white transition hover:bg-secondary">Buy now</button>
        </div>
    </div>
</article>

<!-- Include JavaScript logic once -->
<?php if (!defined('PRODUCT_CARD_JS_DEFINED')): ?>
<?php define('PRODUCT_CARD_JS_DEFINED', true); ?>
<script>
function showNotification(message) {
    let container = document.getElementById('gift-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'gift-toast-container';
        container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 pointer-events-none max-w-sm w-full px-4';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = 'flex items-center gap-3 bg-slate-900/95 text-white px-4 py-3 rounded-2xl shadow-xl backdrop-blur-md border border-white/10 transition-all duration-300 translate-y-5 opacity-0 pointer-events-auto';
    toast.innerHTML = `
        <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-accent text-white">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
        </span>
        <div class="flex-1 text-xs font-semibold">${message}</div>
    `;
    container.appendChild(toast);
    
    // Trigger transition
    setTimeout(() => {
        toast.classList.remove('translate-y-5', 'opacity-0');
    }, 10);
    
    // Remove toast
    setTimeout(() => {
        toast.classList.add('translate-y-[-10px]', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function giftAddCart(event, product) {
    event.preventDefault();
    event.stopPropagation();
    
    // Local storage mock cart
    let cart = JSON.parse(localStorage.getItem('giftvibe_cart') || '[]');
    let existing = cart.find(item => item.slug === product.slug && (item.type || 'product') === (product.type || 'product'));
    if (existing) existing.qty = (Number(existing.qty) || 1) + 1;
    else cart.push({...product, qty: 1});
    localStorage.setItem('giftvibe_cart', JSON.stringify(cart));
    if (window.updateCartUI) window.updateCartUI();
    
    showNotification(`"${product.name}" added to cart!`);
}

function giftBuyNow(event, slug, type) {
    event.preventDefault();
    event.stopPropagation();
    window.giftRequireAuth('/checkout?' + (type === 'combo' ? 'combo' : 'product') + '=' + encodeURIComponent(slug));
}
function giftToggleFavorite(event, button, product) {
    event.preventDefault();event.stopPropagation();
    let favorites=[];try{favorites=JSON.parse(localStorage.getItem('giftvibe_favorites')||'[]');if(!Array.isArray(favorites))favorites=[];}catch(error){}
    const index=favorites.findIndex(item=>item.slug===product.slug&&(item.type||'product')===(product.type||'product'));
    if(index>=0){favorites.splice(index,1);showNotification(`"${product.name}" removed from favorites.`);}else{favorites.push(product);showNotification(`"${product.name}" saved to favorites!`);}
    localStorage.setItem('giftvibe_favorites',JSON.stringify(favorites));
    updateFavoriteButtons();if(window.updateWishlistUI)window.updateWishlistUI();
}
function updateFavoriteButtons(){let favorites=[];try{favorites=JSON.parse(localStorage.getItem('giftvibe_favorites')||'[]');}catch(error){}document.querySelectorAll('[data-favorite-slug]').forEach(function(button){const active=favorites.some(item=>(item.type||'product')+':'+item.slug===button.dataset.favoriteSlug);button.classList.toggle('bg-accent',active);button.classList.toggle('text-white',active);button.classList.toggle('bg-white',!active);button.classList.toggle('text-primary',!active);button.querySelector('iconify-icon').setAttribute('icon',active?'heroicons:heart-solid':'heroicons:heart');});}
document.addEventListener('DOMContentLoaded',updateFavoriteButtons);
</script>
<?php endif; ?>
