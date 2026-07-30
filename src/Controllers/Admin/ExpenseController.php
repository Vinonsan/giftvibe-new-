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
    private array $reimbursementStatuses = ['pending', 'reimbursed'];

    public function index(?string $message = null, ?string $error = null): void
    {
        $this->ensureExpenseOrderColumn();
        [$period, $month, $year, $startDate, $endDate] = $this->resolveFilters();
        $status = in_array($_GET['status'] ?? '', $this->statuses, true) ? $_GET['status'] : '';
        $where = ['expenses.expense_date >= :start_date', 'expenses.expense_date < :end_date'];
        $params = ['start_date' => substr($startDate, 0, 10), 'end_date' => substr($endDate, 0, 10)];

        if ($status !== '') {
            $where[] = 'expenses.status = :status';
            $params['status'] = $status;
        }

        $expenses = Database::instance()->fetchAll(
            'SELECT expenses.*, users.first_name, users.last_name, users.email,
                    paid_users.first_name AS paid_first_name, paid_users.last_name AS paid_last_name,
                    orders.order_number, orders.customer_name AS order_customer_name, orders.grand_total AS order_grand_total
             FROM expenses
             LEFT JOIN users ON users.id = expenses.admin_id
             LEFT JOIN users AS paid_users ON paid_users.id = expenses.paid_by_admin_id
             LEFT JOIN orders ON orders.id = expenses.order_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY expenses.expense_date DESC, expenses.id DESC
             LIMIT 200',
            $params
        );
        $orders = Database::instance()->fetchAll(
            'SELECT id, order_number, customer_name, grand_total, order_status, created_at
             FROM orders
             ORDER BY created_at DESC, id DESC
             LIMIT 150'
        );
        $admins = Database::instance()->fetchAll(
            'SELECT users.id, users.first_name, users.last_name, users.email
             FROM users
             INNER JOIN roles ON roles.id = users.role_id
             WHERE roles.slug IN ("super_admin", "admin", "staff")
               AND users.status = "active"
             ORDER BY users.first_name ASC, users.last_name ASC'
        );
        $paybackSummary = [];
        foreach ($expenses as $expense) {
            if (($expense['paid_source'] ?? 'company_cash') !== 'admin' || ($expense['reimbursement_status'] ?? 'pending') === 'reimbursed') {
                continue;
            }
            $adminId = (int) ($expense['paid_by_admin_id'] ?? 0);
            $key = $adminId > 0 ? (string) $adminId : 'unknown';
            if (!isset($paybackSummary[$key])) {
                $name = trim(($expense['paid_first_name'] ?? '') . ' ' . ($expense['paid_last_name'] ?? '')) ?: 'Admin';
                $paybackSummary[$key] = ['name' => $name, 'amount' => 0.0, 'count' => 0];
            }
            $paybackSummary[$key]['amount'] += (float) ($expense['amount'] ?? 0);
            $paybackSummary[$key]['count']++;
        }
        $paybackSummary = array_values($paybackSummary);

        View::renderPage('admin/pages/expenses/index', [
            'title' => 'Expenses',
            'adminNavigation' => AdminNavigation::make('expenses'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Expenses']],
            'expenses' => $expenses,
            'orders' => $orders,
            'admins' => $admins,
            'paybackSummary' => $paybackSummary,
            'statuses' => $this->statuses,
            'categories' => $this->categories,
            'filters' => ['period' => $period, 'month' => $month, 'year' => $year, 'status' => $status],
            'total' => array_sum(array_map(static fn (array $expense): float => (float) $expense['amount'], $expenses)),
            'message' => $message,
            'error' => $error,
        ], 'admin/layouts/admin');
    }

    public function store(): void
    {
        $this->ensureExpenseOrderColumn();
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index(null, 'Your session expired. Try again.');
            return;
        }

        $expenseScope = ($_POST['expense_scope'] ?? 'general') === 'order' ? 'order' : 'general';
        $orderId = $expenseScope === 'order' ? (int) ($_POST['order_id'] ?? 0) : 0;
        $title = trim((string) ($_POST['title'] ?? ''));
        $amount = (float) ($_POST['amount'] ?? 0);
        $expenseDate = $this->dateOrToday($_POST['expense_date'] ?? null);
        $category = trim((string) ($_POST['category'] ?? 'Other')) ?: 'Other';
        if ($category === 'Other') {
            $category = trim((string) ($_POST['category_other'] ?? '')) ?: 'Other';
        }
        $paidSource = ($_POST['paid_source'] ?? 'company_cash') === 'admin' ? 'admin' : 'company_cash';
        $paidByAdminId = $paidSource === 'admin' ? (int) ($_POST['paid_by_admin_id'] ?? 0) : 0;
        $reimbursementStatus = $paidSource === 'admin' && ($_POST['reimbursement_status'] ?? 'pending') === 'reimbursed' ? 'reimbursed' : 'pending';
        $status = 'approved';

        if ($title === '' || $amount <= 0) {
            $this->index(null, 'Expense title and amount are required.');
            return;
        }
        if ($expenseScope === 'order') {
            $order = Database::instance()->fetch('SELECT id FROM orders WHERE id = :id LIMIT 1', ['id' => $orderId]);
            if (!$order) {
                $this->index(null, 'Select the order this expense belongs to.');
                return;
            }
        }
        if ($paidSource === 'admin') {
            $admin = Database::instance()->fetch(
                'SELECT users.id
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE users.id = :id
                   AND roles.slug IN ("super_admin", "admin", "staff")
                 LIMIT 1',
                ['id' => $paidByAdminId]
            );
            if (!$admin) {
                $this->index(null, 'Select who paid this expense.');
                return;
            }
        }

        $receipt = $this->storeReceipt();
        Database::instance()->execute(
            'INSERT INTO expenses
             (admin_id, order_id, paid_source, paid_by_admin_id, reimbursement_status, expense_number, category, title, description, amount, expense_date, receipt_path, receipt_original_name, status)
             VALUES (:admin_id, :order_id, :paid_source, :paid_by_admin_id, :reimbursement_status, :expense_number, :category, :title, :description, :amount, :expense_date, :receipt_path, :receipt_original_name, :status)',
            [
                'admin_id' => Auth::id(),
                'order_id' => $orderId > 0 ? $orderId : null,
                'paid_source' => $paidSource,
                'paid_by_admin_id' => $paidByAdminId > 0 ? $paidByAdminId : null,
                'reimbursement_status' => $paidSource === 'admin' ? $reimbursementStatus : null,
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

    public function reimbursement(string $id): void
    {
        $this->ensureExpenseOrderColumn();
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/expenses');
        }

        $status = in_array($_POST['reimbursement_status'] ?? '', $this->reimbursementStatuses, true)
            ? $_POST['reimbursement_status']
            : 'pending';

        Database::instance()->execute(
            'UPDATE expenses
             SET reimbursement_status = :reimbursement_status
             WHERE id = :id AND paid_source = "admin"',
            ['reimbursement_status' => $status, 'id' => (int) $id]
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
        [$previousStartDate, $previousEndDate] = $this->previousPeriod($period, $month, $year);
        $previousSummary = $this->profitSummary($previousStartDate, $previousEndDate);
        $monthlySeries = $this->monthlyReportSeries($year);
        $orderSummary = $this->orderStatusSummary($startDate, $endDate);
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
            'previousSummary' => $previousSummary,
            'monthlySeries' => $monthlySeries,
            'orderSummary' => $orderSummary,
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

    private function previousPeriod(string $period, int $month, int $year): array
    {
        if ($period === 'yearly') {
            $start = new DateTimeImmutable(sprintf('%04d-01-01 00:00:00', $year - 1));
            $end = $start->modify('+1 year');
            return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
        }

        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month)))->modify('-1 month');
        $end = $start->modify('+1 month');
        return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
    }

    private function monthlyReportSeries(int $year): array
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = [
                'month' => $i,
                'label' => date('M', mktime(0, 0, 0, $i, 1)),
                'sales' => 0.0,
                'expenses' => 0.0,
                'net_profit' => 0.0,
                'orders' => 0,
            ];
        }

        $orders = Database::instance()->fetchAll(
            'SELECT MONTH(created_at) AS month_number,
                    COALESCE(SUM(grand_total), 0) AS sales,
                    COUNT(*) AS orders
             FROM orders
             WHERE order_status NOT IN ("cancelled", "refunded")
               AND YEAR(created_at) = :year
             GROUP BY MONTH(created_at)',
            ['year' => $year]
        );
        foreach ($orders as $row) {
            $monthNumber = (int) ($row['month_number'] ?? 0);
            if (isset($months[$monthNumber])) {
                $months[$monthNumber]['sales'] = (float) ($row['sales'] ?? 0);
                $months[$monthNumber]['orders'] = (int) ($row['orders'] ?? 0);
            }
        }

        $costs = Database::instance()->fetchAll(
            'SELECT MONTH(orders.created_at) AS month_number,
                    COALESCE(SUM(order_items.quantity * order_items.cost_price), 0) AS product_cost
             FROM order_items
             INNER JOIN orders ON orders.id = order_items.order_id
             WHERE orders.order_status NOT IN ("cancelled", "refunded")
               AND YEAR(orders.created_at) = :year
             GROUP BY MONTH(orders.created_at)',
            ['year' => $year]
        );
        foreach ($costs as $row) {
            $monthNumber = (int) ($row['month_number'] ?? 0);
            if (isset($months[$monthNumber])) {
                $months[$monthNumber]['product_cost'] = (float) ($row['product_cost'] ?? 0);
            }
        }

        $expenses = Database::instance()->fetchAll(
            'SELECT MONTH(expense_date) AS month_number,
                    COALESCE(SUM(amount), 0) AS expenses
             FROM expenses
             WHERE status IN ("approved", "paid")
               AND YEAR(expense_date) = :year
             GROUP BY MONTH(expense_date)',
            ['year' => $year]
        );
        foreach ($expenses as $row) {
            $monthNumber = (int) ($row['month_number'] ?? 0);
            if (isset($months[$monthNumber])) {
                $months[$monthNumber]['expenses'] = (float) ($row['expenses'] ?? 0);
            }
        }

        foreach ($months as &$monthRow) {
            $monthRow['net_profit'] = (float) $monthRow['sales'] - (float) ($monthRow['product_cost'] ?? 0) - (float) $monthRow['expenses'];
        }
        unset($monthRow);

        return array_values($months);
    }

    private function orderStatusSummary(string $startDate, string $endDate): array
    {
        $rows = Database::instance()->fetchAll(
            'SELECT order_status, COUNT(*) AS total
             FROM orders
             WHERE created_at >= :start_date AND created_at < :end_date
             GROUP BY order_status',
            ['start_date' => $startDate, 'end_date' => $endDate]
        );

        $counts = ['total' => 0, 'delivered' => 0, 'processing' => 0, 'cancelled' => 0];
        foreach ($rows as $row) {
            $status = (string) ($row['order_status'] ?? '');
            $total = (int) ($row['total'] ?? 0);
            $counts['total'] += $total;
            if ($status === 'delivered') {
                $counts['delivered'] += $total;
            } elseif (in_array($status, ['pending', 'confirmed', 'processing', 'ready', 'out_for_delivery'], true)) {
                $counts['processing'] += $total;
            } elseif (in_array($status, ['cancelled', 'refunded'], true)) {
                $counts['cancelled'] += $total;
            }
        }

        return $counts;
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

    private function ensureExpenseOrderColumn(): void
    {
        $columns = Database::instance()->fetchAll(
            'SELECT COLUMN_NAME
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = "expenses"'
        );
        $existing = array_flip(array_map(static fn (array $row): string => (string) $row['COLUMN_NAME'], $columns));

        if (!isset($existing['order_id'])) {
            Database::instance()->execute('ALTER TABLE expenses ADD COLUMN order_id BIGINT UNSIGNED NULL AFTER admin_id');
            Database::instance()->execute('ALTER TABLE expenses ADD INDEX idx_expenses_order (order_id)');
        }
        if (!isset($existing['paid_source'])) {
            Database::instance()->execute('ALTER TABLE expenses ADD COLUMN paid_source VARCHAR(30) NOT NULL DEFAULT "company_cash" AFTER order_id');
        }
        if (!isset($existing['paid_by_admin_id'])) {
            Database::instance()->execute('ALTER TABLE expenses ADD COLUMN paid_by_admin_id BIGINT UNSIGNED NULL AFTER paid_source');
            Database::instance()->execute('ALTER TABLE expenses ADD INDEX idx_expenses_paid_by_admin (paid_by_admin_id)');
        }
        if (!isset($existing['reimbursement_status'])) {
            Database::instance()->execute('ALTER TABLE expenses ADD COLUMN reimbursement_status VARCHAR(30) NULL AFTER paid_by_admin_id');
        }
    }
}
