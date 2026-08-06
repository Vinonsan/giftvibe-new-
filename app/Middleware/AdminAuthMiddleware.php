<?php

declare(strict_types=1);

namespace App\Middleware;

class AdminAuthMiddleware
{
    public function handle(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $adminUser = $_SESSION['admin_user'] ?? null;
        $authToken = $_SESSION['admin_auth_token'] ?? null;

        if (!$adminUser || !$authToken) {
            $_SESSION['admin_login_error'] = 'Access denied. Please log in first.';
            header('Location: /admin/login', true, 303);
            exit;
        }
    }
}
