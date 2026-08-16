<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class InvoiceController extends Controller
{
    public function show(): void
    {
        $number = trim((string) ($_GET['order'] ?? ''));
        $token = trim((string) ($_GET['token'] ?? ''));
        $secret = (string) (getenv('INVOICE_SECRET') ?: 'giftvibe-local-invoice-secret');
        $expected = substr(hash_hmac('sha256', $number, $secret), 0, 24);
        if ($number === '' || $token === '' || !hash_equals($expected, $token)) {
            http_response_code(404);
            exit('Invoice not found.');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_number = ? LIMIT 1');
        $stmt->execute([$number]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            http_response_code(404);
            exit('Invoice not found.');
        }

        $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id');
        $itemsStmt->execute([(int) $order['id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = $pdo->query('SELECT * FROM general_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC) ?: [];
        echo $this->render('admin/orders/receipt', compact('order', 'items', 'settings'));
        exit;
    }
}
