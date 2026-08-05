<?php

declare(strict_types=1);

/**
 * Public Home Hero Section Component — dynamic slider loading slides from DB.
 *
 * @var array $heroSlides   List of active hero slides loaded from database.
 */

$heroSlides = $heroSlides ?? [];
?>

<div id="hero-slider-container" 
     class="relative mx-auto w-full max-w-7xl overflow-hidden rounded-3xl bg-[#0B1528] px-4 transition-colors duration-1000 ease-in-out"
     style="min-height: 520px;">

    <!-- Background overlays for smooth color blending -->
    <div class="absolute inset-0 bg-linear-to-t from-black/20 to-transparent pointer-events-none"></div>

    <!-- Slides container -->
    <div class="relative w-full h-full flex flex-col justify-center py-12  px-8" style="min-height: 520px;">
        <?php foreach ($heroSlides as $index => $slide):
            /* Parse custom packed subtitle format: tag|subText|price|btnText|bgColor */
            $parts = explode('|', $slide['subtitle'] ?? '');
            $tag = $parts[0] ?? '';
            $subText = $parts[1] ?? '';
            $price = $parts[2] ?? '';
            $btnText = $parts[3] ?? 'SHOP NOW';
            $bgColor = $parts[4] ?? 'bg-[#0B1528]';
            
            $isActive = $index === 0;
        ?>
            <!-- Single Slide Panel -->
            <div data-hero-slide="<?= $index ?>" 
                 data-bg-class="<?= htmlspecialchars($bgColor) ?>"
                 class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center w-full transition-all duration-700 absolute inset-x-0 top-1/2 -translate-y-1/2 px-6 <?= $isActive ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 z-0' ?>">
                
                <!-- Left: Text Content (Slides UP when active) -->
                <div class="text-left space-y-4 md:space-y-6">
                    <!-- Red Tag -->
                    <div class="inline-block text-[10px] md:text-xs font-bold tracking-widest text-white uppercase bg-rose-600 px-3 py-1 rounded-md shadow-md shadow-rose-600/10 transform transition-all duration-700 ease-out <?= $isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' ?>"
                         data-slide-el="tag">
                        <?= htmlspecialchars($tag) ?>
                    </div>

                    <!-- Title -->
                    <<?= $index === 0 ? 'h1' : 'h2' ?> class="text-3xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight transform transition-all duration-700 ease-out delay-100 <?= $isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' ?>"
                        data-slide-el="title">
                        <?= htmlspecialchars($slide['title']) ?>
                    </<?= $index === 0 ? 'h1' : 'h2' ?>>

                    <!-- Subtitle descriptor -->
                    <p class="text-slate-300 text-xs md:text-sm font-semibold tracking-widest uppercase transform transition-all duration-700 ease-out delay-200 <?= $isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' ?>"
                       data-slide-el="subText">
                        <?= htmlspecialchars($subText) ?>
                    </p>

                    <!-- Price -->
                    <div class="text-slate-300 text-base md:text-lg font-medium transform transition-all duration-700 ease-out delay-300 <?= $isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' ?>"
                         data-slide-el="price">
                        <span class="text-yellow-400 font-extrabold text-2xl md:text-3xl"><?= htmlspecialchars($price) ?></span>
                    </div>

                    <!-- CTA Button -->
                    <div class="pt-2 transform transition-all duration-700 ease-out delay-400 <?= $isActive ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' ?>"
                         data-slide-el="btn">
                        <a href="<?= htmlspecialchars($slide['link_url'] ?? '/shop') ?>" 
                           class="inline-block bg-primary text-white text-sm font-bold tracking-wide uppercase px-8 py-3.5 rounded-xl shadow-lg shadow-primary/30 transition duration-300 hover:opacity-90 hover:shadow-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/40">
                            <?= htmlspecialchars($btnText) ?>
                        </a>
                    </div>
                </div>

                <!-- Right: Image Content (Slides DOWN when active) -->
                <div class="flex justify-center md:justify-end items-center">
                    <img src="<?= htmlspecialchars($slide['image_path'] ?? '') ?>" 
                         alt="<?= htmlspecialchars($slide['title']) ?>" 
                         width="640"
                         height="480"
                         loading="<?= $index === 0 ? 'eager' : 'lazy' ?>"
                         fetchpriority="<?= $index === 0 ? 'high' : 'auto' ?>"
                         class="max-h-[300px] md:max-h-[420px] object-contain rounded-2xl drop-shadow-[0_20px_40px_rgba(0,0,0,0.5)] transform transition-all duration-1000 ease-out delay-200 <?= $isActive ? 'translate-y-0 opacity-100 scale-100' : '-translate-y-12 scale-95' ?>"
                         data-slide-el="img">
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation dots -->
    <div class="absolute bottom-6 inset-x-0 z-20 flex justify-center items-center gap-2">
        <?php foreach ($heroSlides as $index => $slide): ?>
            <button type="button" 
                    data-slide-dot="<?= $index ?>" 
                    class="h-2.5 rounded-full transition-all duration-300 cursor-pointer focus:outline-none <?= $index === 0 ? 'w-8 bg-primary' : 'w-2.5 bg-white/40 hover:bg-white/70' ?>"
                    aria-label="Go to slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.heroSlider) return;
    window.GiftVibeUI = window.GiftVibeUI || {};

    var container = document.getElementById('hero-slider-container');
    var slides = Array.prototype.slice.call(container.querySelectorAll('[data-hero-slide]'));
    var dots = Array.prototype.slice.call(container.querySelectorAll('[data-slide-dot]'));
    if (slides.length <= 1) return;

    var current = 0;
    var timer = null;

    function activateSlide(index) {
        var prevSlide = slides[current];
        var nextSlide = slides[index];

        /* Reset the elements of the old slide */
        prevSlide.classList.replace('opacity-100', 'opacity-0');
        prevSlide.classList.replace('pointer-events-auto', 'pointer-events-none');
        prevSlide.classList.replace('z-10', 'z-0');
        
        prevSlide.querySelectorAll('[data-slide-el="tag"], [data-slide-el="title"], [data-slide-el="subText"], [data-slide-el="price"], [data-slide-el="btn"]').forEach(function(el) {
            el.classList.replace('translate-y-0', 'translate-y-8');
            el.classList.replace('opacity-100', 'opacity-0');
        });
        
        var prevImg = prevSlide.querySelector('[data-slide-el="img"]');
        if (prevImg) {
            prevImg.classList.replace('translate-y-0', '-translate-y-12');
            prevImg.classList.replace('opacity-100', 'opacity-0');
            prevImg.classList.replace('scale-100', 'scale-95');
        }

        /* Activate next slide container opacity & position */
        nextSlide.classList.replace('opacity-0', 'opacity-100');
        nextSlide.classList.replace('pointer-events-none', 'pointer-events-auto');
        nextSlide.classList.replace('z-0', 'z-10');

        /* Trigger text slide-up with requestAnimationFrame delay for clean transition rendering */
        requestAnimationFrame(function() {
            nextSlide.querySelectorAll('[data-slide-el="tag"], [data-slide-el="title"], [data-slide-el="subText"], [data-slide-el="price"], [data-slide-el="btn"]').forEach(function(el) {
                el.classList.replace('translate-y-8', 'translate-y-0');
                el.classList.replace('opacity-0', 'opacity-100');
            });
            
            var nextImg = nextSlide.querySelector('[data-slide-el="img"]');
            if (nextImg) {
                nextImg.classList.replace('-translate-y-12', 'translate-y-0');
                nextImg.classList.replace('opacity-0', 'opacity-100');
                nextImg.classList.replace('scale-95', 'scale-100');
            }
        });

        /* Background color swap */
        var bgClass = nextSlide.getAttribute('data-bg-class');
        container.className = container.className.replace(/bg-\[[^\]]+\]/g, bgClass);

        /* Update dot highlights */
        dots[current].className = 'h-2.5 w-2.5 bg-white/40 hover:bg-white/70 rounded-full transition-all duration-300 cursor-pointer focus:outline-none';
        dots[index].className = 'h-2.5 w-8 bg-primary rounded-full transition-all duration-300 cursor-pointer focus:outline-none';

        current = index;
    }

    function next() {
        var index = (current + 1) % slides.length;
        activateSlide(index);
    }

    function startTimer() {
        stopTimer();
        timer = setInterval(next, 5000);
    }

    function stopTimer() {
        if (timer) clearInterval(timer);
    }

    /* Dot interactions */
    dots.forEach(function(dot, idx) {
        dot.addEventListener('click', function() {
            if (idx === current) return;
            activateSlide(idx);
            startTimer();
        });
    });

    /* Pause autoplay on hover to ensure smooth readability */
    container.addEventListener('mouseenter', stopTimer);
    container.addEventListener('mouseleave', startTimer);

    startTimer();
    window.GiftVibeUI.heroSlider = true;
})();
</script>
