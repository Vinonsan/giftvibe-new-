<?php

use Controllers\PublicSite\CatalogController;
use Controllers\PublicSite\CartController;
use Controllers\PublicSite\CheckoutController;
use Controllers\PublicSite\ContactController;
use Controllers\Customer\CustomGiftRequestController;

return [
    ['method' => 'GET', 'path' => '/', 'handler' => [CatalogController::class, 'home']],
    ['method' => 'GET', 'path' => '/shop', 'handler' => [CatalogController::class, 'shop']],
    ['method' => 'GET', 'path' => '/search', 'handler' => [CatalogController::class, 'search']],
    ['method' => 'GET', 'path' => '/categories', 'handler' => [CatalogController::class, 'categoriesPage']],
    ['method' => 'GET', 'path' => '/about', 'handler' => [CatalogController::class, 'about']],
    ['method' => 'GET', 'path' => '/category/{slug}', 'handler' => [CatalogController::class, 'category']],
    ['method' => 'GET', 'path' => '/product/{slug}', 'handler' => [CatalogController::class, 'product']],
    ['method' => 'POST', 'path' => '/wishlist/toggle', 'handler' => [CatalogController::class, 'toggleWishlist']],
    ['method' => 'GET', 'path' => '/cart', 'handler' => [CartController::class, 'index']],
    ['method' => 'POST', 'path' => '/cart/add', 'handler' => [CartController::class, 'add']],
    ['method' => 'POST', 'path' => '/cart/update', 'handler' => [CartController::class, 'update']],
    ['method' => 'POST', 'path' => '/cart/remove', 'handler' => [CartController::class, 'remove']],
    ['method' => 'GET', 'path' => '/checkout', 'handler' => [CheckoutController::class, 'show']],
    ['method' => 'POST', 'path' => '/checkout', 'handler' => [CheckoutController::class, 'store']],
    ['method' => 'GET', 'path' => '/contact', 'handler' => [ContactController::class, 'show']],
    ['method' => 'POST', 'path' => '/contact', 'handler' => [ContactController::class, 'store']],
    ['method' => 'GET', 'path' => '/custom-gifts', 'handler' => [CustomGiftRequestController::class, 'create']],
];
