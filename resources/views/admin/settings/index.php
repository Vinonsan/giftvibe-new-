<?php
declare(strict_types=1);

$tab = $tab ?? 'general';
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

// Decode links for footer tab
if ($tab === 'footer') {
    $quickLinks = json_decode((string)($settings['quick_links'] ?? '[]'), true) ?: [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Shop', 'href' => '/shop'],
        ['label' => 'Services', 'href' => '/services'],
        ['label' => 'Blog', 'href' => '/blog'],
        ['label' => 'About Us', 'href' => '/about'],
        ['label' => 'Contact', 'href' => '/contact']
    ];

    $productsLinks = json_decode((string)($settings['products_links'] ?? '[]'), true) ?: [
        ['label' => 'All Combos', 'href' => '/combos'],
        ['label' => 'Best Sellers', 'href' => '/shop?sort=popular']
    ];
}
?>
<div class="space-y-6">
    <?php if ($flash): ?><span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'], ENT_QUOTES) ?>"></span><?php endif; ?>

    <!-- Header Panel -->
    <div class="flex items-center justify-between rounded-2xl border bg-white p-6 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">
                <?php if ($tab === 'general'): ?>General Settings
                <?php elseif ($tab === 'contact'): ?>Contact & Social Settings
                <?php elseif ($tab === 'payment'): ?>Payment Settings
                <?php elseif ($tab === 'footer'): ?>Footer Link Lists
                <?php endif; ?>
            </h1>
            <p class="mt-1 text-sm text-slate-500">Configure your parameters, each section saves directly to its own dedicated table.</p>
        </div>
        <?php $button('Save ' . ucfirst($tab) . ' Settings', ['type' => 'submit', 'size' => 'lg', 'attributes' => ['form' => 'settings-form']]); ?>
    </div>

    <!-- Layout Grid: Left Sidebar Submenu, Right Main Content -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
        <!-- Sidebar Navigation (3 columns) -->
        <div class="md:col-span-3">
            <div class="rounded-2xl border bg-white p-3.5 shadow-sm space-y-1">
                <span class="block text-[11px] font-extrabold uppercase tracking-widest text-slate-400 px-3 pb-2">Submenu</span>
                <a href="/admin/settings?tab=general" class="flex items-center gap-3 px-3.5 py-3 text-sm font-bold rounded-xl transition <?= $tab === 'general' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <iconify-icon icon="heroicons:cog-6-tooth-solid" width="18" height="18"></iconify-icon>
                    <span>General Settings</span>
                </a>
                <a href="/admin/settings?tab=contact" class="flex items-center gap-3 px-3.5 py-3 text-sm font-bold rounded-xl transition <?= $tab === 'contact' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <iconify-icon icon="heroicons:phone-solid" width="18" height="18"></iconify-icon>
                    <span>Contact Info</span>
                </a>
                <a href="/admin/settings?tab=footer" class="flex items-center gap-3 px-3.5 py-3 text-sm font-bold rounded-xl transition <?= $tab === 'footer' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <iconify-icon icon="heroicons:list-bullet-solid" width="18" height="18"></iconify-icon>
                    <span>Footer Links</span>
                </a>
                <a href="/admin/settings?tab=payment" class="flex items-center gap-3 px-3.5 py-3 text-sm font-bold rounded-xl transition <?= $tab === 'payment' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' ?>">
                    <iconify-icon icon="heroicons:banknotes-solid" width="18" height="18"></iconify-icon>
                    <span>Payment Details</span>
                </a>
            </div>
        </div>

        <!-- Form Panel (9 columns) -->
        <div class="md:col-span-9">
            <form id="settings-form" method="post" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <?php if ($tab === 'general'): ?>
                    <!-- General settings form -->
                    <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                        <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center gap-2">
                            <iconify-icon icon="heroicons:photo-solid" width="18" height="18" class="text-primary"></iconify-icon>
                            <span>Branding & Logo</span>
                        </h3>
                        
                        <div class="grid gap-4 sm:grid-cols-2 items-center">
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-1.5">Site Logo</label>
                                <input type="file" name="site_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/5 file:text-primary hover:file:bg-primary/10 file:cursor-pointer">
                                <span class="block text-[11px] text-slate-400 mt-1.5">Supported: JPG, PNG, WebP or SVG. Maximum 3MB.</span>
                            </div>
                            <div class="flex justify-center border border-slate-100 bg-slate-50/50 p-4 rounded-2xl">
                                <?php if (!empty($settings['site_logo'])): ?>
                                    <div class="text-center">
                                        <span class="block text-[10px] text-slate-400 mb-1.5 uppercase font-bold tracking-wider">Current Logo</span>
                                        <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="h-14 max-w-full object-contain mx-auto">
                                        <input type="hidden" name="current_logo" value="<?= htmlspecialchars($settings['site_logo']) ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <span class="block text-[10px] text-slate-400 mb-1.5 uppercase font-bold tracking-wider">Default Logo</span>
                                        <img src="/assets/images/logo.svg" alt="Default Logo" class="h-14 max-w-full object-contain mx-auto">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php $input('site_name', 'Site / Brand Name', $settings['site_name'] ?? 'GiftVibe'); ?>

                        <?php $input('google_review_url', 'Google Review Link (encourages users who write a review to also post to Google Business)', $settings['google_review_url'] ?? '', 'url', 'https://g.page/r/.../review'); ?>

                        <label class="block space-y-1.5">
                            <span class="text-sm font-semibold text-secondary">Footer Description</span>
                            <textarea name="site_description" rows="3" placeholder="Thoughtful gifts for every person, moment and celebration—all in one place." class="w-full rounded-xl border border-primary/10 p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string)($settings['site_description'] ?? '')) ?></textarea>
                        </label>
                    </div>

                <?php elseif ($tab === 'contact'): ?>
                    <!-- Contact and Socials Form -->
                    <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                        <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center gap-2">
                            <iconify-icon icon="heroicons:envelope-solid" width="18" height="18" class="text-primary"></iconify-icon>
                            <span>Contact Info</span>
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php 
                            $input('site_contact_email', 'Contact Email', $settings['contact_email'] ?? '', 'email'); 
                            $input('site_contact_phone', 'Contact Phone', $settings['contact_phone'] ?? ''); 
                            ?>
                        </div>
                        <label class="block space-y-1.5">
                            <span class="text-sm font-semibold text-secondary">Physical Address</span>
                            <textarea name="site_contact_address" rows="3" placeholder="123 Galle Road, Colombo, Sri Lanka" class="w-full rounded-xl border border-slate-300 p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string)($settings['contact_address'] ?? '')) ?></textarea>
                        </label>
                    </div>

                    <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-5">
                        <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center gap-2">
                            <iconify-icon icon="heroicons:link-solid" width="18" height="18" class="text-primary"></iconify-icon>
                            <span>Social Media Profiles</span>
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <?php 
                            $input('social_facebook', 'Facebook URL', $settings['social_facebook'] ?? '', 'url'); 
                            $input('social_instagram', 'Instagram URL', $settings['social_instagram'] ?? '', 'url'); 
                            $input('social_youtube', 'YouTube URL', $settings['social_youtube'] ?? '', 'url'); 
                            $input('social_twitter', 'Twitter / X URL', $settings['social_twitter'] ?? '', 'url'); 
                            ?>
                        </div>
                    </div>

                <?php elseif ($tab === 'payment'): ?>
                    <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between gap-3 border-b border-primary/10 pb-3"><h3 class="flex items-center gap-2 text-base font-bold text-secondary"><iconify-icon icon="heroicons:building-library-solid" width="18" height="18" class="text-primary"></iconify-icon><span>Customer deposit accounts</span></h3><button type="button" data-add-bank class="rounded-lg border border-primary/10 px-3 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white">+ Add bank</button></div>
                        <p class="text-sm text-secondary/60">Add any number of bank accounts. Customers select one account during checkout.</p>
                        <div data-bank-list class="space-y-4"><?php $bankAccounts=$settings['bank_accounts']??[];if(!$bankAccounts)$bankAccounts=[[]];foreach($bankAccounts as $bank): ?><div data-bank-row class="grid gap-3 rounded-xl border border-primary/10 bg-primary/5 p-4 sm:grid-cols-2"><input required name="bank_name[]" value="<?=htmlspecialchars((string)($bank['bank_name']??''))?>" placeholder="Bank name" class="rounded-xl border border-primary/10 bg-white px-4 py-3 text-sm outline-none focus:border-primary"><input required name="account_name[]" value="<?=htmlspecialchars((string)($bank['account_name']??''))?>" placeholder="Account holder name" class="rounded-xl border border-primary/10 bg-white px-4 py-3 text-sm outline-none focus:border-primary"><input required name="account_number[]" value="<?=htmlspecialchars((string)($bank['account_number']??''))?>" placeholder="Account number" class="rounded-xl border border-primary/10 bg-white px-4 py-3 text-sm outline-none focus:border-primary"><div class="flex gap-2"><input name="branch[]" value="<?=htmlspecialchars((string)($bank['branch']??''))?>" placeholder="Branch" class="min-w-0 flex-1 rounded-xl border border-primary/10 bg-white px-4 py-3 text-sm outline-none focus:border-primary"><button type="button" data-remove-bank class="rounded-xl border border-accent/30 px-3 text-accent hover:bg-accent/10" aria-label="Remove bank">&times;</button></div></div><?php endforeach; ?></div>
                    </div>
                    <script>document.addEventListener('DOMContentLoaded',function(){var list=document.querySelector('[data-bank-list]');function bind(button){button.addEventListener('click',function(){if(list.querySelectorAll('[data-bank-row]').length>1)button.closest('[data-bank-row]').remove();});}list.querySelectorAll('[data-remove-bank]').forEach(bind);document.querySelector('[data-add-bank]').addEventListener('click',function(){var row=list.querySelector('[data-bank-row]').cloneNode(true);row.querySelectorAll('input').forEach(function(input){input.value='';});list.appendChild(row);bind(row.querySelector('[data-remove-bank]'));});});</script>
                <?php elseif ($tab === 'footer'): ?>
                    <!-- Footer Links lists form -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Column 2 Links -->
                        <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-4">
                            <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center justify-between">
                                <span>Quick Links</span>
                                <span class="text-[10px] font-bold bg-slate-100 px-2 py-0.5 rounded-full text-slate-500 uppercase">Col 1</span>
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

                        <!-- Column 3 Links -->
                        <div class="rounded-2xl border bg-white p-6 shadow-sm space-y-4">
                            <h3 class="text-base font-bold text-secondary border-b pb-3 border-slate-100 flex items-center justify-between">
                                <span>Products Links</span>
                                <span class="text-[10px] font-bold bg-slate-100 px-2 py-0.5 rounded-full text-slate-500 uppercase">Col 2</span>
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
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
