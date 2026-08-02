<?php

declare(strict_types=1);

/**
 * Home page section — rendered as a child of the public layout.
 *
 * @var string $title
 * @var string $message
 * @var array  $heroSlides
 */
?>

<!-- Premium Dynamic Hero Slider -->
<?php require BASE_PATH . '/resources/views/public/home/components/hero.php'; ?>

<?php require BASE_PATH . '/resources/views/public/home/components/categories.php'; ?>

<?php require BASE_PATH . '/resources/views/public/home/components/products.php'; ?>

<!-- Rest of homepage placeholder content -->
<section class="bg-gradient-to-br from-indigo-50 via-white to-pink-50 py-16">
    <div class="mx-auto max-w-7xl px-6 text-center">
        <span class="rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-primary">
            Gift Vibe MVC
        </span>

        <h2 class="mt-6 text-5xl font-extrabold tracking-tight text-secondary sm:text-6xl">
            <?= htmlspecialchars($message) ?>
        </h2>

        <p class="mt-5 max-w-xl mx-auto text-lg text-slate-500">
            Your Core PHP MVC app is running with layouts, global components, and Tailwind CSS v4.
        </p>

        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="/admin" class="rounded-full bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:opacity-90">
                Open Admin
            </a>
            <a href="/shop" class="rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-secondary transition hover:border-primary hover:text-primary">
                Browse Shop
            </a>
        </div>
    </div>
</section>
