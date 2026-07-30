<?php

namespace App\Core;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['auth_user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['auth_user']['id']) ? (int) $_SESSION['auth_user']['id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        return $_SESSION['auth_user']['role'] ?? null;
    }

    public static function isCustomer(): bool
    {
        return self::role() === 'customer';
    }

    public static function isStaff(): bool
    {
        if (!in_array(self::role(), ['super_admin', 'admin', 'manager', 'staff'], true)) {
            return false;
        }

        $expiresAt = $_SESSION['admin_session_expires_at'] ?? null;
        return is_int($expiresAt) && $expiresAt > time();
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['auth_user'] = [
            'id' => (int) $user['id'],
            'name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
            'email' => $user['email'] ?? null,
            'phone' => $user['phone'] ?? null,
            'role' => $user['role_slug'] ?? $user['role'] ?? null,
        ];
    }

    public static function loginAdminByPhone(string $phone, ?array $user = null): void
    {
        $config = require CONFIG_PATH . '/admin.php';

        session_regenerate_id(true);
        $_SESSION['auth_user'] = [
            'id' => isset($user['id']) ? (int) $user['id'] : 1,
            'name' => trim(($user['first_name'] ?? 'GiftVibe') . ' ' . ($user['last_name'] ?? 'Admin')),
            'email' => $user['email'] ?? 'admin@giftvibe.lk',
            'phone' => $phone,
            'role' => $user['role_slug'] ?? 'super_admin',
        ];
        $_SESSION['admin_authenticated_at'] = time();
        $_SESSION['admin_session_expires_at'] = time() + ((int) $config['session_ttl_minutes'] * 60);
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_user']);
        unset($_SESSION['admin_authenticated_at'], $_SESSION['admin_session_expires_at'], $_SESSION['admin_otp_challenge']);
        session_regenerate_id(true);
    }
}
