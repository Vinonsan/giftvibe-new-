<?php

use Controllers\AuthController;
use Controllers\Customer\CustomGiftRequestController;
use Controllers\Customer\OrderController;
use Controllers\Customer\PortalController;
use Controllers\Customer\ReviewController;
use Middleware\CustomerAuth;

return [
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthController::class, 'showCustomerLogin']],
    ['method' => 'POST', 'path' => '/login', 'handler' => [AuthController::class, 'customerLogin']],
    ['method' => 'GET', 'path' => '/signup', 'handler' => [AuthController::class, 'showCustomerSignup']],
    ['method' => 'POST', 'path' => '/signup', 'handler' => [AuthController::class, 'customerSignup']],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [AuthController::class, 'logout']],
    ['method' => 'GET', 'path' => '/customer/dashboard', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'dashboard']],
    ['method' => 'GET', 'path' => '/customer/profile', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'profile']],
    ['method' => 'POST', 'path' => '/customer/profile', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'updateProfile']],
    ['method' => 'GET', 'path' => '/customer/addresses', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'addresses']],
    ['method' => 'POST', 'path' => '/customer/addresses', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'storeAddress']],
    ['method' => 'POST', 'path' => '/customer/addresses/{id}/default', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'defaultAddress']],
    ['method' => 'POST', 'path' => '/customer/addresses/{id}/delete', 'middleware' => [CustomerAuth::class], 'handler' => [PortalController::class, 'deleteAddress']],
    ['method' => 'GET', 'path' => '/customer/orders', 'middleware' => [CustomerAuth::class], 'handler' => [OrderController::class, 'index']],
    ['method' => 'GET', 'path' => '/customer/orders/{orderNumber}', 'middleware' => [CustomerAuth::class], 'handler' => [OrderController::class, 'show']],
    ['method' => 'POST', 'path' => '/reviews', 'middleware' => [CustomerAuth::class], 'handler' => [ReviewController::class, 'store']],
    ['method' => 'POST', 'path' => '/custom-gifts', 'middleware' => [CustomerAuth::class], 'handler' => [CustomGiftRequestController::class, 'store']],
    ['method' => 'GET', 'path' => '/customer/requests', 'middleware' => [CustomerAuth::class], 'handler' => [CustomGiftRequestController::class, 'index']],
    ['method' => 'GET', 'path' => '/customer/requests/{requestNumber}', 'middleware' => [CustomerAuth::class], 'handler' => [CustomGiftRequestController::class, 'show']],
    ['method' => 'POST', 'path' => '/customer/requests/{requestNumber}/reply', 'middleware' => [CustomerAuth::class], 'handler' => [CustomGiftRequestController::class, 'reply']],
];
