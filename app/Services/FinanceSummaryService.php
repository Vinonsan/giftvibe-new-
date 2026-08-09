<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

final class FinanceSummaryService
{
    /** @return array<string, mixed> */
    public static function summary(PDO $pdo): array
    {
        $incomeRow = $pdo->query(
            "SELECT COALESCE(SUM(o.grand_total), 0) AS income, COUNT(*) AS cnt
             FROM orders o WHERE o.order_status = 'delivered'"
        )->fetch(PDO::FETCH_ASSOC) ?: ['income' => 0, 'cnt' => 0];

        $cogsRow = $pdo->query(
            "SELECT COALESCE(SUM(oi.cost_price * oi.quantity), 0) AS cogs
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.order_status = 'delivered'"
        )->fetch(PDO::FETCH_ASSOC) ?: ['cogs' => 0];

        $totalIncome = (float) ($incomeRow['income'] ?? 0);
        $totalCogs = (float) ($cogsRow['cogs'] ?? 0);
        if ($totalCogs <= 0 && $totalIncome > 0) {
            $totalCogs = $totalIncome * 0.70;
        }

        $businessExpenses = self::businessExpensesTotal($pdo);
        $totalInvestments = self::investmentTotal($pdo);
        $productPurchases = self::productProcurementTotal($pdo);
        $grossProfit = $totalIncome - $totalCogs;
        $netProfit = $grossProfit - $businessExpenses;
        $cashOnHand = $totalIncome + $totalInvestments - $businessExpenses - $productPurchases;

        $pendingOrders = (int) ($pdo->query(
            "SELECT COUNT(*) FROM orders WHERE order_status NOT IN ('delivered','cancelled','refunded')"
        )->fetchColumn() ?: 0);

        $productCount = (int) ($pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn() ?: 0);

        $customerCount = (int) ($pdo->query(
            "SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'"
        )->fetchColumn() ?: 0);

        $expenseCount = (int) ($pdo->query('SELECT COUNT(*) FROM expenses')->fetchColumn() ?: 0);

        $recentOrders = $pdo->query(
            "SELECT id, order_number, customer_name, grand_total, order_status, payment_status, created_at
             FROM orders ORDER BY id DESC LIMIT 8"
        )->fetchAll(PDO::FETCH_ASSOC);

        $recentExpenses = [];
        try {
            $recentExpenses = $pdo->query(
                'SELECT id, expense_number, title, category, amount, expense_date, status
                 FROM expenses ORDER BY expense_date DESC, id DESC LIMIT 8'
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException) {
            $recentExpenses = [];
        }

        return [
            'totalIncome' => $totalIncome,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'businessExpenses' => $businessExpenses,
            'totalInvestments' => $totalInvestments,
            'productPurchases' => $productPurchases,
            'netProfit' => $netProfit,
            'cashOnHand' => $cashOnHand,
            'margin' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
            'deliveredCount' => (int) ($incomeRow['cnt'] ?? 0),
            'pendingOrders' => $pendingOrders,
            'productCount' => $productCount,
            'customerCount' => $customerCount,
            'expenseCount' => $expenseCount,
            'recentOrders' => $recentOrders,
            'recentExpenses' => $recentExpenses,
        ];
    }

    public static function businessExpensesTotal(PDO $pdo): float
    {
        try {
            return (float) ($pdo->query(
                "SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE status IN ('approved','paid')"
            )->fetchColumn() ?: 0);
        } catch (\PDOException) {
            return 0.0;
        }
    }

    public static function investmentTotal(PDO $pdo): float
    {
        try {
            return (float) ($pdo->query(
                "SELECT COALESCE(SUM(amount), 0) FROM investments WHERE status = 'received'"
            )->fetchColumn() ?: 0);
        } catch (\PDOException) {
            return 0.0;
        }
    }

    public static function productProcurementTotal(PDO $pdo): float
    {
        try {
            return (float) ($pdo->query('SELECT COALESCE(SUM(amount), 0) FROM product_procurements')->fetchColumn() ?: 0);
        } catch (\PDOException) {
            return 0.0;
        }
    }
}
