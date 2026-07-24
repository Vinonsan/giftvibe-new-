<?php
/**
 * Variables:
 * @var array|null $footerData
 */
?>
<footer class="bg-slate-900 text-slate-400 border-t border-slate-800">
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-4">
                <h3 class="text-white text-lg font-bold">Gift Vibe LK</h3>
                <p class="text-sm">The best handpicked gifts for your loved ones in Sri Lanka. Sending love across distances.</p>
                <?php component('public/components/common/social-links'); ?>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/products" class="hover:text-white transition-colors">Shop All</a></li>
                    <li><a href="/categories" class="hover:text-white transition-colors">Categories</a></li>
                    <li><a href="/occasions" class="hover:text-white transition-colors">Occasions</a></li>
                    <li><a href="/blog" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/contact" class="hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="/faq" class="hover:text-white transition-colors">FAQs</a></li>
                    <li><a href="/terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Newsletter</h4>
                <p class="text-sm mb-4">Subscribe to receive gift guides and special discount alerts.</p>
                <?php component('public/components/common/newsletter-form'); ?>
            </div>
        </div>
        <div class="mt-8 border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-500">&copy; <?= date('Y') ?> Gift Vibe LK. All rights reserved.</p>
            <div class="flex gap-4">
                <svg class="h-6 w-auto opacity-50" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="60" rx="6" fill="#1434CB"/><circle cx="35" cy="30" r="16" fill="#F93F55" fill-opacity="0.8"/><circle cx="65" cy="30" r="16" fill="#FFC300" fill-opacity="0.8"/></svg>
                <svg class="h-6 w-auto opacity-50" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="60" rx="6" fill="#0A0B0D"/><path d="M10 10H90V50H10V10Z" fill="white"/><path d="M25 15L45 35L25 50" stroke="#1A73E8" stroke-width="8"/></svg>
            </div>
        </div>
    </div>
</footer>