<?php
declare(strict_types=1);

$settings = $settings ?? [];
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';

$input = static function($n, $l, $v = '', $t = 'text', $p = '') {
    $inputName = $n;
    $inputId = $n;
    $inputLabel = $l;
    $inputValue = (string)$v;
    $inputType = $t;
    $inputPlaceholder = $p;
    $inputHint = '';
    $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = false;
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = [];
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};

$button = static function($label, $options = []) {
    $buttonLabel = $label;
    $buttonVariant = $options['variant'] ?? 'solid';
    $buttonColor = $options['color'] ?? 'primary';
    $buttonSize = $options['size'] ?? 'md';
    $buttonType = $options['type'] ?? 'button';
    $buttonHref = $options['href'] ?? '';
    $buttonName = $options['name'] ?? '';
    $buttonValue = $options['value'] ?? '';
    $buttonId = '';
    $buttonIcon = '';
    $buttonIconTrailing = '';
    $buttonIconOnly = false;
    $buttonFullWidth = false;
    $buttonDisabled = false;
    $buttonLoading = false;
    $buttonOnclick = '';
    $buttonClass = '';
    $buttonAttributes = $options['attributes'] ?? [];
    require BASE_PATH . '/resources/views/components/base/button.php';
};

// Decode links from JSON or use defaults if empty
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
?>
<div class="space-y-6">
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3.5 text-sm font-semibold <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-between rounded-2xl border bg-white p-6 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Footer Settings</h1>
            <p class="mt-1 text-sm text-slate-500">Configure your footer logo, description, social profiles, links, and contact info.</p>
        </div>
        <?php $button('Save Settings', ['type' => 'submit', 'size' => 'lg', 'attributes' => ['form' => 'settings-form']]); ?>
    </div>

    <form id="settings-form" method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <!-- Left Column: Branding & Contact Info (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Branding Panel -->
            <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100">1. Branding & Logo</h3>
                
                <div class="grid gap-4 sm:grid-cols-2 items-center">
                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-1.5">Site Logo</label>
                        <input type="file" name="site_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/5 file:text-primary hover:file:bg-primary/10 file:cursor-pointer">
                        <span class="block text-[11px] text-slate-400 mt-1.5">Supported: JPG, PNG, WebP or SVG. Maximum 3MB.</span>
                    </div>
                    <div class="flex justify-center border border-slate-100 bg-slate-50/50 p-4 rounded-2xl">
                        <?php if (!empty($settings['site.logo'])): ?>
                            <div class="text-center">
                                <span class="block text-[10px] text-slate-400 mb-1.5 uppercase font-bold tracking-wider">Current Logo</span>
                                <img src="<?= htmlspecialchars($settings['site.logo']) ?>" alt="Site Logo" class="h-14 max-w-full object-contain mx-auto">
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <span class="block text-[10px] text-slate-400 mb-1.5 uppercase font-bold tracking-wider">Default Logo</span>
                                <img src="/assets/images/giftvibe-mark.svg" alt="Default Logo" class="h-14 max-w-full object-contain mx-auto">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php $input('site_name', 'Site / Brand Name', $settings['site.name'] ?? 'GiftVibe'); ?>

                <label class="block space-y-1.5">
                    <span class="text-sm font-semibold text-secondary">Footer Description</span>
                    <textarea name="site_description" rows="3" placeholder="Thoughtful gifts for every person, moment and celebration—all in one place." class="w-full rounded-xl border border-slate-300 p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string)($settings['site.description'] ?? '')) ?></textarea>
                </label>
            </div>

            <!-- Contact Panel -->
            <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100">2. Contact Information</h3>
                
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php 
                    $input('site_contact_email', 'Contact Email', $settings['site.contact_email'] ?? '', 'email'); 
                    $input('site_contact_phone', 'Contact Phone', $settings['site.contact_phone'] ?? ''); 
                    ?>
                </div>

                <label class="block space-y-1.5">
                    <span class="text-sm font-semibold text-secondary">Physical Address</span>
                    <textarea name="site_contact_address" rows="3" placeholder="123 Galle Road, Colombo, Sri Lanka" class="w-full rounded-xl border border-slate-300 p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string)($settings['site.contact_address'] ?? '')) ?></textarea>
                </label>
            </div>

            <!-- Social Media Profiles -->
            <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100">3. Social Media Links</h3>
                
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php 
                    $input('social_facebook', 'Facebook URL', $settings['social.facebook'] ?? '', 'url'); 
                    $input('social_instagram', 'Instagram URL', $settings['social.instagram'] ?? '', 'url'); 
                    $input('social_youtube', 'YouTube URL', $settings['social.youtube'] ?? '', 'url'); 
                    $input('social_twitter', 'Twitter / X URL', $settings['social.twitter'] ?? '', 'url'); 
                    ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Navigation Links lists (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Quick Links -->
            <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center justify-between">
                    <span>4. Quick Links</span>
                    <span class="text-[10px] font-bold bg-slate-100 px-2 py-0.5 rounded-full text-slate-500 uppercase">Column 2</span>
                </h3>
                
                <div class="space-y-3">
                    <?php for ($i = 0; $i < 7; $i++): 
                        $link = $quickLinks[$i] ?? ['label' => '', 'href' => ''];
                    ?>
                        <div class="flex gap-2 items-center">
                            <div class="w-1/2">
                                <input type="text" name="quick_links_label[]" value="<?= htmlspecialchars($link['label']) ?>" placeholder="Label (e.g. Shop)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                            <div class="w-1/2">
                                <input type="text" name="quick_links_href[]" value="<?= htmlspecialchars($link['href']) ?>" placeholder="URL (e.g. /shop)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Products Links -->
            <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center justify-between">
                    <span>5. Products Links</span>
                    <span class="text-[10px] font-bold bg-slate-100 px-2 py-0.5 rounded-full text-slate-500 uppercase">Column 3</span>
                </h3>
                
                <div class="space-y-3">
                    <?php for ($i = 0; $i < 5; $i++): 
                        $link = $productsLinks[$i] ?? ['label' => '', 'href' => ''];
                    ?>
                        <div class="flex gap-2 items-center">
                            <div class="w-1/2">
                                <input type="text" name="products_links_label[]" value="<?= htmlspecialchars($link['label']) ?>" placeholder="Label (e.g. Combos)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                            <div class="w-1/2">
                                <input type="text" name="products_links_href[]" value="<?= htmlspecialchars($link['href']) ?>" placeholder="URL (e.g. /combos)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary/20">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </form>
</div>
