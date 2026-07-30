<?php

namespace Controllers\Customer;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class PortalController
{
    public function dashboard(): void
    {
        $userId = Auth::id();
        $counts = [
            'wishlist' => Database::instance()->fetch('SELECT COUNT(*) AS total FROM wishlists WHERE user_id = :id', ['id' => $userId])['total'] ?? 0,
            'addresses' => Database::instance()->fetch('SELECT COUNT(*) AS total FROM customer_addresses WHERE user_id = :id', ['id' => $userId])['total'] ?? 0,
            'orders' => Database::instance()->fetch('SELECT COUNT(*) AS total FROM orders WHERE user_id = :id', ['id' => $userId])['total'] ?? 0,
            'requests' => Database::instance()->fetch('SELECT COUNT(*) AS total FROM custom_gift_requests WHERE user_id = :id', ['id' => $userId])['total'] ?? 0,
        ];

        View::renderPage('customer/pages/dashboard', [
            'title' => 'Customer Dashboard | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'counts' => $counts,
        ], 'public/layouts/main');
    }

    public function profile(?string $message = null, ?string $error = null): void
    {
        View::renderPage('customer/pages/profile', [
            'title' => 'My Profile | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'user' => Auth::user(),
            'message' => $message,
            'error' => $error,
        ], 'public/layouts/main');
    }

    public function updateProfile(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->profile(null, 'Your session expired. Try again.');
            return;
        }

        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $phone = preg_replace('/\D+/', '', (string) ($_POST['phone'] ?? '')) ?: null;

        if ($firstName === '') {
            $this->profile(null, 'First name is required.');
            return;
        }

        Database::instance()->execute(
            'UPDATE users SET first_name = :first_name, last_name = :last_name, phone = :phone WHERE id = :id',
            ['first_name' => $firstName, 'last_name' => $lastName, 'phone' => $phone, 'id' => Auth::id()]
        );

        $user = Database::instance()->fetch(
            'SELECT users.*, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.id = :id',
            ['id' => Auth::id()]
        );
        if ($user) {
            Auth::login($user);
        }

        $this->profile('Profile updated.');
    }

    public function addresses(?string $message = null, ?string $error = null): void
    {
        $addresses = Database::instance()->fetchAll(
            'SELECT * FROM customer_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC',
            ['user_id' => Auth::id()]
        );

        View::renderPage('customer/pages/addresses', [
            'title' => 'My Addresses | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'addresses' => $addresses,
            'message' => $message,
            'error' => $error,
        ], 'public/layouts/main');
    }

    public function storeAddress(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->addresses(null, 'Your session expired. Try again.');
            return;
        }

        $required = ['recipient_name', 'phone', 'address_line_1', 'city', 'district'];
        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $this->addresses(null, 'Please complete all required address fields.');
                return;
            }
        }

        $isDefault = isset($_POST['is_default']) ? 1 : 0;
        $existing = Database::instance()->fetch('SELECT COUNT(*) AS total FROM customer_addresses WHERE user_id = :id', ['id' => Auth::id()]);
        if ((int) ($existing['total'] ?? 0) === 0) {
            $isDefault = 1;
        }
        if ($isDefault) {
            Database::instance()->execute('UPDATE customer_addresses SET is_default = 0 WHERE user_id = :id', ['id' => Auth::id()]);
        }

        Database::instance()->execute(
            'INSERT INTO customer_addresses
             (user_id, label, recipient_name, phone, address_line_1, address_line_2, city, district, province, postal_code, is_default)
             VALUES (:user_id, :label, :recipient_name, :phone, :address_line_1, :address_line_2, :city, :district, :province, :postal_code, :is_default)',
            [
                'user_id' => Auth::id(),
                'label' => trim((string) ($_POST['label'] ?? 'Home')) ?: 'Home',
                'recipient_name' => trim((string) $_POST['recipient_name']),
                'phone' => trim((string) $_POST['phone']),
                'address_line_1' => trim((string) $_POST['address_line_1']),
                'address_line_2' => trim((string) ($_POST['address_line_2'] ?? '')),
                'city' => trim((string) $_POST['city']),
                'district' => trim((string) $_POST['district']),
                'province' => trim((string) ($_POST['province'] ?? '')),
                'postal_code' => trim((string) ($_POST['postal_code'] ?? '')),
                'is_default' => $isDefault,
            ]
        );

        $this->addresses('Address saved.');
    }

    public function defaultAddress(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->addresses(null, 'Your session expired. Try again.');
            return;
        }

        Database::instance()->execute('UPDATE customer_addresses SET is_default = 0 WHERE user_id = :user_id', ['user_id' => Auth::id()]);
        Database::instance()->execute(
            'UPDATE customer_addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id',
            ['id' => (int) $id, 'user_id' => Auth::id()]
        );
        $this->addresses('Default address updated.');
    }

    public function deleteAddress(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->addresses(null, 'Your session expired. Try again.');
            return;
        }

        Database::instance()->execute(
            'DELETE FROM customer_addresses WHERE id = :id AND user_id = :user_id',
            ['id' => (int) $id, 'user_id' => Auth::id()]
        );
        $this->addresses('Address deleted.');
    }

    private function navigation(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => false],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => false],
            ['label' => 'My Portal', 'url' => url('/customer/dashboard'), 'active' => true],
        ];
    }
}
