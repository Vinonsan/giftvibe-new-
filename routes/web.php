<?php

declare(strict_types=1);

/**
 * Web routes.
 * @var App\Core\Router $router
 */

$router->get('/', 'Public\HomeController@index');
$router->get('/base', 'Public\BaseController@index');
$router->get('/shop', 'Public\ShopController@index');
$router->get('/categories', 'Public\ShopController@index');
$router->get('/cart', 'Public\CartController@index');
$router->get('/reviews', 'Public\ReviewController@index');
$router->post('/reviews', 'Public\ReviewController@submit');
$router->get('/combos', 'Public\ComboController@index');
$router->get('/about', 'Public\PageController@about');
$router->get('/contact', 'Public\PageController@contact');
$router->get('/privacy', 'Public\PageController@privacy');
$router->get('/terms', 'Public\PageController@terms');
$router->post('/reviews/submit', 'Public\ReviewController@submit');
$router->get('/admin', 'Admin\DashboardController@index');
$router->get('/admin/hero', 'Admin\HeroController@index');
$router->post('/admin/hero', 'Admin\HeroController@index');
$router->get('/admin/categories', 'Admin\CategoryController@index');
$router->post('/admin/categories', 'Admin\CategoryController@index');
$router->get('/admin/products', 'Admin\ProductController@index');
$router->post('/admin/products', 'Admin\ProductController@index');
$router->get('/admin/combos', 'Admin\ComboController@index');
$router->post('/admin/combos', 'Admin\ComboController@index');
$router->get('/admin/cta', 'Admin\CtaController@index');
$router->post('/admin/cta', 'Admin\CtaController@index');
$router->get('/admin/reviews', 'Admin\ReviewController@index');
$router->post('/admin/reviews', 'Admin\ReviewController@index');
$router->get('/admin/faqs', 'Admin\FaqController@index');
$router->post('/admin/faqs', 'Admin\FaqController@index');
$router->get('/admin/orders', 'Admin\PlaceholderController@orders');
$router->get('/admin/customers', 'Admin\PlaceholderController@customers');
$router->get('/admin/messages', 'Admin\PlaceholderController@messages');
$router->get('/admin/settings', 'Admin\SettingController@index');
$router->post('/admin/settings', 'Admin\SettingController@index');
