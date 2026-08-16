<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FinanceSummaryService;
use PDO;

final class ExpenseController extends Controller
{
    /** @var array<string, string> */
    private const CATEGORIES = [
        'packing' => 'Packing materials',
        'delivery' => 'Delivery / courier',
        'marketing' => 'Marketing',
        'rent' => 'Rent / utilities',
        'salary' => 'Staff / salary',
        'other' => 'Other',
    ];

    public function index(): void
    {
        $pdo = Database::connection();
        $this->ensureExpenseSchema($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $expenses = [];
        try {
            $expenses = $pdo->query(
                'SELECT e.*, TRIM(CONCAT(COALESCE(u.first_name, \'\'), \' \', COALESCE(u.last_name, \'\'))) AS admin_name
                 FROM expenses e
                 LEFT JOIN users u ON u.id = e.admin_id
                 ORDER BY e.expense_date DESC, e.id DESC'
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException) {
            $expenses = [];
        }

        $totalAmount = array_sum(array_map(static fn(array $e): float => (float) $e['amount'], $expenses));
        $approvedTotal = array_sum(array_map(
            static fn(array $e): float => in_array($e['status'], ['approved', 'paid'], true) ? (float) $e['amount'] : 0.0,
            $expenses
        ));
        $financeSummary = FinanceSummaryService::summary($pdo);

        $flash = $_SESSION['expenses_flash'] ?? null;
        unset($_SESSION['expenses_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Business Expenses',
            'pageTitle' => 'Business Expenses',
            'showPageTitle' => false,
            'content' => $this->render('admin/expenses/index', [
                'expenses' => $expenses,
                'categories' => self::CATEGORIES,
                'totalAmount' => $totalAmount,
                'approvedTotal' => $approvedTotal,
                'cashOnHand' => (float) $financeSummary['cashOnHand'],
                'usableCash' => (float) ($financeSummary['usableCash'] ?? 0),
                'availableProfit' => (float) ($financeSummary['availableProfit'] ?? 0),
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'save') {
            $this->save($pdo);
        }
        if ($action === 'delete') {
            $this->delete($pdo);
        }

        $this->redirect('Invalid action.', 'error');
    }

    private function save(PDO $pdo): never
    {
        $id = (int) ($_POST['id'] ?? 0);
        $category = (string) ($_POST['category'] ?? 'other');
        if (!isset(self::CATEGORIES[$category])) {
            $category = 'other';
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $paidSource = (string) ($_POST['paid_source'] ?? 'company_cash');
        if (!in_array($paidSource, ['company_cash', 'profit'], true)) {
            $paidSource = 'company_cash';
        }
        $description = '';
        $amount = max(0.01, (float) ($_POST['amount'] ?? 0));
        $expenseDate = date('Y-m-d');
        $adminId = (int) ($_SESSION['admin_user']['id'] ?? 0) ?: null;

        if ($title === '') {
            $this->redirect('Please enter an expense title.', 'error');
        }

        $summary = FinanceSummaryService::summary($pdo);
        $oldAmount = 0.0;
        $oldSource = '';
        if ($id > 0) {
            $oldStmt = $pdo->prepare('SELECT amount, paid_source, expense_date FROM expenses WHERE id = ? LIMIT 1');
            $oldStmt->execute([$id]);
            $old = $oldStmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $oldAmount = (float) ($old['amount'] ?? 0);
            $oldSource = (string) ($old['paid_source'] ?? '');
            $expenseDate = (string) ($old['expense_date'] ?? $expenseDate);
        }
        $available = $paidSource === 'profit'
            ? (float) ($summary['availableProfit'] ?? 0) + ($oldSource === 'profit' ? $oldAmount : 0)
            : (float) ($summary['usableCash'] ?? 0) + ($oldSource !== 'profit' ? $oldAmount : 0);
        if ($amount > $available + 0.00001) {
            $balanceName = $paidSource === 'profit' ? 'available profit' : 'usable cash';
            $this->redirect('Insufficient ' . $balanceName . '. Available: LKR ' . number_format(max(0, $available), 2), 'error');
        }

        if ($id > 0) {
            $pdo->prepare(
                'UPDATE expenses SET paid_source = ?, category = ?, title = ?, description = ?, amount = ?, expense_date = ? WHERE id = ?'
            )->execute([$paidSource, $category, $title, $description ?: null, $amount, $expenseDate, $id]);
            $this->redirect('Expense updated.', 'success');
        }

        $expenseNumber = 'EXP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $pdo->prepare(
            'INSERT INTO expenses (admin_id, paid_source, expense_number, category, title, description, amount, expense_date, status, inventory_item_id, stock_quantity)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $adminId,
            $paidSource,
            $expenseNumber,
            $category,
            $title,
            $description ?: null,
            $amount,
            $expenseDate,
            'approved',
            null,
            null,
        ]);

        $expenseId = (int) $pdo->lastInsertId();

        $this->redirect('Expense added successfully.', 'success');
    }

    private function delete(PDO $pdo): never
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) {
            $this->redirect('Invalid expense.', 'error');
        }

        $stmt = $pdo->prepare('SELECT receipt_path FROM expenses WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $path = $stmt->fetchColumn();
        if ($path && is_string($path) && file_exists(BASE_PATH . '/public' . $path)) {
            @unlink(BASE_PATH . '/public' . $path);
        }

        $pdo->prepare('DELETE FROM expenses WHERE id = ?')->execute([$id]);
        $this->redirect('Expense deleted.', 'success');
    }

    private function ensureExpenseSchema(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS expenses (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                admin_id BIGINT UNSIGNED NULL,
                order_id BIGINT UNSIGNED NULL,
                paid_source VARCHAR(30) NOT NULL DEFAULT 'company_cash',
                paid_by_admin_id BIGINT UNSIGNED NULL,
                reimbursement_status VARCHAR(30) NULL,
                inventory_item_id BIGINT UNSIGNED NULL,
                stock_quantity INT UNSIGNED NULL,
                expense_number VARCHAR(40) NOT NULL,
                category VARCHAR(120) NOT NULL,
                title VARCHAR(190) NOT NULL,
                description TEXT NULL,
                amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                expense_date DATE NOT NULL,
                receipt_path VARCHAR(255) NULL,
                receipt_original_name VARCHAR(190) NULL,
                status ENUM('pending','approved','rejected','paid') NOT NULL DEFAULT 'approved',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY expense_number (expense_number),
                KEY idx_expenses_date_status (expense_date, status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $columns = $pdo->query('DESCRIBE expenses')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('paid_source', $columns, true)) {
            $pdo->exec("ALTER TABLE expenses ADD paid_source VARCHAR(30) NOT NULL DEFAULT 'company_cash' AFTER order_id");
        }
        if (!in_array('inventory_item_id', $columns, true)) {
            $pdo->exec('ALTER TABLE expenses ADD inventory_item_id BIGINT UNSIGNED NULL AFTER order_id');
        }
        if (!in_array('stock_quantity', $columns, true)) {
            $pdo->exec('ALTER TABLE expenses ADD stock_quantity INT UNSIGNED NULL AFTER inventory_item_id');
        }
    }

    private function redirect(string $message, string $type = 'success'): never
    {
        $_SESSION['expenses_flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . app_url('/admin/expenses'), true, 303);
        exit;
    }
}
