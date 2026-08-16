<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FinanceSummaryService;
use PDO;

final class FinanceController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();

        $orders = $pdo->query(
            "SELECT o.id, o.order_number, o.customer_name, o.grand_total, o.created_at, p.amount AS received_amount,
                COALESCE((SELECT SUM(oi.cost_price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id), 0) AS total_cost
             FROM orders o
             INNER JOIN payments p ON p.order_id = o.id
             WHERE o.order_status NOT IN ('cancelled','refunded')
               AND ((p.method = 'cod' AND p.status NOT IN ('failed','cancelled','refunded'))
                 OR (p.method = 'bank_deposit' AND p.status = 'paid'))
               AND p.amount > 0
             ORDER BY o.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $totalSales = 0.0;
        $totalCost = 0.0;
        $orderReports = [];

        foreach ($orders as $order) {
            $sales = (float) $order['received_amount'];
            $orderTotal = max(0.01, (float) $order['grand_total']);
            $fullCost = (float) $order['total_cost'];
            if ($fullCost <= 0) $fullCost = $orderTotal * 0.70;
            $cost = $fullCost * min(1, $sales / $orderTotal);
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
        $cashSummary = FinanceSummaryService::summary($pdo);
        $procurementReports = [];
        try {
            $procurementReports = $pdo->query("SELECT pp.id,pp.product_id,p.cost_price AS unit_cost,p.stock_quantity AS quantity,pp.amount,pp.created_at,p.name,p.sku FROM product_procurements pp INNER JOIN products p ON p.id=pp.product_id ORDER BY pp.id DESC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException) {
            $procurementReports = [];
        }

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
                'cashOnHand' => (float) $cashSummary['cashOnHand'],
                'productPurchases' => (float) $cashSummary['productPurchases'],
                'totalInvestments' => (float) $cashSummary['totalInvestments'],
                'businessExpenses' => (float) $cashSummary['businessExpenses'],
                'procurementReports' => $procurementReports,
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
                COALESCE(SUM(p.amount), 0) AS sales,
                COALESCE(SUM(
                    COALESCE((SELECT SUM(oi.cost_price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id), o.grand_total * 0.70)
                    * LEAST(1, p.amount / NULLIF(o.grand_total, 0))
                ), 0) AS cost
             FROM orders o
             INNER JOIN payments p ON p.order_id = o.id
             WHERE o.order_status NOT IN ('cancelled','refunded')
               AND ((p.method = 'cod' AND p.status NOT IN ('failed','cancelled','refunded'))
                 OR (p.method = 'bank_deposit' AND p.status = 'paid'))
               AND p.amount > 0
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
