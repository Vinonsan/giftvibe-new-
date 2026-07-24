<?php
/**
 * Variables:
 * @var array|null $navigation
 */
?>
<header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo -->
        <?php component('public/components/common/logo'); ?>

        <!-- Desktop Navigation -->
        <?php component('public/components/common/desktop-navbar', ['navigation' => $navigation ?? []]); ?>

        <!-- Utilities -->
        <div class="flex items-center gap-2">
            <!-- Search Button -->
            <?php component('public/components/common/icon-button', [
                'icon' => 'search',
                'label' => 'Search Website',
                'variant' => 'ghost',
                'id' => 'header-search-btn'
            ]); ?>

            <!-- Contact/WhatsApp -->
            <?php component('public/components/common/whatsapp-cta', [
                'phone' => '94771234567',
                'label' => 'Chat with Us',
                'showLabel' => false
            ]); ?>

            <!-- Mobile Menu Toggle Button -->
            <?php component('public/components/common/mobile-navbar'); ?>
        </div>
    </div>
    <!-- Mobile Menu Container -->
    <?php component('public/components/common/mobile-menu', ['navigation' => $navigation ?? []]); ?>
</header>