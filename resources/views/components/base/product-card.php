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

?>
<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all duration-300 hover:shadow-lg">
    <!-- Image Wrapper -->
    <div class="relative aspect-square overflow-hidden bg-slate-50">
        <img src="<?= htmlspecialchars($productImage) ?>" alt="<?= htmlspecialchars($productName) ?> - Buy Online from GiftVibe" width="600" height="600" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-103">
        
        <!-- Glassmorphism Quick View Overlay on Hover -->
        <div class="absolute inset-0 z-10 flex items-center justify-center bg-secondary/35 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-hover:opacity-100">
            <div class="flex translate-y-3 items-center justify-center gap-2 transition duration-300 group-hover:translate-y-0">
                <button type="button" onclick="giftAddCart(event, '<?=htmlspecialchars(addslashes($productName),ENT_QUOTES)?>')" class="inline-flex h-10 items-center gap-2 rounded-full bg-primary px-4 text-xs font-bold text-white shadow-lg transition hover:bg-secondary" aria-label="Add <?=htmlspecialchars($productName)?> to cart"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H7M10 20h.01M18 20h.01"/></svg>Add to cart</button>
                <a href="<?=htmlspecialchars($productHref)?>" class="inline-flex h-10 items-center gap-2 rounded-full bg-white px-4 text-xs font-bold text-secondary shadow-lg transition hover:text-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg>View</a>
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
        <div class="mt-auto pt-4">
            <?php
            $buttonLabel = 'Buy Now';
            $buttonClass = 'h-11 w-full rounded-xl whitespace-nowrap text-xs font-bold';
            $buttonOnclick = "giftBuyNow(event, '" . htmlspecialchars($productHref) . "')";
            $buttonColor = 'primary';
            $buttonVariant = 'solid';
            $buttonIcon = null;
            $buttonIconOnly = false;
            $buttonHref = null;
            $buttonAttributes = [];
            require BASE_PATH . '/resources/views/components/base/button.php';
            ?>
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

function giftAddCart(event, name) {
    event.preventDefault();
    event.stopPropagation();
    
    // Local storage mock cart
    let cart = JSON.parse(localStorage.getItem('giftvibe_cart') || '[]');
    cart.push({ name: name, qty: 1 });
    localStorage.setItem('giftvibe_cart', JSON.stringify(cart));
    
    showNotification(`"${name}" added to cart!`);
}

function giftBuyNow(event, href) {
    event.preventDefault();
    event.stopPropagation();
    window.location.href = href;
}
</script>
<?php endif; ?>
