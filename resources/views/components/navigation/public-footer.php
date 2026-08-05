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
    ['label' => 'Services', 'href' => '/services'],
    ['label' => 'Blog', 'href' => '/blog'],
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
        'icon' => 'fa6-brands:facebook-f'
    ],
    'instagram' => [
        'url' => (string)($settings['social.instagram'] ?? ''),
        'icon' => 'fa6-brands:instagram'
    ],
    'youtube' => [
        'url' => (string)($settings['social.youtube'] ?? ''),
        'icon' => 'fa6-brands:youtube'
    ],
    'twitter' => [
        'url' => (string)($settings['social.twitter'] ?? ''),
        'icon' => 'fa6-brands:x-twitter'
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
                <p class="max-w-sm break-words text-sm leading-relaxed text-slate-400 [overflow-wrap:anywhere]">
                    <?= htmlspecialchars($description) ?>
                </p>
                <!-- Social media icons below description as requested -->
                <div class="flex items-center gap-4 pt-2">
                    <?php foreach ($socials as $name => $social): 
                        if ($social['url'] === '') continue;
                    ?>
                        <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-white/[.07] text-blue-300 shadow-sm transition hover:-translate-y-0.5 hover:border-primary hover:bg-primary hover:text-white" aria-label="<?= ucfirst($name) ?>">
                            <iconify-icon icon="<?= htmlspecialchars($social['icon']) ?>" width="19" height="19" aria-hidden="true"></iconify-icon>
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
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-primary text-white shadow-sm"><iconify-icon icon="solar:letter-bold-duotone" width="20" height="20" aria-hidden="true"></iconify-icon></span>
                            <a href="mailto:<?= htmlspecialchars($email) ?>" class="hover:text-white break-all"><?= htmlspecialchars($email) ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ($phone !== ''): ?>
                        <div class="flex items-start gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-primary text-white shadow-sm"><iconify-icon icon="solar:phone-calling-bold-duotone" width="20" height="20" aria-hidden="true"></iconify-icon></span>
                            <span class="hover:text-white"><?= htmlspecialchars($phone) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($address !== ''): ?>
                        <div class="flex items-start gap-2.5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-primary text-white shadow-sm"><iconify-icon icon="solar:map-point-bold-duotone" width="20" height="20" aria-hidden="true"></iconify-icon></span>
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
