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
            <div data-category-slider class="mt-9 flex snap-x snap-mandatory gap-4 overflow-x-auto pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <?php foreach ($categories as $index => $category):
                    $name = (string) ($category['name'] ?? $category['title'] ?? 'Gift collection');
                    $image = (string) ($category['image_path'] ?? $category['image'] ?? $fallbackImages[$index % count($fallbackImages)]);
                    $slug = (string) ($category['slug'] ?? '');
                    $href = trim((string) ($category['link_url'] ?? '')) ?: ($slug !== '' ? '/shop?category=' . rawurlencode($slug) : '/shop');
                ?>
                    <a href="<?= htmlspecialchars($href) ?>" aria-label="<?= htmlspecialchars($name) ?>" class="group h-64 w-[78%] shrink-0 snap-start overflow-hidden rounded-2xl border-2 border-secondary bg-white p-2 transition hover:border-primary sm:h-72 sm:w-[45%] lg:w-[30%] xl:w-[23%]">
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" class="h-full w-full rounded-xl object-cover transition duration-500 group-hover:scale-[1.02]">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="mt-9 rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-14 text-center text-sm text-slate-500">Categories added from admin will appear here.</div>
        <?php endif; ?>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var slider = document.querySelector('[data-category-slider]');
    if (!slider) return;
    function move(direction) {
        var card = slider.querySelector('a');
        slider.scrollBy({left: direction * ((card ? card.offsetWidth : 280) + 16), behavior: 'smooth'});
    }
    document.querySelector('[data-category-prev]')?.addEventListener('click', function () { move(-1); });
    document.querySelector('[data-category-next]')?.addEventListener('click', function () { move(1); });
});
</script>
