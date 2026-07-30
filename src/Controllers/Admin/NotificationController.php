<?php

namespace Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class NotificationController
{
    public function index(): void
    {
        $notifications = Database::instance()->fetchAll(
            'SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 150'
        );
        View::renderPage('admin/pages/notifications/index', [
            'title' => 'Admin Notifications',
            'adminNavigation' => AdminNavigation::make('notifications'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Notifications']],
            'notifications' => $notifications,
        ], 'admin/layouts/admin');
    }

    public function markRead(string $id): void
    {
        if (verify_csrf($_POST['_token'] ?? null)) {
            Database::instance()->execute(
                'UPDATE admin_notifications SET status = "read", read_at = NOW() WHERE id = :id',
                ['id' => (int) $id]
            );
        }
        redirect('/admin/notifications');
    }
}
