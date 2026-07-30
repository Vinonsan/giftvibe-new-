<?php

namespace Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use DateTimeImmutable;
use Helpers\AdminNavigation;

class ExpenseController
{
    private array $statuses = ['pending', 'approved', 'rejected', 'paid'];
    private array $categories = ['Packaging', 'Delivery', 'Marketing', 'Supplies', 'Utilities', 'Payroll', 'Other'];

    public function index(?string $message = null, ?string $error = null): void
    {
        [$period, $month, $year, $startDate, $endDate] = $this->resolveFilters();
        $status = in_array($_GET['status'] ?? '', $this->statuses, true) ? $_GET['status'] : '';
        $where = ['expenses.expense_date >= :start_date', 'expenses.expense_date < :end_date'];
        $params = ['start_date' => substr($startDate, 0, 10), 'end_date' => substr($endDate, 0, 10)];

        if ($status !== '') {
            $where[] = 'expenses.status = :status';
            $params['status'] = $status;
        }

        $expenses = Database::instance()->fetchAll(
            'SELECT expenses.*, users.first_name, users.last_name, users.email
             FROM expenses
             LEFT JOIN users ON users.id = expenses.admin_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY expenses.expense_date DESC, expenses.id DESC
             LIMIT 200',
            $params
        );

        View::renderPage('admin/pages/expenses/index', [
            'title' => 'Expenses',
            'adminNavigation' => AdminNavigation::make('expenses'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Expenses']],
            'expenses' => $expenses,
            'statuses' => $this->statuses,
            'categories' => $this->categories,
            'filters' => ['period' => $period, 'month' => $month, 'year' => $year, 'status' => $status],
            'total' => array_sum(array_map(static fn (array $expense): float => in_array($expense['status'], ['approved', 'paid'], true) ? (float) $expense['amount'] : 0.0, $expenses)),
            'message' => $message,
            'error' => $error,
        ], 'admin/layouts/admin');
    }

    public function store(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index(null, 'Your session expired. Try again.');
            return;
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $amount = (float) ($_POST['amount'] ?? 0);
        $expenseDate = $this->dateOrToday($_POST['expense_date'] ?? null);
        $category = trim((string) ($_POST['category'] ?? 'Other')) ?: 'Other';
        $status = in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'approved';

        if ($title === '' || $amount <= 0) {
            $this->index(null, 'Expense title and amount are required.');
            return;
        }

        $receipt = $this->storeReceipt();
        Database::instance()->execute(
            'INSERT INTO expenses
             (admin_id, expense_number, category, title, description, amount, expense_date, receipt_path, receipt_original_name, status)
             VALUES (:admin_id, :expense_number, :category, :title, :description, :amount, :expense_date, :receipt_path, :receipt_original_name, :status)',
            [
                'admin_id' => Auth::id(),
                'expense_number' => 'EXP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                'category' => $category,
                'title' => $title,
                'description' => trim((string) ($_POST['description'] ?? '')),
                'amount' => $amount,
                'expense_date' => $expenseDate,
                'receipt_path' => $receipt['path'],
                'receipt_original_name' => $receipt['name'],
                'status' => $status,
            ]
        );

        redirect('/admin/expenses');
    }

    public function update(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/expenses');
        }

        $status = in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'approved';
        Database::instance()->execute(
            'UPDATE expenses SET status = :status WHERE id = :id',
            ['status' => $status, 'id' => (int) $id]
        );

        redirect('/admin/expenses');
    }

    public function receipt(string $id): void
    {
        $expense = Database::instance()->fetch('SELECT receipt_path, receipt_original_name FROM expenses WHERE id = :id LIMIT 1', ['id' => (int) $id]);
        if (!$expense || empty($expense['receipt_path'])) {
            http_response_code(404);
            echo 'Receipt not found.';
            return;
        }

        $path = BASE_PATH . '/' . ltrim((string) $expense['receipt_path'], '/');
        if (!is_file($path) || !str_starts_with(realpath($path) ?: '', realpath(UPLOAD_PATH . '/expenses') ?: UPLOAD_PATH . '/expenses')) {
            http_response_code(404);
            echo 'Receipt not found.';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename((string) ($expense['receipt_original_name'] ?: $path)) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }


    public function reports(): void
    {
        [$period, $month, $year, $startDate, $endDate] = $this->resolveFilters();
        $summary = $this->profitSummary($startDate, $endDate);
        $products = Database::instance()->fetchAll(
            'SELECT
                order_items.product_id,
                order_items.product_name,
                SUM(order_items.quantity) AS quantity_sold,
                SUM(order_items.total_price) AS revenue,
                SUM(order_items.quantity * order_items.cost_price) AS product_cost,
                SUM(order_items.total_price - (order_items.quantity * order_items.cost_price)) AS product_profit
             FROM order_items
             INNER JOIN orders ON orders.id = order_items.order_id
             WHERE orders.order_status NOT IN ("cancelled", "refunded")
               AND orders.created_at >= :start_date AND orders.created_at < :end_date
             GROUP BY order_items.product_id, order_items.product_name
             ORDER BY product_profit DESC
             LIMIT 100',
            ['start_date' => $startDate, 'end_date' => $endDate]
        );
        $expenseRows = Database::instance()->fetchAll(
            'SELECT category, SUM(amount) AS total
             FROM expenses
             WHERE status IN ("approved", "paid") AND expense_date >= :start_date AND expense_date < :end_date
             GROUP BY category
             ORDER BY total DESC',
            ['start_date' => substr($startDate, 0, 10), 'end_date' => substr($endDate, 0, 10)]
        );

        View::renderPage('admin/pages/reports/index', [
            'title' => 'Reports',
            'adminNavigation' => AdminNavigation::make('reports'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Reports']],
            'filters' => ['period' => $period, 'month' => $month, 'year' => $year, 'startDate' => $startDate, 'endDate' => $endDate],
            'summary' => $summary,
            'products' => $products,
            'expenseRows' => $expenseRows,
        ], 'admin/layouts/admin');
    }

    public function profitSummary(string $startDate, string $endDate): array
    {
        $orders = Database::instance()->fetch(
            'SELECT COALESCE(SUM(grand_total), 0) AS sales, COUNT(*) AS orders
             FROM orders
             WHERE order_status NOT IN ("cancelled", "refunded")
               AND created_at >= :start_date AND created_at < :end_date',
            ['start_date' => $startDate, 'end_date' => $endDate]
        ) ?? [];
        $productCost = Database::instance()->fetch(
            'SELECT COALESCE(SUM(order_items.quantity * order_items.cost_price), 0) AS total
             FROM order_items
             INNER JOIN orders ON orders.id = order_items.order_id
             WHERE orders.order_status NOT IN ("cancelled", "refunded")
               AND orders.created_at >= :start_date AND orders.created_at < :end_date',
            ['start_date' => $startDate, 'end_date' => $endDate]
        ) ?? [];
        $businessExpenses = Database::instance()->fetch(
            'SELECT COALESCE(SUM(amount), 0) AS total
             FROM expenses
             WHERE status IN ("approved", "paid") AND expense_date >= :start_date AND expense_date < :end_date',
            ['start_date' => substr($startDate, 0, 10), 'end_date' => substr($endDate, 0, 10)]
        ) ?? [];

        $sales = (float) ($orders['sales'] ?? 0);
        $cost = (float) ($productCost['total'] ?? 0);
        $grossProfit = $sales - $cost;
        $totalExpenses = (float) ($businessExpenses['total'] ?? 0);

        return [
            'sales' => $sales,
            'orders' => (int) ($orders['orders'] ?? 0),
            'product_cost' => $cost,
            'gross_profit' => $grossProfit,
            'total_expenses' => $totalExpenses,
            'net_profit' => $grossProfit - $totalExpenses,
        ];
    }

    private function resolveFilters(): array
    {
        $period = ($_GET['period'] ?? 'monthly') === 'yearly' ? 'yearly' : 'monthly';
        $today = new DateTimeImmutable('today');
        $year = max(2020, min(2100, (int) ($_GET['year'] ?? $today->format('Y'))));
        $month = max(1, min(12, (int) ($_GET['month'] ?? $today->format('n'))));

        if ($period === 'yearly') {
            $start = new DateTimeImmutable(sprintf('%04d-01-01 00:00:00', $year));
            $end = $start->modify('+1 year');
        } else {
            $start = new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month));
            $end = $start->modify('+1 month');
        }

        return [$period, $month, $year, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
    }

    private function storeReceipt(): array
    {
        if (empty($_FILES['receipt']['tmp_name']) || !is_uploaded_file($_FILES['receipt']['tmp_name'])) {
            return ['path' => null, 'name' => null];
        }

        $tmp = $_FILES['receipt']['tmp_name'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        $mime = mime_content_type($tmp);
        if (!isset($allowed[$mime]) || (int) ($_FILES['receipt']['size'] ?? 0) > 5 * 1024 * 1024) {
            return ['path' => null, 'name' => null];
        }

        $targetDir = UPLOAD_PATH . '/expenses';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $relative = 'storage/uploads/expenses/receipt-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];
        if (!move_uploaded_file($tmp, BASE_PATH . '/' . $relative)) {
            return ['path' => null, 'name' => null];
        }

        return ['path' => $relative, 'name' => $_FILES['receipt']['name'] ?? null];
    }

    private function dateOrToday(mixed $date): string
    {
        $value = trim((string) $date);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : date('Y-m-d');
    }
}
