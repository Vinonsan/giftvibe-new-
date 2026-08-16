<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FinanceSummaryService;
use PDO;

final class InvestmentController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $this->ensureSchema($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->handlePost($pdo);

        $investments = $pdo->query('SELECT * FROM investments ORDER BY investment_date DESC, id DESC')->fetchAll(PDO::FETCH_ASSOC);
        $summary = FinanceSummaryService::summary($pdo);
        $flash = $_SESSION['investment_flash'] ?? null;
        unset($_SESSION['investment_flash']);
        $this->view('layouts/admin-layout', [
            'title' => 'Investments', 'pageTitle' => 'Investments', 'showPageTitle' => false,
            'content' => $this->render('admin/investments/index', [
                'investments'=>$investments, 'totalInvestments'=>(float)$summary['totalInvestments'],
                'cashOnHand'=>(float)$summary['cashOnHand'], 'csrfToken'=>$_SESSION['csrf_token'], 'flash'=>$flash,
            ]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)($_POST['csrf_token'] ?? ''))) { http_response_code(419); exit('Invalid request token.'); }
        $action = (string)($_POST['action'] ?? 'save');
        $id = max(0, (int)($_POST['id'] ?? 0));
        if ($action === 'delete' && $id > 0) {
            $pdo->prepare('DELETE FROM investments WHERE id=?')->execute([$id]);
            $this->redirect('Investment deleted.');
        }
        $amount = (float)($_POST['amount'] ?? 0);
        $date = date('Y-m-d');
        if ($amount <= 0) $this->redirect('Enter a valid investment amount.', 'error');
        if ($id > 0) {
            $pdo->prepare("UPDATE investments SET member_name=?,phone=?,amount=?,investment_date=?,notes=?,status='received' WHERE id=?")
                ->execute(['Investment',null,$amount,$date,null,$id]);
            $this->redirect('Investment updated.');
        }
        $pdo->prepare("INSERT INTO investments(member_name,phone,amount,investment_date,notes,status) VALUES(?,?,?,?,?,'received')")
            ->execute(['Investment',null,$amount,$date,null]);
        $this->redirect('Investment added to cash on hand.');
    }

    private function ensureSchema(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS investments (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,member_name VARCHAR(190) NOT NULL,phone VARCHAR(40) NULL,amount DECIMAL(12,2) NOT NULL DEFAULT 0,investment_date DATE NOT NULL,notes TEXT NULL,status ENUM('received','returned','cancelled') NOT NULL DEFAULT 'received',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,KEY idx_investment_date_status(investment_date,status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function redirect(string $message, string $type='success'): never
    {
        $_SESSION['investment_flash'] = compact('message','type');
        header('Location: ' . app_url('/admin/investments'), true, 303); exit;
    }
}
