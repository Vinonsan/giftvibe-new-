<?php

declare(strict_types=1);

$currentPath = $currentPath ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicNavigation = $publicNavigation ?? [
    ['label' => 'Home', 'href' => '/'],
    ['label' => 'Shop', 'href' => '/shop'],
    ['label' => 'About', 'href' => '/about'],
    ['label' => 'Contact', 'href' => '/contact'],
];
?>
<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl" data-public-header>
    <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Primary navigation">
        <a href="/" class="flex shrink-0 items-center gap-2.5" aria-label="GiftVibe home">
            <img src="/assets/images/giftvibe-mark.svg" alt="" class="h-10 w-10 rounded-xl shadow-sm">
            <span class="text-xl font-extrabold tracking-tight text-secondary">
                Gift<span class="text-primary">Vibe</span>
            </span>
        </a>

        <div class="hidden items-center rounded-full border border-slate-200 bg-slate-50/80 p-1 md:flex">
            <?php foreach ($publicNavigation as $item):
                $href = (string) ($item['href'] ?? '#');
                $isActive = $currentPath === $href || ($href !== '/' && str_starts_with($currentPath, rtrim($href, '/') . '/'));
            ?>
                <a href="<?= htmlspecialchars($href) ?>"
                   class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isActive ? 'bg-white text-primary shadow-sm ring-1 ring-slate-200/70' : 'text-slate-600 hover:text-secondary' ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars((string) ($item['label'] ?? '')) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2">
            <a href="/shop" class="hidden h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-primary sm:flex" aria-label="Search products">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/>
                </svg>
            </a>
            <a href="/cart" class="relative hidden h-10 w-10 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-primary sm:flex" aria-label="Shopping cart">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.9-1.4L21 8H7M10 20h.01M18 20h.01"/>
                </svg>
            </a>
            <a href="/admin" class="hidden rounded-full bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-primary/20 transition hover:-translate-y-0.5 hover:bg-secondary sm:inline-flex">
                Admin
            </a>
            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-secondary transition hover:bg-slate-50 md:hidden" data-public-menu-toggle aria-expanded="false" aria-controls="public-mobile-menu" aria-label="Open navigation menu">
                <svg class="h-5 w-5" data-menu-open-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
                <svg class="hidden h-5 w-5" data-menu-close-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </nav>

    <div id="public-mobile-menu" class="hidden border-t border-slate-100 bg-white px-4 py-4 shadow-lg md:hidden" data-public-mobile-menu>
        <div class="mx-auto grid max-w-7xl gap-1">
            <?php foreach ($publicNavigation as $item):
                $href = (string) ($item['href'] ?? '#');
                $isActive = $currentPath === $href || ($href !== '/' && str_starts_with($currentPath, rtrim($href, '/') . '/'));
            ?>
                <a href="<?= htmlspecialchars($href) ?>" class="rounded-xl px-4 py-3 text-sm font-medium transition <?= $isActive ? 'bg-primary/10 text-primary' : 'text-slate-600 hover:bg-slate-50 hover:text-secondary' ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars((string) ($item['label'] ?? '')) ?>
                </a>
            <?php endforeach; ?>
            <div class="mt-2 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                <a href="/cart" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-secondary">Cart</a>
                <a href="/admin" class="rounded-xl bg-primary px-4 py-3 text-center text-sm font-semibold text-white">Admin</a>
            </div>
        </div>
    </div>
</header>

<script>
(function () {
    var header = document.querySelector('[data-public-header]');
    if (!header || header.dataset.ready === 'true') return;

    var button = header.querySelector('[data-public-menu-toggle]');
    var menu = header.querySelector('[data-public-mobile-menu]');
    var openIcon = header.querySelector('[data-menu-open-icon]');
    var closeIcon = header.querySelector('[data-menu-close-icon]');

    function setOpen(open) {
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
        menu.classList.toggle('hidden', !open);
        openIcon.classList.toggle('hidden', open);
        closeIcon.classList.toggle('hidden', !open);
    }

    button.addEventListener('click', function () {
        setOpen(button.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setOpen(false);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) setOpen(false);
    });

    header.dataset.ready = 'true';
})();
</script>
