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
        $this->ensureNotificationSchema($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($pdo);
        }

        $orders = $pdo->query(
            "SELECT o.*, p.id payment_id, p.method, p.amount payment_amount, p.status payment_verification_status, p.receipt_path, p.raw_response_json
             FROM orders o
             LEFT JOIN payments p ON p.order_id = o.id
             ORDER BY CASE WHEN LOWER(o.delivery_city) = 'jaffna' OR LOWER(o.delivery_district) = 'jaffna' THEN 0 ELSE 1 END, o.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $flash = $_SESSION['orders_flash'] ?? null;
        unset($_SESSION['orders_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Orders',
            'pageTitle' => 'Orders',
            'showPageTitle' => false,
            'content' => $this->render('admin/orders/index', [
                'orders' => $orders,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $this->ensureNotificationSchema($pdo);
        $this->ensureOrderSchema($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($pdo);
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('Order not found.', 'error');
        }

        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            $this->redirect('Order not found.', 'error');
        }

        $itemsStmt = $pdo->prepare(
            'SELECT oi.*, p.short_description, p.slug,
                COALESCE(pi.image_path, \'/assets/images/hero_slide_1.jpg\') AS product_image
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN product_images pi ON pi.product_id = oi.product_id AND pi.is_primary = 1
             WHERE oi.order_id = ?
             ORDER BY oi.id'
        );
        $itemsStmt->execute([$id]);
        $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $paymentStmt = $pdo->prepare('SELECT * FROM payments WHERE order_id = ? LIMIT 1');
        $paymentStmt->execute([$id]);
        $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $deliveryStmt = $pdo->prepare('SELECT * FROM deliveries WHERE order_id = ? LIMIT 1');
        $deliveryStmt->execute([$id]);
        $delivery = $deliveryStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $statusHistory = $this->fetchStatusHistory($pdo, $id);
        $deliveryHistory = $delivery ? $this->fetchDeliveryHistory($pdo, $id) : [];

        $tabs = ['overview', 'items', 'payment', 'tracking', 'history'];
        $tab = strtolower((string) ($_GET['tab'] ?? 'overview'));
        if (!in_array($tab, $tabs, true)) {
            $tab = 'overview';
        }

        $flash = $_SESSION['orders_flash'] ?? null;
        unset($_SESSION['orders_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'View Order',
            'pageTitle' => 'View Order',
            'showPageTitle' => false,
            'content' => $this->render('admin/orders/show', compact(
                'order',
                'orderItems',
                'payment',
                'delivery',
                'statusHistory',
                'deliveryHistory',
                'tab',
                'tabs'
            ) + ['csrfToken' => $_SESSION['csrf_token'], 'flash' => $flash]),
        ]);
    }

    public function receipt(): void
    {
        $orderId = (int) ($_GET['id'] ?? 0);
        if ($orderId < 1) {
            http_response_code(400);
            exit('Missing order ID.');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            http_response_code(404);
            exit('Order not found.');
        }

        $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = $pdo->query('SELECT * FROM general_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC) ?: [];

        echo $this->render('admin/orders/receipt', compact('order', 'items', 'settings'));
        exit;
    }

    private function update(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $decision = (string) ($_POST['decision'] ?? '');
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        $days = max(1, min(30, (int) ($_POST['delivery_days'] ?? 3)));

        $allowed = ['confirm', 'cancel', 'payment_pending', 'delete_receipt', 'update_status', 'advance_status', 'save_tracking', 'update_payment', 'mark_paid'];
        if ($orderId < 1 || !in_array($decision, $allowed, true)) {
            $this->redirect('Invalid order action.', 'error', $orderId ?: null, $redirectTo);
        }

        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            $this->redirect('Order not found.', 'error', null, $redirectTo);
        }

        if ($decision === 'save_tracking') {
            $this->saveTracking($pdo, $orderId, $order);
            $this->redirect('Tracking details saved.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=tracking');
        }

        if ($decision === 'update_payment') {
            $newPaymentStatus = (string) ($_POST['payment_status'] ?? '');
            if (!in_array($newPaymentStatus, ['pending', 'paid', 'failed'], true)) {
                $this->redirect('Invalid payment status.', 'error', $orderId, $redirectTo);
            }
            $pdo->prepare('UPDATE orders SET payment_status = ? WHERE id = ?')->execute([$newPaymentStatus, $orderId]);
            $pdo->prepare('UPDATE payments SET status = ? WHERE order_id = ?')->execute([$newPaymentStatus, $orderId]);
            $this->recordOrderStatus($pdo, $orderId, 'payment_' . $newPaymentStatus, 'Payment status updated to ' . $newPaymentStatus);
            $this->redirect('Payment status updated.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'update_status') {
            $newOrderStatus = (string) ($_POST['order_status'] ?? '');
            $newPaymentStatus = (string) ($_POST['payment_status'] ?? '');
            $notes = trim((string) ($_POST['admin_notes'] ?? ''));
            $deliveryDate = trim((string) ($_POST['delivery_date'] ?? '')) ?: null;

            $pdo->prepare('UPDATE orders SET order_status = ?, payment_status = ?, delivery_date = ?, admin_notes = ? WHERE id = ?')
                ->execute([$newOrderStatus, $newPaymentStatus, $deliveryDate, $notes, $orderId]);

            $dbPaymentStatus = match ($newPaymentStatus) {
                'paid' => 'paid',
                'failed' => 'failed',
                default => 'pending',
            };
            $pdo->prepare('UPDATE payments SET status = ? WHERE order_id = ?')->execute([$dbPaymentStatus, $orderId]);

            $this->recordOrderStatus($pdo, $orderId, $newOrderStatus, $notes ?: null);

            $statusLabels = [
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'ready' => 'Ready for Delivery',
                'out_for_delivery' => 'Picked up for Courier dispatch',
                'delivered' => 'Completed & Delivered',
                'cancelled' => 'Cancelled',
            ];
            $label = $statusLabels[$newOrderStatus] ?? $newOrderStatus;
            $message = "Your GiftVibe order {$order['order_number']} status is updated to: {$label}.";

            $sent = SmsService::send((string) $order['customer_phone'], $message);
            $notice = $pdo->prepare('INSERT INTO customer_notifications(user_id, order_id, phone, message, status) VALUES (?,?,?,?,?)');
            $notice->execute([(int) $order['user_id'], $orderId, $order['customer_phone'], $message, $sent ? 'sent' : 'queued']);

            $this->redirect('Order status updated and customer notified.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'payment_pending') {
            $defaultMsg = "Your GiftVibe order {$order['order_number']} payment is pending. Please complete payment and upload your bank receipt to continue.";
            $message = trim((string) ($_POST['customer_message'] ?? '')) ?: $defaultMsg;
            $notes = trim((string) ($_POST['admin_notes'] ?? '')) ?: $message;

            $pdo->prepare("UPDATE orders SET order_status = 'pending', payment_status = 'pending', admin_notes = ? WHERE id = ?")
                ->execute([$notes, $orderId]);
            $pdo->prepare("UPDATE payments SET status = 'pending' WHERE order_id = ?")->execute([$orderId]);
            $this->recordOrderStatus($pdo, $orderId, 'payment_pending', $message);
            $this->notifyCustomer($pdo, $order, $orderId, $message);
            $this->redirect('Payment pending message sent to customer.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'mark_paid') {
            $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?")->execute([$orderId]);
            $pdo->prepare("UPDATE payments SET status = 'paid', paid_at = NOW(), verified_by = ?, verified_at = NOW() WHERE order_id = ?")
                ->execute([(int) ($_SESSION['admin_user']['id'] ?? 0), $orderId]);
            $this->recordOrderStatus($pdo, $orderId, 'payment_paid', 'Payment marked as paid');
            $this->notifyCustomer($pdo, $order, $orderId, "Payment for GiftVibe order {$order['order_number']} has been verified. Thank you!");
            $this->redirect('Payment marked as paid.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'advance_status') {
            $newOrderStatus = (string) ($_POST['order_status'] ?? '');
            $allowedStatuses = ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'];
            if (!in_array($newOrderStatus, $allowedStatuses, true)) {
                $this->redirect('Invalid order status.', 'error', $orderId, $redirectTo);
            }

            if ($newOrderStatus === 'delivered') {
                $this->completeOrder($pdo, $orderId, $order);
                $this->redirect('Order marked as delivered and added to finance.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=overview');
            }

            $pdo->prepare('UPDATE orders SET order_status = ? WHERE id = ?')->execute([$newOrderStatus, $orderId]);
            $this->recordOrderStatus($pdo, $orderId, $newOrderStatus, null);

            $labels = [
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'ready' => 'Ready for delivery',
                'out_for_delivery' => 'Out for delivery',
            ];
            $label = $labels[$newOrderStatus] ?? $newOrderStatus;
            $this->notifyCustomer($pdo, $order, $orderId, "Your GiftVibe order {$order['order_number']} status is now: {$label}.");
            $this->redirect('Order status updated and customer notified.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'delete_receipt') {
            $stmt = $pdo->prepare('SELECT receipt_path FROM payments WHERE order_id = ? LIMIT 1');
            $stmt->execute([$orderId]);
            $path = $stmt->fetchColumn();
            if ($path && file_exists(BASE_PATH . '/public' . $path)) {
                @unlink(BASE_PATH . '/public' . $path);
            }
            $pdo->prepare('UPDATE payments SET receipt_path = NULL WHERE order_id = ?')->execute([$orderId]);
            $this->redirect('Receipt deleted successfully.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'confirm') {
            $deliveryDate = date('Y-m-d', strtotime("+{$days} days"));
            $paymentMetaStmt = $pdo->prepare('SELECT amount, raw_response_json FROM payments WHERE order_id = ? LIMIT 1');
            $paymentMetaStmt->execute([$orderId]);
            $paymentRow = $paymentMetaStmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $paymentMeta = json_decode((string) ($paymentRow['raw_response_json'] ?? ''), true) ?: [];
            $orderPaymentStatus = (float) ($paymentMeta['balance_due'] ?? 0) > 0 ? 'pending' : 'paid';
            $notes = trim((string) ($_POST['admin_notes'] ?? ''));

            $pdo->prepare("UPDATE orders SET order_status = 'confirmed', payment_status = ?, delivery_date = ?, admin_notes = ? WHERE id = ?")
                ->execute([$orderPaymentStatus, $deliveryDate, $notes, $orderId]);
            if ($orderPaymentStatus === 'paid') {
                $pdo->prepare("UPDATE payments SET status = 'paid', verified_by = ?, verified_at = NOW(), paid_at = NOW(), verification_notes = ? WHERE order_id = ?")
                    ->execute([(int) ($_SESSION['admin_user']['id'] ?? 0), $notes, $orderId]);
            }

            $this->recordOrderStatus($pdo, $orderId, 'confirmed', $notes ?: 'Order accepted by admin');
            $this->ensureDeliveryRow($pdo, $orderId, $deliveryDate);

            $message = "Your GiftVibe order {$order['order_number']} is confirmed. Expected delivery is within {$days} day(s), by {$deliveryDate}.";
            $this->notifyCustomer($pdo, $order, $orderId, $message);
            $pdo->prepare("UPDATE admin_notifications SET status = 'read', read_at = NOW() WHERE entity_type = 'order' AND entity_id = ?")->execute([$orderId]);
            $this->redirect('Order accepted and customer notified.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        if ($decision === 'cancel') {
            $notes = trim((string) ($_POST['admin_notes'] ?? ''));
            $pdo->prepare("UPDATE orders SET order_status = 'cancelled', payment_status = 'failed', admin_notes = ? WHERE id = ?")
                ->execute([$notes, $orderId]);
            $pdo->prepare("UPDATE payments SET status = 'cancelled', verified_by = ?, verified_at = NOW(), verification_notes = ? WHERE order_id = ?")
                ->execute([(int) ($_SESSION['admin_user']['id'] ?? 0), $notes, $orderId]);

            $this->recordOrderStatus($pdo, $orderId, 'cancelled', $notes ?: 'Order rejected by admin');
            $message = "Your GiftVibe order {$order['order_number']} was rejected. Please contact us if you need assistance.";
            $this->notifyCustomer($pdo, $order, $orderId, $message);
            $pdo->prepare("UPDATE admin_notifications SET status = 'read', read_at = NOW() WHERE entity_type = 'order' AND entity_id = ?")->execute([$orderId]);
            $this->redirect('Order rejected and customer notified.', 'success', $orderId, $redirectTo ?: '/admin/orders/view?id=' . $orderId . '&tab=payment');
        }

        $this->redirect('Invalid order action.', 'error', $orderId, $redirectTo);
    }

    private function completeOrder(PDO $pdo, int $orderId, array $order): void
    {
        $pdo->prepare("UPDATE orders SET order_status = 'delivered', payment_status = 'paid' WHERE id = ?")->execute([$orderId]);
        $pdo->prepare("UPDATE payments SET status = 'paid', paid_at = COALESCE(paid_at, NOW()), verified_by = COALESCE(verified_by, ?), verified_at = COALESCE(verified_at, NOW()) WHERE order_id = ?")
            ->execute([(int) ($_SESSION['admin_user']['id'] ?? 0), $orderId]);

        $deliveryStmt = $pdo->prepare('SELECT id FROM deliveries WHERE order_id = ? LIMIT 1');
        $deliveryStmt->execute([$orderId]);
        $deliveryId = $deliveryStmt->fetchColumn();
        if ($deliveryId) {
            $pdo->prepare("UPDATE deliveries SET delivery_status = 'delivered', delivered_date = COALESCE(delivered_date, CURDATE()) WHERE id = ?")
                ->execute([$deliveryId]);
        }

        $this->recordOrderStatus($pdo, $orderId, 'delivered', 'Order completed — revenue recorded in finance');
        $this->notifyCustomer($pdo, $order, $orderId, "Your GiftVibe order {$order['order_number']} has been delivered. Thank you for shopping with us!");
    }

    private function notifyCustomer(PDO $pdo, array $order, int $orderId, string $message): void
    {
        $sent = SmsService::send((string) $order['customer_phone'], $message);
        $notice = $pdo->prepare('INSERT INTO customer_notifications(user_id, order_id, phone, message, status) VALUES (?,?,?,?,?)');
        $notice->execute([(int) $order['user_id'], $orderId, $order['customer_phone'], $message, $sent ? 'sent' : 'queued']);
    }

    private function saveTracking(PDO $pdo, int $orderId, array $order): void
    {
        $courier = trim((string) ($_POST['courier_service_name'] ?? ''));
        $trackingNumber = trim((string) ($_POST['courier_tracking_number'] ?? ''));
        $parcelRef = trim((string) ($_POST['parcel_reference_number'] ?? ''));
        $trackingCode = trim((string) ($_POST['tracking_code'] ?? ''));
        $deliveryStatus = (string) ($_POST['delivery_status'] ?? 'pending');
        $dispatchDate = trim((string) ($_POST['dispatch_date'] ?? '')) ?: null;
        $expectedDate = trim((string) ($_POST['expected_delivery_date'] ?? '')) ?: null;
        $deliveredDate = trim((string) ($_POST['delivered_date'] ?? '')) ?: null;

        $allowedStatuses = ['pending', 'assigned', 'dispatched', 'picked_up', 'out_for_delivery', 'delivered', 'failed', 'returned'];
        if (!in_array($deliveryStatus, $allowedStatuses, true)) {
            $deliveryStatus = 'pending';
        }

        if ($trackingCode === '') {
            $trackingCode = 'GV-' . strtoupper(substr(md5($order['order_number'] . $orderId), 0, 8));
        }

        $existingStmt = $pdo->prepare('SELECT id, delivery_status FROM deliveries WHERE order_id = ? LIMIT 1');
        $existingStmt->execute([$orderId]);
        $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $pdo->prepare(
                'UPDATE deliveries SET courier_service_name = ?, courier_tracking_number = ?, parcel_reference_number = ?,
                 tracking_code = ?, delivery_status = ?, dispatch_date = ?, expected_delivery_date = ?, delivered_date = ? WHERE id = ?'
            )->execute([$courier, $trackingNumber, $parcelRef, $trackingCode, $deliveryStatus, $dispatchDate, $expectedDate, $deliveredDate, $existing['id']]);
            $deliveryId = (int) $existing['id'];
        } else {
            $pdo->prepare(
                'INSERT INTO deliveries (order_id, courier_service_name, courier_tracking_number, parcel_reference_number, tracking_code, delivery_status, dispatch_date, expected_delivery_date, delivered_date)
                 VALUES (?,?,?,?,?,?,?,?,?)'
            )->execute([$orderId, $courier, $trackingNumber, $parcelRef, $trackingCode, $deliveryStatus, $dispatchDate, $expectedDate, $deliveredDate]);
            $deliveryId = (int) $pdo->lastInsertId();
        }

        if (!$existing || $existing['delivery_status'] !== $deliveryStatus) {
            $pdo->prepare(
                'INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by) VALUES (?,?,?,?,?)'
            )->execute([
                $deliveryId,
                $orderId,
                $deliveryStatus,
                trim((string) ($_POST['tracking_note'] ?? '')) ?: null,
                (int) ($_SESSION['admin_user']['id'] ?? 0) ?: null,
            ]);

            $stageLabels = [
                'pending' => 'Pending',
                'assigned' => 'Assigned to courier',
                'dispatched' => 'Dispatched',
                'picked_up' => 'Picked up',
                'out_for_delivery' => 'Out for delivery',
                'delivered' => 'Delivered',
                'failed' => 'Delivery failed',
                'returned' => 'Returned',
            ];
            $stageLabel = $stageLabels[$deliveryStatus] ?? $deliveryStatus;
            $trackMsg = "GiftVibe order {$order['order_number']} parcel update: {$stageLabel}.";
            if ($trackingNumber !== '') {
                $trackMsg .= " Tracking: {$trackingNumber}.";
            }
            if ($trackingCode !== '') {
                $trackMsg .= " Code: {$trackingCode}.";
            }
            $this->notifyCustomer($pdo, $order, $orderId, $trackMsg);
        }

        if ($deliveryStatus === 'out_for_delivery' && $order['order_status'] !== 'out_for_delivery') {
            $pdo->prepare("UPDATE orders SET order_status = 'out_for_delivery' WHERE id = ?")->execute([$orderId]);
            $this->recordOrderStatus($pdo, $orderId, 'out_for_delivery', 'Updated from tracking panel');
        }
        if ($deliveryStatus === 'delivered' && $order['order_status'] !== 'delivered') {
            $this->completeOrder($pdo, $orderId, $order);
        }
    }

    private function ensureDeliveryRow(PDO $pdo, int $orderId, string $expectedDate): void
    {
        $stmt = $pdo->prepare('SELECT id FROM deliveries WHERE order_id = ? LIMIT 1');
        $stmt->execute([$orderId]);
        if ($stmt->fetchColumn()) {
            $pdo->prepare('UPDATE deliveries SET expected_delivery_date = ? WHERE order_id = ?')->execute([$expectedDate, $orderId]);
            return;
        }

        $trackingCode = 'GV-' . strtoupper(substr(md5((string) $orderId . time()), 0, 8));
        $pdo->prepare(
            'INSERT INTO deliveries (order_id, tracking_code, expected_delivery_date, delivery_status) VALUES (?,?,?,?)'
        )->execute([$orderId, $trackingCode, $expectedDate, 'pending']);
    }

    private function recordOrderStatus(PDO $pdo, int $orderId, string $status, ?string $note = null): void
    {
        try {
            $this->ensureOrderSchema($pdo);
            $pdo->prepare('INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (?,?,?,?)')
                ->execute([$orderId, $status, $note, (int) ($_SESSION['admin_user']['id'] ?? 0) ?: null]);
        } catch (\PDOException) {
            // History table unavailable — order update still proceeds.
        }
    }

    private function fetchStatusHistory(PDO $pdo, int $orderId): array
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC, id DESC'
            );
            $stmt->execute([$orderId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                $row['changed_by_name'] = $this->resolveUserName($pdo, (int) ($row['changed_by'] ?? 0));
            }
            unset($row);
            return $rows;
        } catch (\PDOException) {
            return [];
        }
    }

    private function fetchDeliveryHistory(PDO $pdo, int $orderId): array
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT * FROM delivery_status_history WHERE order_id = ? ORDER BY created_at DESC, id DESC'
            );
            $stmt->execute([$orderId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                $row['changed_by_name'] = $this->resolveUserName($pdo, (int) ($row['changed_by'] ?? 0));
            }
            unset($row);
            return $rows;
        } catch (\PDOException) {
            return [];
        }
    }

    private function resolveUserName(PDO $pdo, int $userId): string
    {
        if ($userId < 1) {
            return '';
        }
        try {
            $stmt = $pdo->prepare('SELECT first_name, last_name, email FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                return '';
            }
            $name = trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')));
            return $name !== '' ? $name : (string) ($user['email'] ?? '');
        } catch (\PDOException) {
            return '';
        }
    }

    private function ensureOrderSchema(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS order_status_history (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                status VARCHAR(80) NOT NULL,
                note TEXT NULL,
                changed_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_order_status_history_order (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS delivery_status_history (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                delivery_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NOT NULL,
                status VARCHAR(80) NOT NULL,
                note TEXT NULL,
                changed_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_delivery_status_history_order (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function ensureNotificationSchema(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS customer_notifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NULL,
                phone VARCHAR(40) NOT NULL,
                message VARCHAR(500) NOT NULL,
                status ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_customer_notifications_user (user_id),
                KEY idx_customer_notifications_order (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function redirect(string $message, string $type = 'success', ?int $orderId = null, ?string $redirectTo = null): never
    {
        $_SESSION['orders_flash'] = ['type' => $type, 'message' => $message];
        $target = $redirectTo ?: ($orderId ? '/admin/orders/view?id=' . $orderId : '/admin/orders');
        header('Location: ' . $target, true, 303);
        exit;
    }
}
