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

        // Check if user is posting a new manual transaction
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
                http_response_code(419);
                exit('Invalid or expired request token.');
            }

            $action = (string) ($_POST['action'] ?? '');
            if ($action === 'add_transaction') {
                $type = (string) ($_POST['type'] ?? 'income');
                $category = trim((string) ($_POST['category'] ?? ''));
                $amount = max(0.0, (float) ($_POST['amount'] ?? 0));
                $description = trim((string) ($_POST['description'] ?? ''));
                $date = trim((string) ($_POST['date'] ?? '')) ?: date('Y-m-d');

                if ($category !== '' && $amount > 0) {
                    $stmt = $pdo->prepare("INSERT INTO financial_transactions (type, category, amount, description, date) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$type, $category, $amount, $description, $date]);
                    $_SESSION['finance_flash'] = ['type' => 'success', 'message' => 'Transaction logged successfully.'];
                } else {
                    $_SESSION['finance_flash'] = ['type' => 'error', 'message' => 'Please provide a valid category and amount.'];
                }
                header('Location: /admin/finance', true, 303);
                exit;
            }
        }

        // 1. Fetch confirmed/completed orders
        $orders = $pdo->query("SELECT o.*, 
            COALESCE((SELECT SUM(oi.cost_price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id), 0) AS total_cost
            FROM orders o 
            WHERE o.order_status = 'delivered'
            ORDER BY o.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $totalSales = 0.0;
        $totalCost = 0.0;
        $orderReports = [];

        foreach ($orders as $order) {
            $sales = (float)$order['grand_total'];
            $cost = (float)$order['total_cost'];
            
            if ($cost <= 0) {
                $cost = $sales * 0.70;
            }
            
            $profit = $sales - $cost;
            $totalSales += $sales;
            $totalCost += $cost;

            $orderReports[] = [
                'order_number' => $order['order_number'],
                'customer_name' => $order['customer_name'] . ' (Order)',
                'date' => substr($order['created_at'], 0, 10),
                'sales' => $sales,
                'cost' => $cost,
                'profit' => $profit
            ];
        }

        // 2. Fetch manual transactions
        $manuals = $pdo->query("SELECT * FROM financial_transactions ORDER BY date DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($manuals as $m) {
            $amt = (float)$m['amount'];
            if ($m['type'] === 'income') {
                $sales = $amt;
                $cost = 0.0;
                $profit = $amt;
                $totalSales += $amt;
            } else {
                $sales = 0.0;
                $cost = $amt;
                $profit = -$amt;
                $totalCost += $amt;
            }

            $orderReports[] = [
                'order_number' => 'MANUAL-' . str_pad((string)$m['id'], 3, '0', STR_PAD_LEFT),
                'customer_name' => $m['category'] . ' (' . ucfirst($m['type']) . ')',
                'date' => $m['date'],
                'sales' => $sales,
                'cost' => $cost,
                'profit' => $profit
            ];
        }

        $totalProfit = $totalSales - $totalCost;
        $margin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        // 3. Mock monthly financial data for visual charts
        $monthlyReport = [
            ['month' => 'March', 'sales' => 125000, 'cost' => 87500, 'profit' => 37500],
            ['month' => 'April', 'sales' => 148000, 'cost' => 103600, 'profit' => 44400],
            ['month' => 'May', 'sales' => 189000, 'cost' => 132300, 'profit' => 56700],
            ['month' => 'June', 'sales' => 220000, 'cost' => 154000, 'profit' => 66000],
            ['month' => 'July', 'sales' => 265000, 'cost' => 185500, 'profit' => 79500],
            ['month' => 'August', 'sales' => $totalSales > 0 ? $totalSales : 290000, 'cost' => $totalCost > 0 ? $totalCost : 203000, 'profit' => $totalSales > 0 ? $totalProfit : 87000],
        ];

        $flash = $_SESSION['finance_flash'] ?? null;
        unset($_SESSION['finance_flash']);

        $content = $this->render('admin/finance/index', [
            'totalSales' => $totalSales,
            'totalCost' => $totalCost,
            'totalProfit' => $totalProfit,
            'margin' => $margin,
            'orderReports' => $orderReports,
            'monthlyReport' => $monthlyReport,
            'csrfToken' => $_SESSION['csrf_token'],
            'flash' => $flash
        ]);
        
        $this->view('layouts/admin-layout', ['title' => 'Financial Reports', 'showPageTitle' => false, 'content' => $content]);
    }
}
