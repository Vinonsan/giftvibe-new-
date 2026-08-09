<?php

declare(strict_types=1);

/**
 * Web routes.
 * @var App\Core\Router $router
 */

$router->get('/', 'Public\HomeController@index');
$router->get('/robots.txt', 'Public\SeoController@robots');
$router->get('/sitemap.xml', 'Public\SeoController@sitemap');
$router->get('/base', 'Public\BaseController@index');
$router->get('/shop', 'Public\ShopController@index');
$router->get('/api/shop/products', 'Public\ShopController@products');
$router->get('/categories', 'Public\ShopController@index');
$router->get('/cart', 'Public\CartController@index');
$router->get('/favorites', 'Public\CartController@favorites');
$router->get('/reviews', 'Public\ReviewController@index');
$router->post('/reviews', 'Public\ReviewController@submit');
$router->post('/checkout/success/review', 'Public\CheckoutController@submitReview');
$router->get('/combos', 'Public\ComboController@index');
$router->get('/about', 'Public\PageController@about');
$router->get('/services', 'Public\PageController@services');
$router->get('/blog', 'Public\PageController@blog');
$router->get('/contact', 'Public\PageController@contact');
$router->get('/privacy', 'Public\PageController@privacy');
$router->get('/terms', 'Public\PageController@terms');
$router->post('/reviews/submit', 'Public\ReviewController@submit');

// Authentication routes
$router->get('/login', 'Public\AuthController@form');
$router->post('/api/auth/login', 'Public\AuthController@login');
$router->post('/api/auth/register', 'Public\AuthController@register');
$router->get('/api/auth/logout', 'Public\AuthController@logout');
$router->get('/api/auth/status', 'Public\AuthController@status');
$router->post('/api/auth/avatar', 'Public\AuthController@updateAvatar');
$router->post('/api/auth/password/request', 'Public\AuthController@requestPasswordReset');
$router->post('/api/auth/password/verify', 'Public\AuthController@verifyPasswordReset');
$router->post('/api/auth/password/reset', 'Public\AuthController@resetPassword');
$router->get('/checkout', 'Public\CheckoutController@index');
$router->post('/checkout', 'Public\CheckoutController@index');
$router->get('/checkout/success', 'Public\CheckoutController@success');

// Admin Authentication routes
$router->get('/admin/login', 'Admin\AuthController@login');
$router->post('/admin/login', 'Admin\AuthController@requestOtp');
$router->get('/admin/login/verify', 'Admin\AuthController@verifyForm');
$router->post('/admin/login/verify', 'Admin\AuthController@verifyOtp');
$router->get('/admin/logout', 'Admin\AuthController@logout');

$router->get('/admin', 'Admin\DashboardController@index');
$router->get('/admin/hero', 'Admin\HeroController@index');
$router->post('/admin/hero', 'Admin\HeroController@index');
$router->get('/admin/categories', 'Admin\CategoryController@index');
$router->post('/admin/categories', 'Admin\CategoryController@index');
$router->get('/admin/categories/view', 'Admin\CategoryController@show');
$router->get('/admin/products', 'Admin\ProductController@index');
$router->post('/admin/products', 'Admin\ProductController@index');
$router->get('/admin/products/view', 'Admin\ProductController@show');
$router->get('/admin/combos', 'Admin\ComboController@index');
$router->post('/admin/combos', 'Admin\ComboController@index');
$router->get('/admin/combos/view', 'Admin\ComboController@show');
$router->get('/admin/cta', 'Admin\CtaController@index');
$router->post('/admin/cta', 'Admin\CtaController@index');
$router->get('/admin/reviews', 'Admin\ReviewController@index');
$router->post('/admin/reviews', 'Admin\ReviewController@index');
$router->get('/admin/faqs', 'Admin\FaqController@index');
$router->post('/admin/faqs', 'Admin\FaqController@index');
$router->get('/admin/orders', 'Admin\OrderController@index');
$router->post('/admin/orders', 'Admin\OrderController@index');
$router->get('/admin/orders/create', 'Admin\OrderController@create');
$router->post('/admin/orders/create', 'Admin\OrderController@create');
$router->get('/admin/orders/view', 'Admin\OrderController@show');
$router->post('/admin/orders/view', 'Admin\OrderController@show');
$router->get('/admin/orders/receipt', 'Admin\OrderController@receipt');
$router->get('/admin/customers', 'Admin\CustomerController@index');
$router->post('/admin/customers', 'Admin\CustomerController@index');
$router->get('/admin/customers/view', 'Admin\CustomerController@show');
$router->post('/admin/customers/view', 'Admin\CustomerController@show');
$router->get('/admin/finance', 'Admin\FinanceController@index');
$router->get('/admin/messages', 'Admin\PlaceholderController@messages');
$router->get('/admin/settings', 'Admin\SettingController@index');
$router->post('/admin/settings', 'Admin\SettingController@index');
$router->get('/admin/expenses', 'Admin\ExpenseController@index');
$router->post('/admin/expenses', 'Admin\ExpenseController@index');
$router->get('/admin/inventory', 'Admin\InventoryController@index');
$router->post('/admin/inventory', 'Admin\InventoryController@index');
$router->get('/admin/investments', 'Admin\InvestmentController@index');
$router->post('/admin/investments', 'Admin\InvestmentController@index');
