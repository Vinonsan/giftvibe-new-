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

<div class="flex flex-col py-8 gap-4">
     
    <?php require BASE_PATH . '/resources/views/public/home/components/hero.php'; ?>
    
    <div class="scroll-reveal">
        <?php require BASE_PATH . '/resources/views/public/home/components/categories.php'; ?>
    </div>

    <div class="scroll-reveal">
        <?php require BASE_PATH . '/resources/views/public/home/components/products.php'; ?>
    </div>
    
    <div class="scroll-reveal">
        <?php require BASE_PATH . '/resources/views/public/home/components/combos.php'; ?>
    </div>
    
    <div class="scroll-reveal">
        <?php $cta = $homepageCtas['home_after_categories'] ?? null; require BASE_PATH . '/resources/views/components/public/cta-banner.php'; ?>
    </div>
    
    <div class="scroll-reveal">
        <?php require BASE_PATH . '/resources/views/public/home/components/testimonials.php'; ?>
    </div>
    
    <div class="scroll-reveal">
        <?php require BASE_PATH . '/resources/views/public/home/components/faqs.php'; ?>
    </div>
    
    <div class="scroll-reveal">
        <?php $cta = $homepageCtas['home_between_products_combos'] ?? null; require BASE_PATH . '/resources/views/components/public/cta-banner.php'; ?>
    </div>
</div>
