<?php

declare(strict_types=1);

use App\Core\Database;

$currentPath = $currentPath ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$publicNavigation = $publicNavigation ?? [
    ['label' => 'Home', 'href' => '/'],
    ['label' => 'Shop', 'href' => '/shop'],
    ['label' => 'Combos', 'href' => '/combos'],
    ['label' => 'Services', 'href' => '/services'],
    ['label' => 'About', 'href' => '/about'],
    ['label' => 'Contact', 'href' => '/contact'],
];

$pdo = Database::connection();
$general = $pdo->query("SELECT * FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$logo = (string)($general['site_logo'] ?? '/assets/images/logo.svg');
$siteName = (string)($general['site_name'] ?? 'GiftVibe');
$customer = $_SESSION['user'] ?? null;
$customerName = $customer ? trim((string) (($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))) : '';
$initialSource = $customerName ?: (string) ($customer['email'] ?? 'US');
$customerInitials = function_exists('mb_substr') ? mb_strtoupper(mb_substr($initialSource, 0, 2)) : strtoupper(substr($initialSource, 0, 2));
?>
<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl" data-public-header>
    <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-12" aria-label="Primary navigation">
        <a href="/" class="flex shrink-0 items-center gap-2.5" aria-label="GiftVibe home">
            <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?> Logo" width="40" height="40" class="h-10 w-10 rounded-xl shadow-sm object-contain">
            <span class="text-xl font-extrabold tracking-tight text-secondary">
                <?= htmlspecialchars($siteName) ?>
            </span>
        </a>

        <div class="hidden items-center rounded-full border border-slate-200 bg-slate-50/80 p-1 md:flex">
            <?php foreach ($publicNavigation as $item):
                $href = (string) ($item['href'] ?? '#');
                $isActive = $currentPath === $href || ($href !== '/' && str_starts_with($currentPath, rtrim($href, '/') . '/'));
            ?>
                <a href="<?= htmlspecialchars($href) ?>"
                   class="rounded-full px-4 py-2 text-sm font-medium transition <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:text-secondary' ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars((string) ($item['label'] ?? '')) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2">
            <!-- Wishlist Icon -->
            <a id="nav-wishlist-btn" href="/favorites" class="relative hidden h-10 w-10 items-center justify-center rounded-xl border border-primary bg-white text-primary transition hover:bg-primary hover:text-white" aria-label="Favorite products">
                <iconify-icon icon="heroicons:heart-solid" width="20" height="20" aria-hidden="true"></iconify-icon>
                <span id="nav-wishlist-badge" class="absolute -right-1 -top-1 hidden h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1 text-[10px] font-bold text-white ring-2 ring-white">0</span>
            </a>
            <!-- Cart Icon -->
            <a id="nav-cart-btn" href="/cart" class="relative hidden h-10 w-10 items-center justify-center rounded-xl border border-primary bg-white text-primary transition hover:bg-primary hover:text-white" aria-label="Shopping cart">
                <iconify-icon icon="heroicons:shopping-cart-solid" width="20" height="20" aria-hidden="true"></iconify-icon>
                <span id="nav-cart-badge" class="absolute -right-1 -top-1 hidden h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1 text-[10px] font-bold text-white ring-2 ring-white">0</span>
            </a>

            <?php if ($customer): ?>
                <div class="relative hidden sm:block" data-user-menu>
                    <button type="button" data-user-menu-toggle class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-primary bg-white text-primary transition hover:bg-primary hover:text-white" aria-label="Open customer menu" aria-expanded="false">
                        <span class="text-xs font-black"><?= htmlspecialchars($customerInitials) ?></span>
                    </button>
                    <div data-user-menu-panel class="absolute right-0 top-12 hidden w-64 rounded-2xl border border-primary/10 bg-white p-3 shadow-xl">
                        <div class="rounded-xl bg-primary/5 p-3"><p class="truncate text-sm font-bold text-secondary"><?= htmlspecialchars($customerName ?: 'Customer') ?></p><p class="mt-0.5 truncate text-xs text-secondary/55"><?= htmlspecialchars((string) ($customer['email'] ?? '')) ?></p><?php if (!empty($customer['phone'])): ?><p class="mt-1 text-xs text-secondary/55"><?= htmlspecialchars((string) $customer['phone']) ?></p><?php endif; ?></div>
                        <a href="/api/auth/logout?redirect=/" class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-bold text-accent transition hover:bg-accent/10"><iconify-icon icon="heroicons:arrow-right-on-rectangle-solid" width="18" height="18"></iconify-icon>Logout</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Mobile Menu Toggle -->
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
                <a href="<?= htmlspecialchars($href) ?>" class="rounded-xl px-4 py-3 text-sm font-medium transition <?= $isActive ? 'bg-[#FF5A79] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-secondary' ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
                    <?= htmlspecialchars((string) ($item['label'] ?? '')) ?>
                </a>
            <?php endforeach; ?>
            <?php if ($customer): ?><div class="mt-2 rounded-xl bg-primary/5 p-3"><p class="text-sm font-bold text-secondary"><?= htmlspecialchars($customerName ?: 'Customer') ?></p><p class="truncate text-xs text-secondary/55"><?= htmlspecialchars((string) ($customer['email'] ?? '')) ?></p></div><?php endif; ?>
            <div class="mt-2 grid <?= $customer ? 'grid-cols-2' : 'grid-cols-1' ?> gap-2 border-t border-slate-100 pt-3">
                <a href="/cart" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-secondary">Cart</a>
                <?php if ($customer): ?><a href="/api/auth/logout?redirect=/" class="rounded-xl bg-primary px-4 py-3 text-center text-sm font-semibold text-white">Logout</a><?php endif; ?>
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
    var userMenu = header.querySelector('[data-user-menu]');
    var userToggle = header.querySelector('[data-user-menu-toggle]');
    var userPanel = header.querySelector('[data-user-menu-panel]');

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

    if (userToggle && userPanel) {
        userToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var open = userPanel.classList.toggle('hidden') === false;
            userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function (event) {
            if (!userMenu.contains(event.target)) {
                userPanel.classList.add('hidden');
                userToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    header.dataset.ready = 'true';
})();
</script>
