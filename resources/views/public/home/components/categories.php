<?php
$categories = $categories ?? [];
$fallbackImages = [
    '/assets/images/hero_slide_1.jpg',
    '/assets/images/hero_slide_2.jpg',
    '/assets/images/hero_slide_3.jpg',
];
?>
<section class="bg-white py-8 sm:py-20" aria-labelledby="category-heading">
    <div class="mx-auto max-w-7xl ">
   
        <?php if ($categories): ?>
            <div data-category-wrap class="mt-9">
                <div data-category-slider class="flex snap-x snap-mandatory gap-4 overflow-x-auto py-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <?php foreach ($categories as $index => $category):
                    $name = (string) ($category['name'] ?? $category['title'] ?? 'Gift collection');
                    $image = (string) ($category['image_path'] ?? $category['image'] ?? $fallbackImages[$index % count($fallbackImages)]);
                    if (str_starts_with($image, 'public/')) {
                        $image = '/' . substr($image, strlen('public/'));
                    } elseif ($image !== '' && !str_starts_with($image, '/') && !str_starts_with($image, 'http')) {
                        $image = '/' . $image;
                    }
                    $slug = (string) ($category['slug'] ?? '');
                    $href = trim((string) ($category['link_url'] ?? '')) ?: ($slug !== '' ? '/shop?category=' . rawurlencode($slug) : '/shop');
                ?>
                    <a href="<?= htmlspecialchars($href) ?>" aria-label="<?= htmlspecialchars($name) ?>" class="group h-44 w-[46%] shrink-0 snap-start overflow-hidden rounded-xl border-2 border-secondary bg-white p-1.5 transition hover:border-primary sm:w-[30%] md:h-36 md:w-[calc((100%_-_7rem)/8)]">
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" class="h-full w-full rounded-lg object-cover transition duration-500 group-hover:scale-[1.02]">
                    </a>
                <?php endforeach; ?>
                </div>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <button type="button" data-category-prev aria-label="Previous categories" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-secondary bg-white text-secondary shadow-sm transition hover:bg-secondary hover:text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg></button>
                    <button type="button" data-category-next aria-label="Next categories" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-secondary bg-secondary text-white shadow-sm transition hover:bg-primary"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg></button>
                </div>
            </div>
        <?php else: ?>
            <div class="mt-9 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-14 text-center text-sm text-slate-500">Categories added from admin will appear here.</div>
        <?php endif; ?>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var slider = document.querySelector('[data-category-slider]');
    var wrap = document.querySelector('[data-category-wrap]');
    if (!slider) return;
    var timer = null;

    function move(direction) {
        var card = slider.querySelector('a');
        var step = (card ? card.offsetWidth : 160) + 16;
        var atEnd = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 4;
        var atStart = slider.scrollLeft <= 4;
        if (direction > 0 && atEnd) {
            slider.scrollTo({left: 0, behavior: 'smooth'});
        } else if (direction < 0 && atStart) {
            slider.scrollTo({left: slider.scrollWidth, behavior: 'smooth'});
        } else {
            slider.scrollBy({left: direction * step, behavior: 'smooth'});
        }
    }

    function startAutoPlay() {
        stopAutoPlay();
        timer = window.setInterval(function () { move(1); }, 3000);
    }
    function stopAutoPlay() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }

    document.querySelector('[data-category-prev]')?.addEventListener('click', function () { move(-1); startAutoPlay(); });
    document.querySelector('[data-category-next]')?.addEventListener('click', function () { move(1); startAutoPlay(); });
    wrap?.addEventListener('mouseenter', stopAutoPlay);
    wrap?.addEventListener('mouseleave', startAutoPlay);
    wrap?.addEventListener('focusin', stopAutoPlay);
    wrap?.addEventListener('focusout', startAutoPlay);
    document.addEventListener('visibilitychange', function () { document.hidden ? stopAutoPlay() : startAutoPlay(); });
    startAutoPlay();
});
</script>
