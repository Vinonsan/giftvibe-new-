<?php

namespace Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use DateTimeImmutable;
use Controllers\Admin\ExpenseController;
use Helpers\AdminNavigation;
use Throwable;

class DashboardController
{
    public function index(): void
    {
        [$period, $month, $year, $startDate, $endDate] = $this->resolveFilters();
        $summary = $this->summary($startDate, $endDate);

        View::renderPage('admin/pages/dashboard', [
            'title' => 'Dashboard',
            'adminNavigation' => AdminNavigation::make('dashboard'),
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => url('/admin/dashboard')],
                ['label' => 'Dashboard'],
            ],
            'filters' => [
                'period' => $period,
                'month' => $month,
                'year' => $year,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'summary' => $summary,
        ], 'admin/layouts/admin');
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

    private function summary(string $startDate, string $endDate): array
    {
        try {
            $db = Database::instance();

            $orders = $db->fetch(
                'SELECT
                    COUNT(*) AS total_orders,
                    COALESCE(SUM(grand_total), 0) AS sales,
                    COALESCE(SUM(discount_total), 0) AS discounts,
                    COALESCE(SUM(delivery_fee), 0) AS delivery_fees,
                    COUNT(DISTINCT user_id) AS ordering_customers
                 FROM orders
                 WHERE order_status NOT IN ("cancelled", "refunded")
                   AND created_at >= :start_date AND created_at < :end_date',
                ['start_date' => $startDate, 'end_date' => $endDate]
            ) ?? [];

            $productCost = $db->fetch(
                'SELECT COALESCE(SUM(order_items.quantity * order_items.cost_price), 0) AS expenses
                 FROM order_items
                 INNER JOIN orders ON orders.id = order_items.order_id
                 WHERE orders.order_status NOT IN ("cancelled", "refunded")
                   AND orders.created_at >= :start_date AND orders.created_at < :end_date',
                ['start_date' => $startDate, 'end_date' => $endDate]
            ) ?? [];

            $customers = $db->fetch(
                'SELECT COUNT(*) AS total_customers
                 FROM users
                 INNER JOIN roles ON roles.id = users.role_id
                 WHERE roles.slug = "customer"',
            ) ?? [];

            $stock = $db->fetch(
                'SELECT
                    SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= low_stock_threshold THEN 1 ELSE 0 END) AS low_stock,
                    SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) AS out_of_stock
                 FROM products'
            ) ?? [];
            $parcelRows = $db->fetchAll('SELECT delivery_status, COUNT(*) AS total FROM deliveries GROUP BY delivery_status');
            $reportSummary = (new ExpenseController())->profitSummary($startDate, $endDate);
            $inbox = $db->fetch('SELECT COUNT(*) AS total FROM contact_messages WHERE status IN ("new", "read")') ?? [];
            $customRequests = $db->fetch('SELECT COUNT(*) AS total FROM custom_gift_requests WHERE status IN ("new", "reviewing", "quoted")') ?? [];
            $pendingReviews = $db->fetch('SELECT COUNT(*) AS total FROM reviews WHERE status = "pending"') ?? [];
            $notifications = $db->fetch('SELECT COUNT(*) AS total FROM admin_notifications WHERE status = "unread"') ?? [];
        } catch (Throwable) {
            return $this->emptySummary();
        }

        $sales = (float) ($orders['sales'] ?? 0);
        $costTotal = (float) ($productCost['expenses'] ?? 0);
        $parcelCounts = ['pending' => 0, 'out_for_delivery' => 0, 'delivered' => 0, 'failed' => 0, 'returned' => 0];
        foreach ($parcelRows ?? [] as $row) {
            if (array_key_exists($row['delivery_status'], $parcelCounts)) {
                $parcelCounts[$row['delivery_status']] = (int) $row['total'];
            }
        }

        return [
            'sales' => $sales,
            'orders' => (int) ($orders['total_orders'] ?? 0),
            'profit' => $sales - $costTotal,
            'expenses' => $costTotal,
            'gross_profit' => (float) ($reportSummary['gross_profit'] ?? ($sales - $costTotal)),
            'total_expenses' => (float) ($reportSummary['total_expenses'] ?? 0),
            'net_profit' => (float) ($reportSummary['net_profit'] ?? ($sales - $costTotal)),
            'customers' => (int) ($customers['total_customers'] ?? 0),
            'ordering_customers' => (int) ($orders['ordering_customers'] ?? 0),
            'low_stock' => (int) ($stock['low_stock'] ?? 0),
            'out_of_stock' => (int) ($stock['out_of_stock'] ?? 0),
            'parcel_counts' => $parcelCounts,
            'messages' => (int) ($inbox['total'] ?? 0),
            'custom_requests' => (int) ($customRequests['total'] ?? 0),
            'pending_reviews' => (int) ($pendingReviews['total'] ?? 0),
            'notifications' => (int) ($notifications['total'] ?? 0),
        ];
    }

    private function emptySummary(): array
    {
        return [
            'sales' => 0.0,
            'orders' => 0,
            'profit' => 0.0,
            'expenses' => 0.0,
            'gross_profit' => 0.0,
            'total_expenses' => 0.0,
            'net_profit' => 0.0,
            'customers' => 0,
            'ordering_customers' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'parcel_counts' => ['pending' => 0, 'out_for_delivery' => 0, 'delivered' => 0, 'failed' => 0, 'returned' => 0],
            'messages' => 0,
            'custom_requests' => 0,
            'pending_reviews' => 0,
            'notifications' => 0,
        ];
    }
}
