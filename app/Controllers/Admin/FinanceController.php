<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class FinanceController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();

        $orders = $pdo->query(
            "SELECT o.id, o.order_number, o.customer_name, o.grand_total, o.created_at,
                COALESCE((SELECT SUM(oi.cost_price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id), 0) AS total_cost
             FROM orders o
             WHERE o.order_status = 'delivered'
             ORDER BY o.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $totalSales = 0.0;
        $totalCost = 0.0;
        $orderReports = [];

        foreach ($orders as $order) {
            $sales = (float) $order['grand_total'];
            $cost = (float) $order['total_cost'];
            if ($cost <= 0) {
                $cost = $sales * 0.70;
            }
            $profit = $sales - $cost;
            $totalSales += $sales;
            $totalCost += $cost;

            $orderReports[] = [
                'order_id' => (int) $order['id'],
                'order_number' => (string) $order['order_number'],
                'customer_name' => (string) $order['customer_name'],
                'date' => substr((string) $order['created_at'], 0, 10),
                'sales' => $sales,
                'cost' => $cost,
                'profit' => $profit,
                'source' => 'order',
            ];
        }

        usort($orderReports, static fn(array $a, array $b): int => strcmp($b['date'], $a['date']));

        $totalProfit = $totalSales - $totalCost;
        $margin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;
        $monthlyReport = $this->buildMonthlyReport($pdo);
        $deliveredCount = count($orders);

        $this->view('layouts/admin-layout', [
            'title' => 'Financial Reports',
            'pageTitle' => 'Financial Reports',
            'showPageTitle' => false,
            'content' => $this->render('admin/finance/index', [
                'totalSales' => $totalSales,
                'totalCost' => $totalCost,
                'totalProfit' => $totalProfit,
                'margin' => $margin,
                'orderReports' => $orderReports,
                'monthlyReport' => $monthlyReport,
                'deliveredCount' => $deliveredCount,
            ]),
        ]);
    }

    /** @return list<array{month: string, sales: float, cost: float, profit: float}> */
    private function buildMonthlyReport(PDO $pdo): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('Y-m', strtotime("-{$i} months"));
        }

        $orderRows = $pdo->query(
            "SELECT DATE_FORMAT(o.created_at, '%Y-%m') AS ym,
                COALESCE(SUM(o.grand_total), 0) AS sales,
                COALESCE(SUM(
                    (SELECT SUM(oi.cost_price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id)
                ), 0) AS cost
             FROM orders o
             WHERE o.order_status = 'delivered'
               AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY ym"
        )->fetchAll(PDO::FETCH_ASSOC);

        $orderMap = [];
        foreach ($orderRows as $row) {
            $orderMap[(string) $row['ym']] = [
                'sales' => (float) $row['sales'],
                'cost' => (float) $row['cost'],
            ];
        }

        $report = [];
        foreach ($months as $ym) {
            $sales = $orderMap[$ym]['sales'] ?? 0;
            $cost = $orderMap[$ym]['cost'] ?? 0;
            if ($cost <= 0 && $sales > 0) {
                $cost = $sales * 0.70;
            }
            $report[] = [
                'month' => date('M Y', strtotime($ym . '-01')),
                'sales' => $sales,
                'cost' => $cost,
                'profit' => $sales - $cost,
            ];
        }

        return $report;
    }
}
