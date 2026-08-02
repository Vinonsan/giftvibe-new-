<?php
declare(strict_types=1);

use App\Core\Database;

$pdo = Database::connection();
$settings = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

$logo = (string)($settings['site.logo'] ?? '/assets/images/giftvibe-mark.svg');
if (str_starts_with($logo, 'public/')) {
    $logo = '/' . substr($logo, 7);
}
$siteName = (string)($settings['site.name'] ?? 'GiftVibe');
$description = (string)($settings['site.description'] ?? 'Thoughtful gifts for every person, moment and celebration—all in one place.');

$quickLinks = json_decode((string)($settings['footer.quick_links'] ?? '[]'), true) ?: [
    ['label' => 'Home', 'href' => '/'],
    ['label' => 'Shop', 'href' => '/shop'],
    ['label' => 'About Us', 'href' => '/about'],
    ['label' => 'Contact', 'href' => '/contact']
];

$productsLinks = json_decode((string)($settings['footer.products_links'] ?? '[]'), true) ?: [
    ['label' => 'All Combos', 'href' => '/combos'],
    ['label' => 'Best Sellers', 'href' => '/shop?sort=popular']
];

$email = (string)($settings['site.contact_email'] ?? '');
$phone = (string)($settings['site.contact_phone'] ?? '');
$address = (string)($settings['site.contact_address'] ?? '');

$socials = [
    'facebook' => [
        'url' => (string)($settings['social.facebook'] ?? ''),
        'icon' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>'
    ],
    'instagram' => [
        'url' => (string)($settings['social.instagram'] ?? ''),
        'icon' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.01 3.74.054 9.21.42 9.77 5.77 9.77 9.77 0 2.43-.01 2.784-.054 3.74-.42 9.21-5.77 9.77-9.77 9.77-2.43 0-2.784-.01-3.74-.054-9.21-.42-9.77-5.77-9.77-9.77 0-2.43.01-2.784.054-3.74.42-9.21 5.77-9.77 9.77-9.77zm.052 2.01c-2.4 0-2.717.01-3.66.053-7.56.344-7.9 5.27-7.9 7.91 0 2.4.01 2.716.053 3.661.344 7.56 5.27 7.9 7.91 7.9 2.4 0 2.716-.01 3.661-.053 7.56-.344 7.9-5.27 7.9-7.91 0-2.4-.01-2.717-.053-3.66-.344-7.56-5.27-7.9-7.91-7.9zM12 5.802a6.197 6.197 0 110 12.394 6.197 6.197 0 010-12.394zm0 2.01a4.186 4.186 0 100 8.372 4.186 4.186 0 000-8.372zm6.404-1.84a1.44 1.44 0 110 2.88 1.44 1.44 0 010-2.88z" clip-rule="evenodd"/></svg>'
    ],
    'youtube' => [
        'url' => (string)($settings['social.youtube'] ?? ''),
        'icon' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.518 3.545 12 3.545 12 3.545s-7.518 0-9.388.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.87.507 9.388.507 9.388.507s7.518 0 9.388-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'
    ],
    'twitter' => [
        'url' => (string)($settings['social.twitter'] ?? ''),
        'icon' => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'
    ]
];
?>
<footer class="border-t border-slate-200 bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.25fr]">
            <!-- Column 1: Logo, Brand, Description, Social Media -->
            <div class="space-y-6">
                <a href="/" class="inline-flex items-center gap-3" aria-label="<?= htmlspecialchars($siteName) ?> home">
                    <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-11 w-11 rounded-xl ring-1 ring-white/10 object-contain bg-white/5 p-1">
                    <span class="text-xl font-extrabold tracking-tight"><?= htmlspecialchars($siteName) ?></span>
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-slate-400">
                    <?= htmlspecialchars($description) ?>
                </p>
                <!-- Social media icons below description as requested -->
                <div class="flex items-center gap-4 pt-2">
                    <?php foreach ($socials as $name => $social): 
                        if ($social['url'] === '') continue;
                    ?>
                        <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-slate-400 transition hover:text-white" aria-label="<?= ucfirst($name) ?>">
                            <?= $social['icon'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h2 class="text-sm font-semibold text-white tracking-wider uppercase">Quick Links</h2>
                <ul class="mt-4 space-y-3">
                    <?php foreach ($quickLinks as $link): ?>
                        <li>
                            <a href="<?= htmlspecialchars($link['href']) ?>" class="text-sm text-slate-400 transition hover:text-white">
                                <?= htmlspecialchars($link['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Column 3: Products Links -->
            <div>
                <h2 class="text-sm font-semibold text-white tracking-wider uppercase">Products Links</h2>
                <ul class="mt-4 space-y-3">
                    <?php foreach ($productsLinks as $link): ?>
                        <li>
                            <a href="<?= htmlspecialchars($link['href']) ?>" class="text-sm text-slate-400 transition hover:text-white">
                                <?= htmlspecialchars($link['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Column 4: Contact Details (Email & Address) -->
            <div class="space-y-4">
                <h2 class="text-sm font-semibold text-white tracking-wider uppercase">Contact Us</h2>
                <div class="space-y-3 text-sm text-slate-400">
                    <?php if ($email !== ''): ?>
                        <div class="flex items-start gap-2.5">
                            <svg class="h-5 w-5 shrink-0 text-slate-500 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            <a href="mailto:<?= htmlspecialchars($email) ?>" class="hover:text-white break-all"><?= htmlspecialchars($email) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ($phone !== ''): ?>
                        <div class="flex items-start gap-2.5">
                            <svg class="h-5 w-5 shrink-0 text-slate-500 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.122-4.1-6.924-6.924l1.293-.97a1.125 1.125 0 00.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            <span class="hover:text-white"><?= htmlspecialchars($phone) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($address !== ''): ?>
                        <div class="flex items-start gap-2.5">
                            <svg class="h-5 w-5 shrink-0 text-slate-500 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <address class="not-italic leading-relaxed"><?= nl2br(htmlspecialchars($address)) ?></address>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All rights reserved.</p>
            <div class="flex items-center gap-5">
                <a href="/terms" class="transition hover:text-white">Terms</a>
                <a href="/privacy" class="transition hover:text-white">Privacy</a>
                <a href="/contact" class="transition hover:text-white">Support</a>
            </div>
        </div>
    </div>
</footer>

