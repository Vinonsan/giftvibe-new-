<?php
namespace Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Services\AdminOtpService;
use Throwable;

class AuthController
{
    public function showLogin(): void
    {
        $this->showCustomerLogin();
    }

    public function showCustomerLogin(?string $error = null): void
    {
        View::renderPage('auth/login', [
            'title' => 'Customer Sign In',
            'action' => url('/login'),
            'portal' => 'customer',
            'error' => $error,
            'navigation' => $this->publicNavigation('customer'),
        ], 'public/layouts/main');
    }

    public function showCustomerSignup(?string $error = null): void
    {
        View::renderPage('auth/signup', [
            'title' => 'Create GiftVibe.lk Account',
            'error' => $error,
            'navigation' => $this->publicNavigation('customer'),
            'meta' => [
                'description' => 'Create a GiftVibe.lk customer account to save wishlist products and delivery addresses.',
                'url' => url('/signup'),
            ],
        ], 'public/layouts/main');
    }

    public function showAdminLogin(?string $error = null): void
    {
        $otpService = new AdminOtpService();

        View::renderPage('auth/login', [
            'title' => 'Admin Sign In',
            'action' => url('/admin/login/request-otp'),
            'portal' => 'admin',
            'pendingPhone' => $otpService->pendingPhone(),
            'error' => $error,
        ], 'admin/layouts/auth');
    }

    public function customerLogin(): void
    {
        $this->attemptLogin(['customer'], '/customer/dashboard', fn ($error) => $this->showCustomerLogin($error));
    }

    public function customerSignup(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->showCustomerSignup('Your session expired. Refresh the page and try again.');
            return;
        }

        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $phone = preg_replace('/\D+/', '', (string) ($_POST['phone'] ?? '')) ?: null;
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['password_confirmation'] ?? '');

        if ($firstName === '' || $email === '' || $password === '') {
            $this->showCustomerSignup('First name, email, and password are required.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showCustomerSignup('Enter a valid email address.');
            return;
        }

        if (strlen($password) < 8) {
            $this->showCustomerSignup('Password must be at least 8 characters.');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->showCustomerSignup('Password confirmation does not match.');
            return;
        }

        $db = Database::instance();
        $existing = $db->fetch('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
        if ($existing) {
            $this->showCustomerSignup('An account with this email already exists.');
            return;
        }

        $role = $db->fetch('SELECT id FROM roles WHERE slug = "customer" LIMIT 1');
        if (!$role) {
            $this->showCustomerSignup('Customer role is missing. Import the schema seed data.');
            return;
        }

        $db->execute(
            'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status)
             VALUES (:role_id, :first_name, :last_name, :email, :phone, :password_hash, "active")',
            [
                'role_id' => $role['id'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]
        );

        $user = $db->fetch(
            'SELECT users.*, roles.slug AS role_slug FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.email = :email LIMIT 1',
            ['email' => $email]
        );

        if ($user) {
            Auth::login($user);
        }

        redirect('/customer/dashboard');
    }

    public function adminLogin(): void
    {
        $this->requestAdminOtp();
    }

    public function requestAdminOtp(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->showAdminLogin('Your session expired. Refresh the page and try again.');
            return;
        }

        $otpService = new AdminOtpService();
        $phone = $otpService->normalizePhone((string) ($_POST['phone'] ?? ''));

        if (!$otpService->isAuthorizedPhone($phone)) {
            $this->showAdminLogin('This phone number is not authorized for admin access.');
            return;
        }

        $otpService->issue($phone);
        $this->showAdminLogin('OTP sent. Use 000000 in development.');
    }

    public function verifyAdminOtp(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->showAdminLogin('Your session expired. Refresh the page and try again.');
            return;
        }

        $otpService = new AdminOtpService();
        $pendingPhone = $otpService->pendingPhone();

        if (!$pendingPhone) {
            $this->showAdminLogin('Request a fresh OTP to continue.');
            return;
        }

        if (!$otpService->verify((string) ($_POST['otp'] ?? ''))) {
            $this->showAdminLogin('Invalid or expired OTP.');
            return;
        }

        $phone = $otpService->consume();
        $user = null;

        try {
            $user = Database::instance()->fetch(
                'SELECT users.*, roles.slug AS role_slug
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.email = :email AND users.status = "active"
                 LIMIT 1',
                ['email' => 'admin@giftvibe.lk']
            );
        } catch (Throwable) {
            $user = null;
        }

        Auth::loginAdminByPhone($phone ?? $pendingPhone, $user);

        if ($user) {
            Database::instance()->execute('UPDATE users SET last_login_at = NOW() WHERE id = :id', ['id' => $user['id']]);
        }

        redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }

    public function adminLogout(): void
    {
        Auth::logout();
        redirect('/admin/login');
    }

    private function attemptLogin(array $allowedRoles, string $redirectTo, callable $failed): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $failed('Your session expired. Refresh the page and try again.');
            return;
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $failed('Email and password are required.');
            return;
        }

        try {
            $user = Database::instance()->fetch(
                'SELECT users.*, roles.slug AS role_slug
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.email = :email AND users.status = "active"
                 LIMIT 1',
                ['email' => $email]
            );
        } catch (Throwable) {
            $failed('The database is not ready yet. Import database/schema.sql and try again.');
            return;
        }

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $failed('Invalid email or password.');
            return;
        }

        if (!in_array($user['role_slug'], $allowedRoles, true)) {
            $failed('You do not have permission to access this portal.');
            return;
        }

        Auth::login($user);
        Database::instance()->execute('UPDATE users SET last_login_at = NOW() WHERE id = :id', ['id' => $user['id']]);

        redirect($redirectTo);
    }

    private function publicNavigation(string $active = 'home'): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => $active === 'home'],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => $active === 'shop'],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => $active === 'categories'],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => $active === 'custom-gifts'],
            ['label' => 'About', 'url' => url('/about'), 'active' => $active === 'about'],
            ['label' => 'Contact', 'url' => url('/contact'), 'active' => $active === 'contact'],
            ['label' => Auth::isCustomer() ? 'My Portal' : 'Login', 'url' => Auth::isCustomer() ? url('/customer/dashboard') : url('/login'), 'active' => $active === 'customer'],
        ];
    }
}
