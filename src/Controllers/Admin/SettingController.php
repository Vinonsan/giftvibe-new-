<?php

namespace Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class SettingController
{
    public function index(?string $message = null): void
    {
        $settings = Database::instance()->fetchAll('SELECT * FROM settings ORDER BY setting_group ASC, setting_key ASC');
        View::renderPage('admin/pages/settings/index', [
            'title' => 'Settings',
            'adminNavigation' => AdminNavigation::make('settings'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Settings']],
            'settings' => $settings,
            'message' => $message,
        ], 'admin/layouts/admin');
    }

    public function update(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index('Your session expired. Try again.');
            return;
        }

        foreach (($_POST['settings'] ?? []) as $key => $value) {
            Database::instance()->execute(
                'UPDATE settings SET setting_value = :value WHERE setting_key = :key',
                ['value' => trim((string) $value), 'key' => (string) $key]
            );
        }

        $this->index('Settings saved.');
    }
}
