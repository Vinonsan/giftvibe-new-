<?php

declare(strict_types=1);

/**
 * Web routes.
 * @var App\Core\Router $router
 */

$router->get('/', 'Public\HomeController@index');
$router->get('/base', 'Public\BaseController@index');
$router->get('/shop', 'Public\ShopController@index');
$router->get('/admin', 'Admin\DashboardController@index');
$router->get('/admin/hero', 'Admin\HeroController@index');
$router->post('/admin/hero', 'Admin\HeroController@index');
$router->get('/admin/categories', 'Admin\CategoryController@index');
$router->post('/admin/categories', 'Admin\CategoryController@index');
$router->get('/admin/products', 'Admin\ProductController@index');
$router->post('/admin/products', 'Admin\ProductController@index');
