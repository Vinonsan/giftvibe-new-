<?php

declare(strict_types=1);

$title = $title ?? 'Home';
$content = $content ?? '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$metaDescription = trim((string) ($metaDescription ?? 'Find thoughtful gifts for every person and celebration at GiftVibe.'));
$canonicalPath = '/' . ltrim((string) ($canonicalPath ?? $currentPath), '/');
$host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
$canonicalUrl = $siteUrl . ($canonicalPath === '/' ? '/' : rtrim($canonicalPath, '/'));
$ogImage = (string) ($ogImage ?? '/assets/images/giftvibe-mark.svg');
$ogImageUrl = str_starts_with($ogImage, 'http') ? $ogImage : $siteUrl . '/' . ltrim($ogImage, '/');
$structuredData = $structuredData ?? null;
$robots = (string) ($robots ?? 'index, follow, max-image-preview:large');
$ogType = (string) ($ogType ?? 'website');
if (is_array($structuredData) && ($structuredData['url'] ?? '') === '/') {
    $structuredData['url'] = $siteUrl . '/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Gift Vibe</title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="robots" content="<?= htmlspecialchars($robots) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>">
    <meta property="og:site_name" content="GiftVibe">
    <meta property="og:title" content="<?= htmlspecialchars($title) ?> | Gift Vibe">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImageUrl) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title) ?> | Gift Vibe">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImageUrl) ?>">
    <?php if (is_array($structuredData)): ?>
        <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script type="module" src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>

    <style type="text/tailwindcss">
        <?= file_get_contents(BASE_PATH . '/public/assets/css/global.css') ?>
    </style>
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased">
    <?php require BASE_PATH . '/resources/views/components/public/theme-cursor.php'; ?>
    <?php require BASE_PATH . '/resources/views/components/navigation/navbar.php'; ?>
    <?php if ($currentPath !== '/login'): ?>
        <?php require BASE_PATH . '/resources/views/components/public/auth-modal.php'; ?>
    <?php endif; ?>
    <?php require BASE_PATH . '/resources/views/components/base/feedback.php'; ?>

    <main class="flex flex-1 flex-col gap-4 px-4 sm:px-6 lg:px-8">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/resources/views/components/navigation/public-footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Section Reveal Observer
        const revealOptions = {
            root: null,
            rootMargin: '0px 0px -60px 0px',
            threshold: 0.05
        };
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, revealOptions);

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            revealObserver.observe(el);
        });

        // Image Lazy Load Helper for Cache/Fallback
        function setupLazyImages() {
            document.querySelectorAll('img.lazy-img').forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                    const parent = img.closest('.img-skeleton');
                    if (parent) parent.classList.remove('img-skeleton');
                } else {
                    img.addEventListener('load', function() {
                        img.classList.add('loaded');
                        const parent = img.closest('.img-skeleton');
                        if (parent) parent.classList.remove('img-skeleton');
                    });
                }
            });
        }
        setupLazyImages();

        // Support dynamically inserted content
        const observer = new MutationObserver(() => {
            setupLazyImages();
            document.querySelectorAll('.scroll-reveal:not(.revealed)').forEach(el => {
                revealObserver.observe(el);
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // Dynamic Cart Badge and Visibility Check
        const navCartBtn = document.getElementById('nav-cart-btn');
        const navCartBadge = document.getElementById('nav-cart-badge');
        const navWishlistBtn = document.getElementById('nav-wishlist-btn');
        const navWishlistBadge = document.getElementById('nav-wishlist-badge');

        window.updateWishlistUI = function() {
            if (!navWishlistBtn || !navWishlistBadge) return;
            let favorites = [];
            try { const stored = JSON.parse(localStorage.getItem('giftvibe_favorites') || '[]'); favorites = Array.isArray(stored) ? stored : []; } catch (error) { localStorage.removeItem('giftvibe_favorites'); }
            navWishlistBtn.classList.toggle('hidden', favorites.length === 0);
            navWishlistBtn.classList.toggle('flex', favorites.length > 0);
            navWishlistBadge.classList.toggle('hidden', favorites.length === 0);
            navWishlistBadge.classList.toggle('flex', favorites.length > 0);
            navWishlistBadge.textContent = favorites.length > 99 ? '99+' : favorites.length;
        };

        window.updateCartUI = function() {
            if (!navCartBtn || !navCartBadge) return;
            let cart = [];
            try {
                const storedCart = JSON.parse(localStorage.getItem('giftvibe_cart') || '[]');
                cart = Array.isArray(storedCart) ? storedCart : [];
            } catch (error) {
                localStorage.removeItem('giftvibe_cart');
            }
            const itemCount = cart.reduce((total, item) => total + Math.max(1, Number(item.qty) || 1), 0);
            if (itemCount > 0) {
                navCartBtn.classList.remove('hidden');
                navCartBtn.classList.add('flex');
                navCartBadge.classList.remove('hidden');
                navCartBadge.classList.add('flex');
                navCartBadge.textContent = itemCount > 99 ? '99+' : itemCount;
            } else {
                navCartBtn.classList.add('hidden');
                navCartBtn.classList.remove('flex');
                navCartBadge.classList.add('hidden');
                navCartBadge.classList.remove('flex');
            }
        };

        window.updateCartUI();
        window.updateWishlistUI();

        // Update when storage changes
        window.addEventListener('storage', window.updateCartUI);
        window.addEventListener('storage', window.updateWishlistUI);
        
        // Update in real-time on cart additions
        document.body.addEventListener('click', function(e) {
            const addBtn = e.target.closest('button[onclick*="giftAddCart"], button[onclick*="detailAddCart"]');
            if (addBtn) {
                setTimeout(window.updateCartUI, 50);
            }
        });
    });
    </script>
</body>
</html>
