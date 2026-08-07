<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class AuthController extends Controller
{
    public function login(): void
    {
        $pdo = Database::connection();
        $this->ensureAdminExists($pdo);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // If already logged in, redirect to admin dashboard
        if (isset($_SESSION['admin_user']) && isset($_SESSION['admin_auth_token'])) {
            header('Location: ' . app_url('/admin'), true, 303);
            exit;
        }

        $error = $_SESSION['admin_login_error'] ?? null;
        unset($_SESSION['admin_login_error']);

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        // Render standalone phone request form
        $this->view('admin/auth/login', [
            'error' => $error,
            'csrfToken' => $_SESSION['csrf_token'],
            ...$this->branding($pdo),
        ]);
    }

    public function requestOtp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(419);
            echo 'Invalid or expired request token.';
            return;
        }

        $phone = trim((string) ($_POST['phone'] ?? ''));

        if ($phone === '') {
            $_SESSION['admin_login_error'] = 'Please enter your phone number.';
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        $pdo = Database::connection();
        $this->ensureAdminExists($pdo); // Ensure tables & default numbers are populated

        // Find user by phone
        $stmt = $pdo->prepare('SELECT u.*, r.slug as role_slug FROM users u JOIN roles r ON u.role_id = r.id WHERE u.phone = ? LIMIT 1');
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['admin_login_error'] = 'Phone number not registered or access denied.';
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        // Ensure user has administrative rights
        if (!in_array($user['role_slug'], ['admin', 'staff'], true)) {
            $_SESSION['admin_login_error'] = 'Access denied. You do not have admin permissions.';
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        if ($user['status'] !== 'active') {
            $_SESSION['admin_login_error'] = 'Your account is currently disabled.';
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        // Save default OTP 992011 to DB for verification
        $otp = '992011';
        $expires = date('Y-m-d H:i:s', time() + 600); // 10 minutes expiry

        $update = $pdo->prepare('UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?');
        $update->execute([$otp, $expires, $user['id']]);

        // Keep phone in session to verify in the next screen
        $_SESSION['otp_phone'] = $phone;

        header('Location: ' . app_url('/admin/login/verify'), true, 303);
        exit;
    }

    public function verifyForm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Ensure we have requested OTP first
        if (!isset($_SESSION['otp_phone'])) {
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        $error = $_SESSION['admin_login_error'] ?? null;
        unset($_SESSION['admin_login_error']);

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $pdo = Database::connection();
        $this->view('admin/auth/verify', [
            'phone' => $_SESSION['otp_phone'],
            'error' => $error,
            'csrfToken' => $_SESSION['csrf_token'],
            ...$this->branding($pdo),
        ]);
    }

    public function verifyOtp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(419);
            echo 'Invalid or expired request token.';
            return;
        }

        $phone = $_SESSION['otp_phone'] ?? '';
        if ($phone === '') {
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        // Collect 6-digit OTP from input arrays
        $digits = $_POST['otp_digit'] ?? [];
        $enteredOtp = implode('', array_map('trim', (array) $digits));

        if (strlen($enteredOtp) !== 6) {
            $_SESSION['admin_login_error'] = 'Please enter a complete 6-digit OTP code.';
            header('Location: ' . app_url('/admin/login/verify'), true, 303);
            exit;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT u.*, r.slug as role_slug FROM users u JOIN roles r ON u.role_id = r.id WHERE u.phone = ? LIMIT 1');
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['admin_login_error'] = 'Authentication failed. Please request a new OTP.';
            unset($_SESSION['otp_phone']);
            header('Location: ' . app_url('/admin/login'), true, 303);
            exit;
        }

        // Check DB OTP code match and expiration
        $dbOtp = (string) ($user['otp_code'] ?? '');
        $dbExpires = $user['otp_expires_at'] ? strtotime((string) $user['otp_expires_at']) : 0;

        if ($dbOtp === '' || $enteredOtp !== $dbOtp || time() > $dbExpires) {
            $_SESSION['admin_login_error'] = 'Invalid or expired OTP code.';
            header('Location: ' . app_url('/admin/login/verify'), true, 303);
            exit;
        }

        // Clear OTP columns on successful login
        $clear = $pdo->prepare('UPDATE users SET otp_code = NULL, otp_expires_at = NULL WHERE id = ?');
        $clear->execute([$user['id']]);

        // Login session variables
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role_slug']
        ];
        $_SESSION['admin_auth_token'] = bin2hex(random_bytes(32));

        unset($_SESSION['otp_phone']);

        header('Location: ' . app_url('/admin'), true, 303);
        exit;
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION['admin_user']);
        unset($_SESSION['admin_auth_token']);
        unset($_SESSION['otp_phone']);
        $_SESSION['admin_login_error'] = 'You have logged out successfully.';
        header('Location: ' . app_url('/admin/login'), true, 303);
        exit;
    }

    private function ensureAdminExists(PDO $pdo): void
    {
        // 1. Ensure 'roles' table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
        if (!$stmt->fetch()) {
            $pdo->exec("CREATE TABLE `roles` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(80) NOT NULL,
              `slug` varchar(80) NOT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // 2. Ensure 'users' table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if (!$stmt->fetch()) {
            $pdo->exec("CREATE TABLE `users` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `role_id` bigint(20) unsigned NOT NULL,
              `first_name` varchar(100) NOT NULL,
              `last_name` varchar(100) DEFAULT NULL,
              `email` varchar(190) NOT NULL,
              `phone` varchar(40) DEFAULT NULL,
              `password_hash` varchar(255) NOT NULL,
              `status` enum('active','inactive','blocked') NOT NULL DEFAULT 'active',
              `email_verified_at` timestamp NULL DEFAULT NULL,
              `phone_verified_at` timestamp NULL DEFAULT NULL,
              `last_login_at` timestamp NULL DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `email` (`email`),
              UNIQUE KEY `phone` (`phone`),
              KEY `fk_users_role` (`role_id`),
              CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // 3. Ensure 'otp_code' and 'otp_expires_at' columns exist in users table
        $columns = $pdo->query("DESCRIBE `users`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('otp_code', $columns, true)) {
            $pdo->exec("ALTER TABLE `users` ADD `otp_code` VARCHAR(10) NULL DEFAULT NULL");
        }
        if (!in_array('otp_expires_at', $columns, true)) {
            $pdo->exec("ALTER TABLE `users` ADD `otp_expires_at` DATETIME NULL DEFAULT NULL");
        }

        // 4. Ensure 'admin' role exists
        $stmt = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
        $stmt->execute(['admin']);
        $roleId = $stmt->fetchColumn();

        if (!$roleId) {
            $pdo->exec("INSERT INTO roles (name, slug) VALUES ('Admin', 'admin')");
            $roleId = $pdo->lastInsertId();
        }

        // 5. Ensure the admin user exists with phone number 0768306759
        $stmt = $pdo->prepare('SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = ? LIMIT 1');
        $stmt->execute(['admin']);
        $adminUserId = $stmt->fetchColumn();

        if (!$adminUserId) {
            // Seed default admin: admin@giftvibe.lk / admin123 with phone 0768306759
            $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
            $insert = $pdo->prepare('INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insert->execute([$roleId, 'Admin', 'User', 'admin@giftvibe.lk', '0768306759', $hashedPassword, 'active']);
        } else {
            // Ensure the phone is set to 0768306759 for this admin
            $update = $pdo->prepare('UPDATE users SET phone = ? WHERE id = ?');
            $update->execute(['0768306759', $adminUserId]);
        }
    }

    /** @return array{logo: string, siteName: string} */
    private function branding(PDO $pdo): array
    {
        $general = $pdo->query('SELECT site_logo, site_name FROM general_settings WHERE id = 1')
            ->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'logo' => (string) ($general['site_logo'] ?? '/assets/images/logo.svg'),
            'siteName' => (string) ($general['site_name'] ?? 'GiftVibe'),
        ];
    }
}
