<?php

use App\Core\View;
use Controllers\Admin\CatalogController;
use Controllers\Admin\CustomerController;
use Controllers\Admin\DashboardController;
use Controllers\Admin\DeliveryController;
use Controllers\Admin\ExpenseController;
use Controllers\Admin\MessageController;
use Controllers\Admin\NotificationController;
use Controllers\Admin\OrderController;
use Controllers\Admin\ReviewController;
use Controllers\Admin\SettingController;
use Controllers\AuthController;
use Helpers\AdminNavigation;
use Middleware\StaffAuth;

$adminNavigation = static function (string $active = 'dashboard'): array {
    return AdminNavigation::make($active);
};

return [
    ['method' => 'GET', 'path' => '/', 'handler' => static fn () => redirect('/admin/dashboard')],
    ['method' => 'GET', 'path' => '/login', 'handler' => [AuthController::class, 'showAdminLogin']],
    ['method' => 'POST', 'path' => '/login', 'handler' => [AuthController::class, 'adminLogin']],
    ['method' => 'POST', 'path' => '/login/request-otp', 'handler' => [AuthController::class, 'requestAdminOtp']],
    ['method' => 'POST', 'path' => '/login/verify-otp', 'handler' => [AuthController::class, 'verifyAdminOtp']],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [AuthController::class, 'adminLogout']],
    [
        'method' => 'GET',
        'path' => '/dashboard',
        'middleware' => [StaffAuth::class],
        'handler' => [DashboardController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/component-showcase',
        'middleware' => [StaffAuth::class],
        'handler' => static function () use ($adminNavigation): void {
            View::renderPage('admin/pages/component-showcase', [
                'title' => 'Admin UI Component Showcase',
                'adminNavigation' => $adminNavigation('dashboard'),
                'breadcrumbs' => [
                    ['label' => 'Admin', 'url' => url('/admin/dashboard')],
                    ['label' => 'Showcase'],
                ],
            ], 'admin/layouts/admin');
        },
    ],
    ['method' => 'GET', 'path' => '/orders', 'middleware' => [StaffAuth::class], 'handler' => [OrderController::class, 'index']],
    ['method' => 'GET', 'path' => '/orders/{id}', 'middleware' => [StaffAuth::class], 'handler' => [OrderController::class, 'show']],
    ['method' => 'POST', 'path' => '/orders/{id}/status', 'middleware' => [StaffAuth::class], 'handler' => [OrderController::class, 'updateStatus']],
    ['method' => 'POST', 'path' => '/orders/{id}/payment', 'middleware' => [StaffAuth::class], 'handler' => [OrderController::class, 'verifyPayment']],
    ['method' => 'GET', 'path' => '/deliveries', 'middleware' => [StaffAuth::class], 'handler' => [DeliveryController::class, 'index']],
    ['method' => 'POST', 'path' => '/deliveries/{orderId}/parcel', 'middleware' => [StaffAuth::class], 'handler' => [DeliveryController::class, 'saveParcel']],
    ['method' => 'POST', 'path' => '/deliveries/{orderId}/status', 'middleware' => [StaffAuth::class], 'handler' => [DeliveryController::class, 'updateStatus']],
    ['method' => 'POST', 'path' => '/deliveries/reminders/{id}/complete', 'middleware' => [StaffAuth::class], 'handler' => [DeliveryController::class, 'completeReminder']],
    ['method' => 'GET', 'path' => '/reviews', 'middleware' => [StaffAuth::class], 'handler' => [ReviewController::class, 'index']],
    ['method' => 'POST', 'path' => '/reviews/{id}/status', 'middleware' => [StaffAuth::class], 'handler' => [ReviewController::class, 'updateStatus']],
    ['method' => 'GET', 'path' => '/messages', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'contacts']],
    ['method' => 'GET', 'path' => '/messages/{id}', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'contactShow']],
    ['method' => 'POST', 'path' => '/messages/{id}/reply', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'contactReply']],
    ['method' => 'GET', 'path' => '/custom-requests', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'requests']],
    ['method' => 'GET', 'path' => '/custom-requests/{id}', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'requestShow']],
    ['method' => 'POST', 'path' => '/custom-requests/{id}/reply', 'middleware' => [StaffAuth::class], 'handler' => [MessageController::class, 'requestReply']],
    ['method' => 'GET', 'path' => '/notifications', 'middleware' => [StaffAuth::class], 'handler' => [NotificationController::class, 'index']],
    ['method' => 'POST', 'path' => '/notifications/{id}/read', 'middleware' => [StaffAuth::class], 'handler' => [NotificationController::class, 'markRead']],
    ['method' => 'GET', 'path' => '/expenses', 'middleware' => [StaffAuth::class], 'handler' => [ExpenseController::class, 'index']],
    ['method' => 'POST', 'path' => '/expenses', 'middleware' => [StaffAuth::class], 'handler' => [ExpenseController::class, 'store']],
    ['method' => 'GET', 'path' => '/expenses/{id}/receipt', 'middleware' => [StaffAuth::class], 'handler' => [ExpenseController::class, 'receipt']],
    ['method' => 'POST', 'path' => '/expenses/{id}/status', 'middleware' => [StaffAuth::class], 'handler' => [ExpenseController::class, 'update']],
    ['method' => 'GET', 'path' => '/reports', 'middleware' => [StaffAuth::class], 'handler' => [ExpenseController::class, 'reports']],
    ['method' => 'GET', 'path' => '/products', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'products']],
    ['method' => 'POST', 'path' => '/products', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'storeProduct']],
    ['method' => 'GET', 'path' => '/products/{id}', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'productShow']],
    ['method' => 'POST', 'path' => '/products/{id}', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'updateProduct']],
    ['method' => 'POST', 'path' => '/products/{id}/status', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'productStatus']],
    ['method' => 'GET', 'path' => '/categories', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'categories']],
    ['method' => 'POST', 'path' => '/categories', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'storeCategory']],
    ['method' => 'POST', 'path' => '/categories/{id}', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'updateCategory']],
    ['method' => 'POST', 'path' => '/categories/{id}/status', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'categoryStatus']],
    ['method' => 'POST', 'path' => '/categories/{id}/delete', 'middleware' => [StaffAuth::class], 'handler' => [CatalogController::class, 'deleteCategory']],
    ['method' => 'GET', 'path' => '/customers', 'middleware' => [StaffAuth::class], 'handler' => [CustomerController::class, 'index']],
    ['method' => 'GET', 'path' => '/settings', 'middleware' => [StaffAuth::class], 'handler' => [SettingController::class, 'index']],
    ['method' => 'POST', 'path' => '/settings', 'middleware' => [StaffAuth::class], 'handler' => [SettingController::class, 'update']],
];
