<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Services\OrderPlacementService;
use App\Services\SmsService;
use App\Sms\Messages\AdminNewOrderMessage;
use PDO;
use Throwable;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user']['id'])) {
            $_SESSION['auth_notice'] = 'Please sign in before continuing to checkout.';
            header('Location: ' . app_url('/login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/checkout')), true, 303);
            exit;
        }

        $pdo = Database::connection();
        $this->ensureCheckoutTables($pdo);
        $selection = trim((string) ($_GET['items'] ?? $_POST['items'] ?? ''));
        if ($selection === '') {
            $slug = trim((string) ($_GET['product'] ?? ''));
            $comboSlug = trim((string) ($_GET['combo'] ?? ''));
            $variantId = max(0, (int) ($_GET['variant'] ?? 0));
            $selection = $comboSlug !== '' ? 'c~' . $comboSlug . ':' . max(1, min(10, (int) ($_GET['qty'] ?? 1))) : ($slug !== '' ? 'p~' . $slug . ($variantId ? '@' . $variantId : '') . ':' . max(1, min(10, (int) ($_GET['qty'] ?? 1))) : '');
        }
        $orderItems = OrderPlacementService::resolveFromSelection($pdo, $selection);
        if (!$orderItems) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->placeOrder($pdo, $orderItems, $selection);
            return;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $bankAccounts = $pdo->query("SELECT * FROM bank_accounts WHERE status='active' ORDER BY sort_order,id")->fetchAll(PDO::FETCH_ASSOC);
        $userColumns = $pdo->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
        $optionalUserColumns = array_intersect(['phone_2', 'address', 'address_line_1', 'city', 'district'], $userColumns);
        $optionalSelect = $optionalUserColumns ? ', ' . implode(', ', $optionalUserColumns) : '';
        $userStmt = $pdo->prepare("SELECT first_name, last_name, email, phone{$optionalSelect} FROM users WHERE id = ?");
        $userStmt->execute([(int) $_SESSION['user']['id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $savedAddresses = $pdo->prepare('SELECT * FROM user_addresses WHERE user_id=? ORDER BY is_default DESC,id DESC');
        $savedAddresses->execute([(int) $_SESSION['user']['id']]);
        $savedAddresses = $savedAddresses->fetchAll(PDO::FETCH_ASSOC);
        $flash = $_SESSION['checkout_error'] ?? null;
        unset($_SESSION['checkout_error']);

        $content = $this->render('public/checkout/index', compact('orderItems', 'selection', 'bankAccounts', 'user', 'savedAddresses', 'flash') + [
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
        $this->view('layouts/public-layout', [
            'title' => 'Secure Checkout',
            'robots' => 'noindex, nofollow',
            'canonicalPath' => '/checkout',
            'content' => $content,
        ]);
    }

    public function success(): void
    {
        $orderNumber = (string) ($_SESSION['placed_order'] ?? '');
        unset($_SESSION['placed_order']);
        if ($orderNumber === '') {
            header('Location: ' . app_url('/shop'), true, 303);
            exit;
        }
        $pdo = Database::connection();
        $general = $pdo->query("SELECT google_review_url FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $googleReviewUrl = $general['google_review_url'] ?? '';

        $content = $this->render('public/checkout/success', compact('orderNumber', 'googleReviewUrl'));
        $this->view('layouts/public-layout', ['title' => 'Order Received', 'robots' => 'noindex, nofollow', 'content' => $content]);
    }

    private function placeOrder(PDO $pdo, array $orderItems, string $selection): never
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $fields = ['customer_name', 'customer_email', 'customer_phone'];
        foreach ($fields as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $this->checkoutError('Please complete all required customer and delivery fields.', $selection);
            }
        }

        $recipientType = (string) ($_POST['recipient_type'] ?? 'self');
        $recipientName = trim((string) ($_POST['recipient_name'] ?? ''));
        $recipientPhone = trim((string) ($_POST['recipient_phone'] ?? ''));
        if ($recipientType !== 'gift') {
            $recipientName = trim((string) $_POST['customer_name']);
            $recipientPhone = trim((string) $_POST['customer_phone']);
        } elseif ($recipientName === '' || $recipientPhone === '') {
            $this->checkoutError('Enter the gift recipient name and phone number.', $selection);
        }

        $addressId = (int) ($_POST['address_id'] ?? 0);
        $addressLine1 = trim((string) ($_POST['delivery_address_line_1'] ?? ''));
        $addressLine2 = trim((string) ($_POST['delivery_address_line_2'] ?? ''));
        $city = trim((string) ($_POST['delivery_city'] ?? ''));
        $district = trim((string) ($_POST['delivery_district'] ?? ''));
        if ($addressId > 0) {
            $addressStmt = $pdo->prepare('SELECT * FROM user_addresses WHERE id=? AND user_id=? LIMIT 1');
            $addressStmt->execute([$addressId,(int)$_SESSION['user']['id']]);
            $address = $addressStmt->fetch(PDO::FETCH_ASSOC);
            if (!$address) $this->checkoutError('Select a valid saved delivery address.', $selection);
            $addressLine1=(string)$address['address_line_1'];$addressLine2=(string)($address['address_line_2']??'');$city=(string)$address['city'];$district=(string)$address['district'];
        }
        if ($addressLine1 === '' || $city === '' || $district === '') $this->checkoutError('Complete the delivery address, city, and district.', $selection);
        if ($addressId === 0 && isset($_POST['save_address'])) {
            $saveAddress=$pdo->prepare('INSERT INTO user_addresses(user_id,label,address_line_1,address_line_2,city,district,is_default) VALUES(?,?,?,?,?,?,?)');
            $saveAddress->execute([(int)$_SESSION['user']['id'],trim((string)($_POST['address_label']??'Delivery address')),$addressLine1,$addressLine2,$city,$district,0]);
        }

        $method = (string) ($_POST['payment_method'] ?? 'cod');
        if (!in_array($method, ['cod', 'bank_deposit'], true)) {
            $this->checkoutError('Select a valid payment method.', $selection);
        }
        $orderTotal = round((float) array_sum(array_column($orderItems, 'line_total')), 2);
        $paymentAmount = $method === 'cod'
            ? round((float) ($_POST['payment_amount'] ?? 0), 2)
            : $orderTotal;
        if ($paymentAmount < 0 || $paymentAmount > $orderTotal) {
            $this->checkoutError('Enter a valid COD amount within the order total.', $selection);
        }
        $bankId = (int) ($_POST['bank_account_id'] ?? 0);
        if ($bankId > 0) {
            $bankStmt = $pdo->prepare("SELECT id,bank_name,account_name,account_number,branch FROM bank_accounts WHERE id=? AND status='active' LIMIT 1");
            $bankStmt->execute([$bankId]);
            if (!$bankStmt->fetch(PDO::FETCH_ASSOC)) {
                $this->checkoutError('Select a valid deposit account.', $selection);
            }
        }
        try {
            $receipt = OrderPlacementService::storeReceipt($_FILES['receipt'] ?? [], $method === 'bank_deposit');
            $orderId = OrderPlacementService::create($pdo, $orderItems, [
                'user_id' => (int) $_SESSION['user']['id'],
                'customer_name' => trim((string) $_POST['customer_name']),
                'customer_email' => trim((string) $_POST['customer_email']),
                'customer_phone' => trim((string) $_POST['customer_phone']),
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address_line_1' => $addressLine1,
                'delivery_address_line_2' => $addressLine2,
                'delivery_city' => $city,
                'delivery_district' => $district,
                'customer_notes' => '',
                'payment_method' => $method,
                'payment_amount' => $paymentAmount,
                'bank_account_id' => $bankId,
                'receipt_path' => $receipt,
                'order_status' => 'pending',
                'payment_status' => 'pending',
                'created_by_admin' => false,
                'notify_admin' => true,
            ]);

            $orderNumberStmt = $pdo->prepare('SELECT order_number, customer_name, customer_phone, grand_total FROM orders WHERE id = ? LIMIT 1');
            $orderNumberStmt->execute([$orderId]);
            $placedOrder = $orderNumberStmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $orderNumber = (string) ($placedOrder['order_number'] ?? '');
            $_SESSION['placed_order'] = $orderNumber;

            SmsService::send(
                (string) getenv('ADMIN_SMS_PHONE'),
                AdminNewOrderMessage::build(
                    (string) ($placedOrder['customer_name'] ?? ''),
                    $orderItems,
                    $method,
                    (float) ($placedOrder['grand_total'] ?? 0)
                )
            );
            header('Location: ' . app_url('/checkout/success'), true, 303);
            exit;
        } catch (\InvalidArgumentException $exception) {
            $this->checkoutError($exception->getMessage(), $selection);
        } catch (Throwable $exception) {
            $this->checkoutError('The order could not be placed. Please try again.', $selection);
        }
    }

    private function checkoutError(string $message, string $selection): never
    {
        $_SESSION['checkout_error'] = $message;
        header('Location: ' . app_url('/checkout?items=' . rawurlencode($selection)), true, 303);
        exit;
    }

    private function ensureCheckoutTables(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS customer_notifications (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,order_id BIGINT UNSIGNED NULL,phone VARCHAR(40) NOT NULL,message VARCHAR(500) NOT NULL,status ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,KEY idx_customer_notifications_user(user_id),KEY idx_customer_notifications_order(order_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $pdo->exec("CREATE TABLE IF NOT EXISTS bank_accounts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,bank_name VARCHAR(120) NOT NULL,account_name VARCHAR(190) NOT NULL,account_number VARCHAR(100) NOT NULL,branch VARCHAR(120) NULL,sort_order INT UNSIGNED NOT NULL DEFAULT 0,status ENUM('active','inactive') NOT NULL DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_addresses (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,label VARCHAR(80) NOT NULL DEFAULT 'Delivery address',address_line_1 VARCHAR(255) NOT NULL,address_line_2 VARCHAR(255) NULL,city VARCHAR(120) NOT NULL,district VARCHAR(120) NOT NULL,is_default TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,KEY idx_user_addresses_user(user_id),CONSTRAINT fk_user_addresses_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function submitReview(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        $customer = $_SESSION['user'] ?? null;
        if (!$customer) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'You must be signed in to submit a review.']);
            exit;
        }

        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
        $comments = trim((string) ($_POST['comments'] ?? ''));

        if ($comments === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Feedback comment is required.']);
            exit;
        }

        $name = trim((string) (($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')));
        $email = $customer['email'] ?? '';
        $avatarKey = $customer['avatar'] ?? 'avatar_1';
        $avatarPaths = [
            'avatar_1' => '/assets/images/avatars/avatar-1.svg',
            'avatar_2' => '/assets/images/avatars/avatar-2.svg',
            'avatar_3' => '/assets/images/avatars/avatar-3.svg',
            'avatar_4' => '/assets/images/avatars/avatar-4.svg',
            'avatar_5' => '/assets/images/avatars/avatar-5.svg',
            'avatar_6' => '/assets/images/avatars/avatar-6.svg'
        ];
        $avatarPath = $avatarPaths[$avatarKey] ?? '/assets/images/avatars/avatar-1.svg';

        $pdo = Database::connection();
        $stmt = $pdo->prepare("INSERT INTO testimonials (reviewer_name, reviewer_email, reviewer_role, avatar_path, title, review_text, rating, source, status, sort_order) VALUES (?, ?, 'Customer', ?, 'Order Feedback', ?, ?, 'public', 'pending', 0)");
        $stmt->execute([$name, $email, $avatarPath, $comments, $rating]);

        // Fetch Google review URL to redirect
        $general = $pdo->query("SELECT google_review_url FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $googleReviewUrl = $general['google_review_url'] ?? '';

        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'google_review_url' => $googleReviewUrl
        ]);
        exit;
    }
}
