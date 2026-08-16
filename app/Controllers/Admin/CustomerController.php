<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class CustomerController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $customers = $pdo->query(
            "SELECT u.*,
                COALESCE(stats.orders_count, 0) AS orders_count,
                COALESCE(stats.lifetime_total, 0) AS lifetime_total
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'
             LEFT JOIN (
                SELECT user_id, COUNT(*) AS orders_count, COALESCE(SUM(grand_total), 0) AS lifetime_total
                FROM orders GROUP BY user_id
             ) stats ON stats.user_id = u.id
             ORDER BY u.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $flash = $_SESSION['customers_flash'] ?? null;
        unset($_SESSION['customers_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Customers',
            'pageTitle' => 'Customers',
            'showPageTitle' => false,
            'content' => $this->render('admin/customers/index', [
                'customers' => $customers,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('Customer not found.', 'error');
        }

        $stmt = $pdo->prepare(
            "SELECT u.* FROM users u
             INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'
             WHERE u.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$customer) {
            $this->redirect('Customer not found.', 'error');
        }

        $profileStmt = $pdo->prepare('SELECT * FROM customer_profiles WHERE user_id = ? LIMIT 1');
        $profileStmt->execute([$id]);
        $profile = $profileStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $statsStmt = $pdo->prepare(
            "SELECT COUNT(*) AS orders_count,
                COALESCE(SUM(grand_total), 0) AS lifetime_total,
                COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN grand_total ELSE 0 END), 0) AS delivered_total,
                MAX(created_at) AS last_order_at
             FROM orders WHERE user_id = ?"
        );
        $statsStmt->execute([$id]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC) ?: [
            'orders_count' => 0,
            'lifetime_total' => 0,
            'delivered_total' => 0,
            'last_order_at' => null,
        ];

        $ordersStmt = $pdo->prepare(
            'SELECT id, order_number, order_status, payment_status, grand_total, recipient_name, delivery_date, created_at
             FROM orders WHERE user_id = ? ORDER BY id DESC'
        );
        $ordersStmt->execute([$id]);
        $customerOrders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        $addressesStmt = $pdo->prepare(
            'SELECT * FROM customer_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC'
        );
        $addressesStmt->execute([$id]);
        $addresses = $addressesStmt->fetchAll(PDO::FETCH_ASSOC);

        try {
            $notificationsStmt = $pdo->prepare(
                'SELECT cn.*, o.order_number
                 FROM customer_notifications cn
                 LEFT JOIN orders o ON o.id = cn.order_id
                 WHERE cn.user_id = ?
                 ORDER BY cn.created_at DESC, cn.id DESC
                 LIMIT 50'
            );
            $notificationsStmt->execute([$id]);
            $notifications = $notificationsStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException) {
            $notifications = [];
        }

        $tabs = ['overview', 'orders', 'addresses', 'notifications'];
        $tab = strtolower((string) ($_GET['tab'] ?? 'overview'));
        if (!in_array($tab, $tabs, true)) {
            $tab = 'overview';
        }

        $editMode = (($_GET['mode'] ?? '') === 'edit') && $tab === 'overview';

        $flash = $_SESSION['customers_flash'] ?? null;
        unset($_SESSION['customers_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'View Customer',
            'pageTitle' => 'View Customer',
            'showPageTitle' => false,
            'content' => $this->render('admin/customers/show', compact(
                'customer',
                'profile',
                'stats',
                'customerOrders',
                'addresses',
                'notifications',
                'tab',
                'tabs',
                'editMode'
            ) + ['csrfToken' => $_SESSION['csrf_token'], 'flash' => $flash]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create_customer') {
            $this->createCustomer($pdo);
        }

        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));

        if ($customerId < 1) {
            $this->redirect('Invalid customer.', 'error', null, $redirectTo ?: '/admin/customers');
        }

        $stmt = $pdo->prepare(
            "SELECT u.id FROM users u
             INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'
             WHERE u.id = ? LIMIT 1"
        );
        $stmt->execute([$customerId]);
        if (!$stmt->fetchColumn()) {
            $this->redirect('Customer not found.', 'error', null, $redirectTo ?: '/admin/customers');
        }

        if ($action === 'update_status') {
            $status = (string) ($_POST['status'] ?? '');
            if (!in_array($status, ['active', 'inactive', 'blocked'], true)) {
                $this->redirect('Invalid status.', 'error', $customerId, $redirectTo);
            }
            $pdo->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$status, $customerId]);
            $this->redirect('Customer status updated.', 'success', $customerId, $redirectTo ?: '/admin/customers/view?id=' . $customerId . '&tab=overview');
        }

        if ($action === 'save_notes') {
            $notes = trim((string) ($_POST['notes'] ?? ''));
            $exists = $pdo->prepare('SELECT id FROM customer_profiles WHERE user_id = ? LIMIT 1');
            $exists->execute([$customerId]);
            if ($exists->fetchColumn()) {
                $pdo->prepare('UPDATE customer_profiles SET notes = ? WHERE user_id = ?')->execute([$notes, $customerId]);
            } else {
                $pdo->prepare('INSERT INTO customer_profiles (user_id, notes) VALUES (?, ?)')->execute([$customerId, $notes]);
            }
            $this->redirect('Admin notes saved.', 'success', $customerId, $redirectTo ?: '/admin/customers/view?id=' . $customerId . '&tab=overview&mode=edit');
        }

        $this->redirect('Invalid action.', 'error', $customerId, $redirectTo);
    }

    private function createCustomer(PDO $pdo): never
    {
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $phone2 = trim((string) ($_POST['phone_2'] ?? ''));
        $addressLine1 = trim((string) ($_POST['address_line_1'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $district = trim((string) ($_POST['district'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $status = (string) ($_POST['status'] ?? 'active');

        if ($firstName === '' || $email === '' || $phone === '') {
            $this->redirect('First name, email and phone are required.', 'error');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('Enter a valid email address.', 'error');
        }

        if (!in_array($status, ['active', 'inactive', 'blocked'], true)) {
            $status = 'active';
        }

        if ($password !== '' && strlen($password) < 8) {
            $this->redirect('Password must be at least 8 characters, or leave blank to auto-generate.', 'error');
        }

        $generatedPassword = false;
        if ($password === '') {
            $password = 'Gift' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $generatedPassword = true;
        }

        $dup = $pdo->prepare('SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1');
        $dup->execute([$email, $phone]);
        if ($dup->fetchColumn()) {
            $this->redirect('This email or phone number is already registered.', 'error');
        }

        $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
        $roleStmt->execute(['customer']);
        $roleId = $roleStmt->fetchColumn();
        if (!$roleId) {
            $pdo->prepare('INSERT INTO roles (name, slug) VALUES (?, ?)')->execute(['Customer', 'customer']);
            $roleId = $pdo->lastInsertId();
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare(
            'INSERT INTO users (role_id, first_name, last_name, email, phone, phone_2, address, address_line_1, city, district, avatar, password_hash, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            (int) $roleId,
            $firstName,
            $lastName ?: null,
            $email,
            $phone,
            $phone2 ?: null,
            $addressLine1 ?: null,
            $addressLine1 ?: null,
            $city ?: null,
            $district ?: null,
            'avatar_1',
            $passwordHash,
            $status,
        ]);

        $newId = (int) $pdo->lastInsertId();

        if ($addressLine1 !== '' && $city !== '' && $district !== '') {
            try {
                $pdo->prepare(
                    'INSERT INTO user_addresses (user_id, label, address_line_1, city, district, is_default) VALUES (?, ?, ?, ?, ?, 1)'
                )->execute([$newId, 'Primary address', $addressLine1, $city, $district]);
            } catch (\PDOException) {
                // Optional table — customer record is still created.
            }
        }

        $message = $generatedPassword
            ? "Customer added. Temporary password: {$password}"
            : 'Customer added successfully.';

        $this->redirect($message, 'success', $newId, app_url('/admin/customers/view?id=' . $newId));
    }

    private function redirect(string $message, string $type = 'success', ?int $customerId = null, ?string $redirectTo = null): never
    {
        $_SESSION['customers_flash'] = ['type' => $type, 'message' => $message];
        $target = $redirectTo ?: ($customerId ? app_url('/admin/customers/view?id=' . $customerId) : app_url('/admin/customers'));
        header('Location: ' . app_url($target), true, 303);
        exit;
    }
}
