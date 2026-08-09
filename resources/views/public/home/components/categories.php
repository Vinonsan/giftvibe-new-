<?php
$categories = $categories ?? [];
$fallbackImages = [
    '/assets/images/hero_slide_1.jpg',
    '/assets/images/hero_slide_2.jpg',
    '/assets/images/hero_slide_3.jpg',
];
?>
<section class="bg-white" aria-labelledby="category-heading">
    <div class="mx-auto max-w-7xl">
   
        <?php if ($categories): ?>
            <div data-category-wrap class="py-8">
                <div data-category-slider class="flex cursor-grab snap-x snap-mandatory gap-4 overflow-x-auto py-1 select-none active:cursor-grabbing [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <?php foreach ($categories as $index => $category):
                    $name = (string) ($category['name'] ?? $category['title'] ?? 'Gift collection');
                    $image = (string) ($category['image_path'] ?? $category['image'] ?? $fallbackImages[$index % count($fallbackImages)]);
                    if (str_starts_with($image, 'public/')) {
                        $image = '/' . substr($image, strlen('public/'));
                    } elseif ($image !== '' && !str_starts_with($image, '/') && !str_starts_with($image, 'http')) {
                        $image = '/' . $image;
                    }
                    $slug = (string) ($category['slug'] ?? '');
                    $href = $slug !== '' ? '/shop?category=' . rawurlencode($slug) : '/shop';
                ?>
                    <a href="<?= htmlspecialchars($href) ?>" aria-label="<?= htmlspecialchars($name) ?>" class="group h-44 w-[46%] shrink-0 snap-start overflow-hidden rounded-xl border-2 border-secondary bg-white  transition hover:border-primary sm:w-[30%] md:h-36 md:w-[calc((100%_-_7rem)/8)]">
                        <div class="img-skeleton h-full w-full rounded-lg">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars(trim((string) ($category['image_alt_text'] ?? '')) ?: ('Shop ' . $name . ' at GiftVibe')) ?>" width="320" height="240" loading="lazy" decoding="async" class="lazy-img h-full w-full rounded-lg object-cover transition duration-500 group-hover:scale-[1.02]" onload="this.classList.add('loaded'); this.parentElement.classList.remove('img-skeleton');">
                        </div>
                    </a>
                <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class=" rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-14 text-center text-sm text-slate-500">Categories added from admin will appear here.</div>
        <?php endif; ?>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var slider = document.querySelector('[data-category-slider]');
    if (!slider) return;
    var dragging = false;
    var moved = false;
    var startX = 0;
    var startScroll = 0;

    slider.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'touch') return;
        dragging = true;
        moved = false;
        startX = event.clientX;
        startScroll = slider.scrollLeft;
        slider.setPointerCapture(event.pointerId);
    });
    slider.addEventListener('pointermove', function (event) {
        if (!dragging) return;
        var distance = event.clientX - startX;
        if (Math.abs(distance) > 5) moved = true;
        slider.scrollLeft = startScroll - distance;
    });
    slider.addEventListener('pointerup', function (event) {
        if (!dragging) return;
        dragging = false;
        slider.releasePointerCapture(event.pointerId);
    });
    slider.addEventListener('pointercancel', function () { dragging = false; });
    slider.addEventListener('dragstart', function (event) { event.preventDefault(); });
    slider.addEventListener('click', function (event) {
        if (moved) { event.preventDefault(); event.stopPropagation(); moved = false; }
    }, true);
});
</script>
