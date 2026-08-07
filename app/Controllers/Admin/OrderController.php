<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SmsService;
use PDO;

final class OrderController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS customer_notifications (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,order_id BIGINT UNSIGNED NULL,phone VARCHAR(40) NOT NULL,message VARCHAR(500) NOT NULL,status ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,KEY idx_customer_notifications_user(user_id),KEY idx_customer_notifications_order(order_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($pdo);
        }

        $orders = $pdo->query("SELECT o.*,p.id payment_id,p.method,p.amount payment_amount,p.status payment_verification_status,p.receipt_path,p.raw_response_json FROM orders o LEFT JOIN payments p ON p.order_id=o.id ORDER BY CASE WHEN LOWER(o.delivery_city) = 'jaffna' OR LOWER(o.delivery_district) = 'jaffna' THEN 0 ELSE 1 END, o.id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $flash = $_SESSION['orders_flash'] ?? null;
        unset($_SESSION['orders_flash']);
        $content = $this->render('admin/orders/index', ['orders' => $orders, 'csrfToken' => $_SESSION['csrf_token'], 'flash' => $flash]);
        $this->view('layouts/admin-layout', ['title' => 'Orders', 'showPageTitle' => false, 'content' => $content]);
    }

    private function update(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $decision = (string) ($_POST['decision'] ?? '');
        $days = max(1, min(30, (int) ($_POST['delivery_days'] ?? 3)));
        if ($orderId < 1 || !in_array($decision, ['confirm', 'cancel', 'delete_receipt', 'update_status'], true)) {
            $_SESSION['orders_flash'] = ['type' => 'error', 'message' => 'Invalid order action.'];
            header('Location: /admin/orders', true, 303);
            exit;
        }
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            $_SESSION['orders_flash'] = ['type' => 'error', 'message' => 'Order not found.'];
            header('Location: /admin/orders', true, 303);
            exit;
        }

        if ($decision === 'update_status') {
            $newOrderStatus = (string) ($_POST['order_status'] ?? '');
            $newPaymentStatus = (string) ($_POST['payment_status'] ?? '');
            $notes = trim((string) ($_POST['admin_notes'] ?? ''));
            $deliveryDate = trim((string) ($_POST['delivery_date'] ?? '')) ?: null;

            $pdo->prepare("UPDATE orders SET order_status=?, payment_status=?, delivery_date=?, admin_notes=? WHERE id=?")
                ->execute([$newOrderStatus, $newPaymentStatus, $deliveryDate, $notes, $orderId]);
            
            $paymentStatusMap = [
                'paid' => 'paid',
                'pending' => 'pending',
                'failed' => 'failed'
            ];
            $dbPaymentStatus = $paymentStatusMap[$newPaymentStatus] ?? 'pending';
            $pdo->prepare("UPDATE payments SET status=? WHERE order_id=?")->execute([$dbPaymentStatus, $orderId]);

            $statusLabels = [
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'ready' => 'Ready for Delivery',
                'out_for_delivery' => 'Picked up for Courier dispatch',
                'delivered' => 'Completed & Delivered',
                'cancelled' => 'Cancelled'
            ];
            $label = $statusLabels[$newOrderStatus] ?? $newOrderStatus;
            $message = "Your GiftVibe order {$order['order_number']} status is updated to: {$label}.";
            
            $sent = SmsService::send((string) $order['customer_phone'], $message);
            $notice = $pdo->prepare('INSERT INTO customer_notifications(user_id,order_id,phone,message,status) VALUES(?,?,?,?,?)');
            $notice->execute([(int) $order['user_id'], $orderId, $order['customer_phone'], $message, $sent ? 'sent' : 'queued']);

            $_SESSION['orders_flash'] = ['type' => 'success', 'message' => 'Order status updated and customer notified.'];
            header('Location: /admin/orders', true, 303);
            exit;
        }

        if ($decision === 'delete_receipt') {
            $stmt = $pdo->prepare('SELECT receipt_path FROM payments WHERE order_id=? LIMIT 1');
            $stmt->execute([$orderId]);
            $path = $stmt->fetchColumn();
            if ($path && file_exists(BASE_PATH . '/public' . $path)) {
                @unlink(BASE_PATH . '/public' . $path);
            }
            $pdo->prepare("UPDATE payments SET receipt_path=NULL WHERE order_id=?")->execute([$orderId]);
            $_SESSION['orders_flash'] = ['type' => 'success', 'message' => 'Receipt deleted successfully.'];
            header('Location: /admin/orders', true, 303);
            exit;
        }

        if ($decision === 'confirm') {
            $deliveryDate = date('Y-m-d', strtotime("+{$days} days"));
            $paymentMetaStmt = $pdo->prepare('SELECT amount,raw_response_json FROM payments WHERE order_id=? LIMIT 1');
            $paymentMetaStmt->execute([$orderId]);
            $paymentRow = $paymentMetaStmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $paymentMeta = json_decode((string) ($paymentRow['raw_response_json'] ?? ''), true) ?: [];
            $orderPaymentStatus = (float) ($paymentMeta['balance_due'] ?? 0) > 0 ? 'pending' : 'paid';
            $pdo->prepare("UPDATE orders SET order_status='confirmed',payment_status=?,delivery_date=?,admin_notes=? WHERE id=?")
                ->execute([$orderPaymentStatus, $deliveryDate, trim((string) ($_POST['admin_notes'] ?? '')), $orderId]);
            $pdo->prepare("UPDATE payments SET status='paid',verified_by=?,verified_at=NOW(),paid_at=NOW(),verification_notes=? WHERE order_id=?")
                ->execute([(int) $_SESSION['admin_user']['id'], trim((string) ($_POST['admin_notes'] ?? '')), $orderId]);
            $message = "Your GiftVibe order {$order['order_number']} is confirmed. Expected delivery is within {$days} day(s), by {$deliveryDate}.";
        } else {
            $pdo->prepare("UPDATE orders SET order_status='cancelled',payment_status='failed',admin_notes=? WHERE id=?")
                ->execute([trim((string) ($_POST['admin_notes'] ?? '')), $orderId]);
            $pdo->prepare("UPDATE payments SET status='cancelled',verified_by=?,verified_at=NOW(),verification_notes=? WHERE order_id=?")
                ->execute([(int) $_SESSION['admin_user']['id'], trim((string) ($_POST['admin_notes'] ?? '')), $orderId]);
            $message = "Your GiftVibe order {$order['order_number']} was cancelled. Please contact us if you need assistance.";
        }

        $sent = SmsService::send((string) $order['customer_phone'], $message);
        $notice = $pdo->prepare('INSERT INTO customer_notifications(user_id,order_id,phone,message,status) VALUES(?,?,?,?,?)');
        $notice->execute([(int) $order['user_id'], $orderId, $order['customer_phone'], $message, $sent ? 'sent' : 'queued']);
        $pdo->prepare("UPDATE admin_notifications SET status='read',read_at=NOW() WHERE entity_type='order' AND entity_id=?")->execute([$orderId]);
        $_SESSION['orders_flash'] = ['type' => 'success', 'message' => $decision === 'confirm' ? 'Order confirmed and customer notified.' : 'Order cancelled and customer notified.'];
        header('Location: /admin/orders', true, 303);
        exit;
    }

    public function receipt(): void
    {
        $orderId = (int) ($_GET['id'] ?? 0);
        if ($orderId < 1) {
            http_response_code(400);
            exit('Missing order ID.');
        }
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            http_response_code(404);
            exit('Order not found.');
        }

        $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?');
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = $pdo->query('SELECT * FROM general_settings WHERE id=1')->fetch(PDO::FETCH_ASSOC) ?: [];

        echo $this->render('admin/orders/receipt', compact('order', 'items', 'settings'));
        exit;
    }
}
