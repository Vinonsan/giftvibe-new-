<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Controllers\Admin\ExpenseController;
use Controllers\PublicSite\CatalogController;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$db = Database::instance()->connection();
$db->exec(
    "CREATE TABLE IF NOT EXISTS expenses (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      admin_id BIGINT UNSIGNED NULL,
      expense_number VARCHAR(40) NOT NULL UNIQUE,
      category VARCHAR(120) NOT NULL,
      title VARCHAR(190) NOT NULL,
      description TEXT NULL,
      amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
      expense_date DATE NOT NULL,
      receipt_path VARCHAR(255) NULL,
      receipt_original_name VARCHAR(190) NULL,
      status ENUM('pending','approved','rejected','paid') NOT NULL DEFAULT 'approved',
      created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_expenses_admin (admin_id),
      INDEX idx_expenses_date_status (expense_date, status),
      CONSTRAINT fk_expenses_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$db->beginTransaction();
try {
    $suffix = bin2hex(random_bytes(4));
    $role = Database::instance()->fetch('SELECT id FROM roles WHERE slug = "admin" LIMIT 1') ?: Database::instance()->fetch('SELECT id FROM roles LIMIT 1');
    $db->prepare(
        'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status)
         VALUES (?, "Finance", "Admin", ?, ?, ?, "active")'
    )->execute([(int) $role['id'], 'finance-' . $suffix . '@example.test', '0788' . random_int(100000, 999999), password_hash('password123', PASSWORD_DEFAULT)]);
    $adminId = (int) $db->lastInsertId();
    Auth::login(['id' => $adminId, 'first_name' => 'Finance', 'last_name' => 'Admin', 'email' => 'finance-' . $suffix . '@example.test', 'role_slug' => 'admin']);
    $_SESSION['admin_session_expires_at'] = time() + 3600;

    $db->prepare(
        'INSERT INTO products (sku, name, slug, short_description, base_price, cost_price, stock_quantity, status)
         VALUES (?, ?, ?, "Finance product", 5000, 2500, 5, "active")'
    )->execute(['FIN-' . $suffix, 'Finance Product ' . $suffix, 'finance-product-' . $suffix]);
    $productId = (int) $db->lastInsertId();
    $db->prepare('INSERT INTO product_images (product_id, image_path, alt_text, is_primary) VALUES (?, "public/assets/images/hero_gift_box.jpg", "Delivered finance gift", 1)')->execute([$productId]);

    $db->prepare(
        'INSERT INTO orders
         (order_number, customer_name, customer_email, customer_phone, recipient_name, recipient_phone, delivery_address_line_1, delivery_city, delivery_district, subtotal, grand_total, order_status, created_at)
         VALUES (?, "Finance Customer", "finance-customer@example.test", "0771234567", "Recipient", "0779876543", "No 1", "Colombo", "Colombo", 10000, 10000, "delivered", "2026-07-15 10:00:00")'
    )->execute(['ORD-FIN-' . strtoupper($suffix)]);
    $orderId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, sku, quantity, unit_price, cost_price, total_price)
         VALUES (?, ?, ?, ?, 2, 5000, 2500, 10000)'
    )->execute([$orderId, $productId, 'Finance Product ' . $suffix, 'FIN-' . $suffix]);
    $db->prepare('INSERT INTO deliveries (order_id, delivery_status, delivered_date, delivered_at) VALUES (?, "delivered", "2026-07-16", "2026-07-16 12:00:00")')->execute([$orderId]);

    $db->prepare(
        'INSERT INTO expenses (admin_id, expense_number, category, title, amount, expense_date, receipt_path, receipt_original_name, status)
         VALUES (?, ?, "Marketing", "July campaign", 1200, "2026-07-10", "storage/uploads/expenses/sample.jpg", "sample.jpg", "approved")'
    )->execute([$adminId, 'EXP-FIN-' . strtoupper($suffix)]);
    $expenseId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO expenses (admin_id, expense_number, category, title, amount, expense_date, status)
         VALUES (?, ?, "Other", "Rejected test", 9999, "2026-07-10", "rejected")'
    )->execute([$adminId, 'EXP-REJ-' . strtoupper($suffix)]);

    $expense = Database::instance()->fetch(
        'SELECT expenses.*, users.email FROM expenses INNER JOIN users ON users.id = expenses.admin_id WHERE expenses.id = :id',
        ['id' => $expenseId]
    );
    $assert($expense && (int) $expense['admin_id'] === $adminId && $expense['receipt_path'] !== '', 'expense stores responsible admin and receipt metadata');

    $summary = (new ExpenseController())->profitSummary('2026-07-01 00:00:00', '2026-08-01 00:00:00');
    $assert((float) $summary['gross_profit'] === 5000.0, 'gross profit is sales minus product cost');
    $assert((float) $summary['total_expenses'] === 1200.0, 'total expenses include approved and paid expenses only');
    $assert((float) $summary['net_profit'] === 3800.0, 'net profit is gross profit minus expenses');

    $_SERVER['REQUEST_URI'] = '/';
    ob_start();
    (new CatalogController())->home();
    $homeHtml = ob_get_clean();
    $assert(str_contains($homeHtml, 'Delivered orders') && str_contains($homeHtml, 'Finance Product'), 'delivered-order trust section renders counts and images');
    $assert(str_contains($homeHtml, '<meta name="description"') && str_contains($homeHtml, '<link rel="canonical"'), 'home page renders SEO description and canonical tags');

    $db->rollBack();
    Auth::logout();
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $messages[] = 'FAIL exception: ' . $exception->getMessage();
    $failures[] = 'exception';
}

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
