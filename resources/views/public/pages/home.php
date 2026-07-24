<?php
/**
 * Public Homepage - Hero Section
 */
?>
<section class="relative bg-slate-50 overflow-hidden py-16 lg:py-24">
    <!-- Background subtle gradient glow -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary-50/40 via-white to-slate-50 pointer-events-none"></div>

    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Column: Content & Search -->
            <div class="lg:col-span-7 space-y-6 sm:space-y-8 animate-slideUp">
                
                <!-- Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-50 border border-primary-100 rounded-full text-xs font-bold text-primary animate-pulse">
                    <span>🎁</span>
                    <span>Premium Gift Delivery in Sri Lanka</span>
                </div>

                <!-- Main SEO Optimized Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight">
                    Send Handpicked <span class="text-primary-600 block sm:inline">Gifts to Sri Lanka</span> with Love
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
                    Make your distance disappear. Order handpicked luxury gift boxes, fresh flower bouquets, and gourmet chocolates with guaranteed same-day delivery across Colombo and major districts.
                </p>

                <!-- Search box component -->
                <div class="bg-white p-2 rounded-card shadow-md max-w-md border border-slate-200/80">
                    <?php component('public/components/common/search-form', [
                        'placeholder' => 'Find birthday, anniversary, or thank you gifts...'
                    ]); ?>
                </div>

                <!-- Quick tags / suggestions -->
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                    <span>Popular Occasions:</span>
                    <a href="?route=component-showcase" class="px-2.5 py-1 bg-white hover:bg-primary-50 hover:text-primary border border-slate-200 rounded-full transition-colors">Birthday</a>
                    <a href="?route=component-showcase" class="px-2.5 py-1 bg-white hover:bg-primary-50 hover:text-primary border border-slate-200 rounded-full transition-colors">Anniversary</a>
                    <a href="?route=component-showcase" class="px-2.5 py-1 bg-white hover:bg-primary-50 hover:text-primary border border-slate-200 rounded-full transition-colors">For Her</a>
                    <a href="?route=component-showcase" class="px-2.5 py-1 bg-white hover:bg-primary-50 hover:text-primary border border-slate-200 rounded-full transition-colors">For Him</a>
                </div>

                <!-- Action CTAs -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <?php component('public/components/common/button', [
                        'label' => 'Explore Best Sellers',
                        'href' => '?route=component-showcase',
                        'variant' => 'primary',
                        'size' => 'lg',
                        'rightIcon' => 'arrow-right'
                    ]); ?>
                    
                    <?php component('public/components/common/whatsapp-cta', [
                        'phone' => '94771234567',
                        'label' => 'Chat with Gifting Expert',
                        'showLabel' => true
                    ]); ?>
                </div>
            </div>

            <!-- Right Column: Visual Product Showcase -->
            <div class="lg:col-span-5 flex justify-center relative animate-fadeIn">
                <!-- Decorative background elements -->
                <div class="absolute -top-6 -left-6 w-32 h-32 bg-primary-100 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-rose-100 rounded-full blur-3xl opacity-60"></div>

                <!-- Floating Main Product Card -->
                <div class="relative w-full max-w-sm rounded-card border border-slate-200/60 bg-white p-4 shadow-card hover:shadow-xl transition-shadow duration-300 animate-float">
                    
                    <!-- Image -->
                    <div class="aspect-square bg-slate-100 rounded-card overflow-hidden relative mb-4">
                        <img 
                            src="<?= BASE_URL ?>/public/assets/images/hero_gift_box.jpg" 
                            alt="Luxury Gourmet Chocolate and Flowers Gift Box" 
                            class="w-full h-full object-cover"
                            width="400"
                            height="400"
                            loading="eager"
                        >
                        <div class="absolute top-2 left-2">
                            <?php component('public/components/common/badge', [
                                'label' => 'Best Seller',
                                'variant' => 'primary'
                            ]); ?>
                        </div>
                    </div>

                    <!-- Product specifications -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs uppercase font-bold text-primary-600 tracking-wider">Curated Gift Box</span>
                            <div class="flex items-center gap-1">
                                <?php component('public/components/common/rating', ['rating' => 5, 'size' => 'xs']); ?>
                                <span class="text-2xs font-bold text-slate-400">(48)</span>
                            </div>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800">The Royal Crimson Delight</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">Fresh red roses bouquet paired with hand-crafted dark Belgian chocolate truffles in a pink velvet box.</p>
                        
                        <div class="flex items-center justify-between pt-2">
                            <?php component('public/components/common/price', [
                                'price' => 7500.00,
                                'oldPrice' => 8900.00
                            ]); ?>
                            <a href="?route=component-showcase" class="text-xs font-bold text-primary-600 hover:text-primary-800 hover:underline">Order Now &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Floating overlay pill: same day delivery -->
                <div class="absolute -bottom-2 -left-4 bg-white/95 backdrop-blur-md border border-slate-200 rounded-full px-4 py-2 shadow-md flex items-center gap-2 hover:scale-105 transition-transform duration-200">
                    <span class="text-sm">🚚</span>
                    <div class="text-left">
                        <p class="text-2xs font-bold text-slate-400 uppercase tracking-wide">Delivery</p>
                        <p class="text-xs font-extrabold text-slate-800">Same-Day Colombo</p>
                    </div>
                </div>

                <!-- Floating overlay pill: rating stats -->
                <div class="absolute top-8 -right-4 bg-white/95 backdrop-blur-md border border-slate-200 rounded-full px-4 py-2 shadow-md flex items-center gap-2 hover:scale-105 transition-transform duration-200">
                    <span class="text-sm">⭐</span>
                    <div class="text-left">
                        <p class="text-xs font-extrabold text-slate-800">4.9 Star Rated</p>
                        <p class="text-2xs font-bold text-slate-400 uppercase tracking-wide">12,000+ Customers</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
