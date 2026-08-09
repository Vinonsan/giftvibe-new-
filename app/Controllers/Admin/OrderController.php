<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\OrderPlacementService;
use App\Services\SmsService;
use App\Services\InventoryService;
use PDO;

final class OrderController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $this->ensureNotificationSchema($pdo);
        InventoryService::ensureSchema($pdo);
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

    public function create(): void
    {
        $pdo = Database::connection();
        $this->ensureNotificationSchema($pdo);
        InventoryService::ensureSchema($pdo);
        $this->ensureOrderSchema($pdo);
        $this->ensureComboCostColumn($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storeManualOrder($pdo);
        }

        $catalogProducts = $pdo->query(
            "SELECT p.id, p.name, p.slug, p.sku, p.base_price, p.cost_price,
                COALESCE(pi.image_path, '/assets/images/hero_slide_1.jpg') AS image_path
             FROM products p
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
             WHERE p.status = 'active'
             ORDER BY p.name"
        )->fetchAll(PDO::FETCH_ASSOC);

        $catalogCombos = $pdo->query(
            "SELECT c.id, c.name, c.slug, c.price, c.other_cost,
                COALESCE(ci.image_path, '/assets/images/hero_slide_1.jpg') AS image_path
             FROM combos c
             LEFT JOIN combo_images ci ON ci.combo_id = c.id AND ci.is_primary = 1
             WHERE c.status = 'active'
             ORDER BY c.name"
        )->fetchAll(PDO::FETCH_ASSOC);

        $recentOrderItems = $pdo->query(
            "SELECT oi.product_name, oi.unit_price, oi.cost_price, oi.sku, oi.product_id, oi.quantity,
                o.order_number, o.created_at
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id
             ORDER BY oi.id DESC
             LIMIT 30"
        )->fetchAll(PDO::FETCH_ASSOC);

        $customers = $pdo->query(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.phone,
                    u.address, u.address_line_1, u.city, u.district
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'
             WHERE u.status IN ('active', 'inactive')
             ORDER BY u.first_name, u.last_name, u.id"
        )->fetchAll(PDO::FETCH_ASSOC);

        $addressesByCustomer = [];
        foreach (['customer_addresses', 'user_addresses'] as $addressTable) {
            try {
                $addressRows = $pdo->query(
                    "SELECT * FROM {$addressTable} ORDER BY is_default DESC, id DESC"
                )->fetchAll(PDO::FETCH_ASSOC);
                foreach ($addressRows as $row) {
                    $uid = (int) $row['user_id'];
                    $addressesByCustomer[$uid] ??= [];
                    $addressesByCustomer[$uid][] = $row;
                }
            } catch (\PDOException) {
                // Table may not exist in older installs.
            }
        }

        foreach ($customers as $customer) {
            $uid = (int) $customer['id'];
            if (!empty($addressesByCustomer[$uid])) {
                continue;
            }
            $line1 = trim((string) ($customer['address_line_1'] ?? ''));
            if ($line1 === '') {
                $line1 = trim((string) ($customer['address'] ?? ''));
            }
            if ($line1 === '') {
                continue;
            }
            $addressesByCustomer[$uid][] = [
                'label' => 'Profile address',
                'address_line_1' => $line1,
                'address_line_2' => '',
                'city' => trim((string) ($customer['city'] ?? '')) ?: '—',
                'district' => trim((string) ($customer['district'] ?? '')) ?: '—',
                'is_default' => 1,
            ];
        }

        $bankAccounts = [];
        try {
            $bankAccounts = $pdo->query(
                "SELECT * FROM bank_accounts WHERE status = 'active' ORDER BY sort_order, id"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException) {
            $bankAccounts = [];
        }

        $flash = $_SESSION['orders_flash'] ?? null;
        unset($_SESSION['orders_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Create Order',
            'pageTitle' => 'Create Order',
            'showPageTitle' => false,
            'content' => $this->render('admin/orders/create', [
                'catalogProducts' => $catalogProducts,
                'catalogCombos' => $catalogCombos,
                'recentOrderItems' => $recentOrderItems,
                'customers' => $customers,
                'addressesByCustomer' => $addressesByCustomer,
                'bankAccounts' => $bankAccounts,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    private function storeManualOrder(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $lineItemsJson = (string) ($_POST['line_items'] ?? '[]');
        $lineItems = json_decode($lineItemsJson, true);
        if (!is_array($lineItems)) {
            $this->redirect('Invalid order items.', 'error', null, '/admin/orders/create');
        }

        try {
            $orderItems = OrderPlacementService::resolveFromLineItems($pdo, $lineItems);
            if ($orderItems === []) {
                $this->redirect('Add at least one active product or combo.', 'error', null, '/admin/orders/create');
            }

            $customerMode = (string) ($_POST['customer_mode'] ?? 'existing');
            $customerId = (int) ($_POST['customer_id'] ?? 0);

            if ($customerMode === 'new') {
                $newName = trim((string) ($_POST['new_customer_name'] ?? ''));
                $newPhone = trim((string) ($_POST['new_customer_phone'] ?? ''));
                $newAddress = trim((string) ($_POST['new_customer_address'] ?? ''));

                if ($newName === '' || $newPhone === '' || $newAddress === '') {
                    $this->redirect('New customer name, phone and address are required.', 'error', null, '/admin/orders/create');
                }

                $customerId = $this->createQuickCustomer($pdo, $newName, $newPhone, $newAddress);
                $_POST['delivery_address_line_1'] = $newAddress;
                $_POST['delivery_address_line_2'] = '';
                $_POST['delivery_city'] = trim((string) ($_POST['delivery_city'] ?? '')) ?: '—';
                $_POST['delivery_district'] = trim((string) ($_POST['delivery_district'] ?? '')) ?: '—';
            }

            if ($customerId < 1) {
                $this->redirect('Please select a customer or add a new one.', 'error', null, '/admin/orders/create');
            }

            $customerStmt = $pdo->prepare(
                "SELECT u.id, u.first_name, u.last_name, u.email, u.phone
                 FROM users u
                 INNER JOIN roles r ON r.id = u.role_id AND r.slug = 'customer'
                 WHERE u.id = ? LIMIT 1"
            );
            $customerStmt->execute([$customerId]);
            $customerRow = $customerStmt->fetch(PDO::FETCH_ASSOC);
            if (!$customerRow) {
                $this->redirect('Selected customer was not found.', 'error', null, '/admin/orders/create');
            }

            $customerName = trim(((string) ($customerRow['first_name'] ?? '')) . ' ' . ((string) ($customerRow['last_name'] ?? '')));
            if ($customerName === '') {
                $customerName = trim((string) $_POST['customer_name']);
            }
            $customerEmail = trim((string) ($customerRow['email'] ?? $_POST['customer_email'] ?? ''));
            $customerPhone = trim((string) ($customerRow['phone'] ?? $_POST['customer_phone'] ?? ''));

            foreach (['delivery_address_line_1', 'delivery_city', 'delivery_district'] as $field) {
                if (trim((string) ($_POST[$field] ?? '')) === '') {
                    $this->redirect('Customer delivery address is missing. Add an address for this customer first.', 'error', null, '/admin/orders/create');
                }
            }

            $recipientName = $customerName;
            $recipientPhone = $customerPhone;

            $method = 'cod';
            $autoConfirm = true;
            $markPaid = true;
            $sendSms = isset($_POST['send_sms']);

            $receiptPath = null;
            if (!empty($_FILES['receipt']['name'] ?? '')) {
                try {
                    $receiptPath = OrderPlacementService::storeReceipt($_FILES['receipt'] ?? [], false);
                } catch (\InvalidArgumentException $exception) {
                    $this->redirect($exception->getMessage(), 'error', null, '/admin/orders/create');
                }
            }

            $orderStatus = 'confirmed';
            $paymentStatus = 'paid';

            $orderId = OrderPlacementService::create($pdo, $orderItems, [
                'user_id' => $customerId,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address_line_1' => trim((string) $_POST['delivery_address_line_1']),
                'delivery_address_line_2' => trim((string) ($_POST['delivery_address_line_2'] ?? '')),
                'delivery_city' => trim((string) $_POST['delivery_city']),
                'delivery_district' => trim((string) $_POST['delivery_district']),
                'customer_notes' => trim((string) ($_POST['customer_notes'] ?? '')),
                'admin_notes' => trim((string) ($_POST['admin_notes'] ?? '')) ?: 'Manual order created by admin',
                'payment_method' => $method,
                'bank_account_id' => (int) ($_POST['bank_account_id'] ?? 0),
                'receipt_path' => $receiptPath,
                'order_status' => $orderStatus,
                'payment_status' => $paymentStatus,
                'created_by_admin' => true,
                'notify_admin' => true,
                'verified_by' => (int) ($_SESSION['admin_user']['id'] ?? 0),
            ]);

            $orderStmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? LIMIT 1');
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $this->recordOrderStatus($pdo, $orderId, 'created', 'Manual order created by admin');

            if ($autoConfirm) {
                $days = max(1, min(30, (int) ($_POST['delivery_days'] ?? 3)));
                $deliveryDate = date('Y-m-d', strtotime("+{$days} days"));
                $pdo->prepare('UPDATE orders SET delivery_date = ? WHERE id = ?')->execute([$deliveryDate, $orderId]);
                $this->recordOrderStatus($pdo, $orderId, 'confirmed', 'Auto-confirmed on manual order creation');
                $this->ensureDeliveryRow($pdo, $orderId, $deliveryDate);
            }

            if ($markPaid) {
                $this->recordOrderStatus($pdo, $orderId, 'payment_paid', 'Payment marked paid on manual order creation');
            }

            if ($sendSms && $order !== []) {
                $orderNumber = (string) ($order['order_number'] ?? '');
                $message = $autoConfirm
                    ? "Your GiftVibe order {$orderNumber} has been placed and confirmed. We will update you on delivery."
                    : "Your GiftVibe order {$orderNumber} has been placed. We will confirm after payment verification.";
                $this->notifyCustomer($pdo, $order, $orderId, $message);
            }

            if ($order !== []) {
                SmsService::send((string) getenv('ADMIN_SMS_PHONE'), "Admin placed order {$order['order_number']} for {$order['customer_name']}.");
            }

            $this->redirect('Order created successfully.', 'success', $orderId, '/admin/orders/view?id=' . $orderId);
        } catch (\InvalidArgumentException $exception) {
            $this->redirect($exception->getMessage(), 'error', null, '/admin/orders/create');
        } catch (\Throwable) {
            $this->redirect('The order could not be created. Please try again.', 'error', null, '/admin/orders/create');
        }
    }

    private function createQuickCustomer(PDO $pdo, string $name, string $phone, string $address): int
    {
        $phone = preg_replace('/\s+/', '', $phone) ?? $phone;

        $existing = $pdo->prepare('SELECT u.id FROM users u INNER JOIN roles r ON r.id = u.role_id AND r.slug = ? WHERE u.phone = ? LIMIT 1');
        $existing->execute(['customer', $phone]);
        $existingId = (int) ($existing->fetchColumn() ?: 0);
        if ($existingId > 0) {
            return $existingId;
        }

        $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
        $roleStmt->execute(['customer']);
        $roleId = (int) ($roleStmt->fetchColumn() ?: 0);
        if ($roleId < 1) {
            $pdo->prepare('INSERT INTO roles (name, slug) VALUES (?, ?)')->execute(['Customer', 'customer']);
            $roleId = (int) $pdo->lastInsertId();
        }

        $nameParts = preg_split('/\s+/', trim($name), 2) ?: [trim($name)];
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        $emailBase = preg_replace('/\D+/', '', $phone) ?: (string) time();
        $email = 'walkin+' . $emailBase . '@giftvibe.local';

        $emailCheck = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $emailCheck->execute([$email]);
        if ($emailCheck->fetchColumn()) {
            $email = 'walkin+' . $emailBase . '+' . bin2hex(random_bytes(2)) . '@giftvibe.local';
        }

        $passwordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
        $pdo->prepare(
            'INSERT INTO users (role_id, first_name, last_name, email, phone, address, address_line_1, city, district, avatar, password_hash, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $roleId,
            $firstName,
            $lastName ?: null,
            $email,
            $phone,
            $address,
            $address,
            '—',
            '—',
            'avatar_1',
            $passwordHash,
            'active',
        ]);

        $newId = (int) $pdo->lastInsertId();

        try {
            $pdo->prepare(
                'INSERT INTO user_addresses (user_id, label, address_line_1, city, district, is_default) VALUES (?, ?, ?, ?, ?, 1)'
            )->execute([$newId, 'Primary address', $address, '—', '—']);
        } catch (\PDOException) {
            // Optional table.
        }

        return $newId;
    }

    private function ensureComboCostColumn(PDO $pdo): void
    {
        try {
            $pdo->query('SELECT other_cost FROM combos LIMIT 1');
        } catch (\PDOException) {
            try {
                $pdo->exec('ALTER TABLE combos ADD other_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER price');
            } catch (\PDOException) {
                // Column already exists or table unavailable.
            }
        }
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $this->ensureNotificationSchema($pdo);
        $this->ensureOrderSchema($pdo);
        InventoryService::ensureSchema($pdo);
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

            if (in_array($newOrderStatus, ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'], true)) {
                InventoryService::deductOrderStock($pdo, $orderId);
            }

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
            InventoryService::deductOrderStock($pdo, $orderId);
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
            InventoryService::deductOrderStock($pdo, $orderId);
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
        InventoryService::deductOrderStock($pdo, $orderId);
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
        $userId = (int) ($order['user_id'] ?? 0);
        if ($userId < 1) {
            return;
        }
        $notice = $pdo->prepare('INSERT INTO customer_notifications(user_id, order_id, phone, message, status) VALUES (?,?,?,?,?)');
        $notice->execute([$userId, $orderId, $order['customer_phone'], $message, $sent ? 'sent' : 'queued']);
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
            "CREATE TABLE IF NOT EXISTS admin_notifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(80) NOT NULL,
                title VARCHAR(190) NOT NULL,
                message TEXT NULL,
                entity_type VARCHAR(80) NULL,
                entity_id BIGINT UNSIGNED NULL,
                status ENUM('unread','read') NOT NULL DEFAULT 'unread',
                read_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_admin_notifications_status (status),
                KEY idx_admin_notifications_entity (entity_type, entity_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
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
        header('Location: ' . app_url($target), true, 303);
        exit;
    }
}
