<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SmsService;
use PDO;
use Throwable;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user']['id'])) {
            $_SESSION['auth_notice'] = 'Please sign in before continuing to checkout.';
            header('Location: /login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/checkout'), true, 303);
            exit;
        }

        $pdo = Database::connection();
        $this->ensureCheckoutTables($pdo);
        $selection = trim((string) ($_GET['items'] ?? $_POST['items'] ?? ''));
        if ($selection === '') {
            $slug = trim((string) ($_GET['product'] ?? ''));
            $comboSlug = trim((string) ($_GET['combo'] ?? ''));
            $selection = $comboSlug !== '' ? 'c~' . $comboSlug . ':' . max(1, min(10, (int) ($_GET['qty'] ?? 1))) : ($slug !== '' ? 'p~' . $slug . ':' . max(1, min(10, (int) ($_GET['qty'] ?? 1))) : '');
        }
        $orderItems = $this->products($pdo, $selection);
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
            header('Location: /shop', true, 303);
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

        $total = round(array_sum(array_column($orderItems, 'line_total')), 2);
        $method = (string) ($_POST['payment_method'] ?? 'cod');
        if (!in_array($method, ['cod', 'bank_deposit'], true)) {
            $this->checkoutError('Select a valid payment method.', $selection);
        }
        $option = $method === 'cod' ? 'advance' : 'full';
        $amount = $method === 'cod' ? min(500.00, $total) : $total;
        $bankId = (int) ($_POST['bank_account_id'] ?? 0);
        $bankAccount = null;
        if ($bankId > 0) {
            $bankStmt = $pdo->prepare("SELECT id,bank_name,account_name,account_number,branch FROM bank_accounts WHERE id=? AND status='active' LIMIT 1");
            $bankStmt->execute([$bankId]);
            $bankAccount = $bankStmt->fetch(PDO::FETCH_ASSOC);
            if (!$bankAccount) $this->checkoutError('Select a valid deposit account.', $selection);
        }
        $receipt = $this->storeReceipt($_FILES['receipt'] ?? [], $selection);

        try {
            $pdo->beginTransaction();
            $orderNumber = 'GV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $order = $pdo->prepare('INSERT INTO orders (order_number,user_id,customer_name,customer_email,customer_phone,recipient_name,recipient_phone,delivery_address_line_1,delivery_address_line_2,delivery_city,delivery_district,delivery_postal_code,subtotal,grand_total,payment_status,order_status,customer_notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $order->execute([$orderNumber, (int) $_SESSION['user']['id'], trim($_POST['customer_name']), trim($_POST['customer_email']), trim($_POST['customer_phone']), $recipientName, $recipientPhone, $addressLine1, $addressLine2, $city, $district, '', $total, $total, 'pending', 'pending', trim((string) ($_POST['customer_notes'] ?? ''))]);
            $orderId = (int) $pdo->lastInsertId();
            $item = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,sku,quantity,unit_price,total_price) VALUES (?,?,?,?,?,?,?)');
            foreach ($orderItems as $product) {
                $item->execute([$orderId, $product['product_id'], $product['name'], $product['sku'], $product['quantity'], $product['base_price'], $product['line_total']]);
            }
            $payment = $pdo->prepare('INSERT INTO payments (order_id,provider,method,transaction_reference,amount,status,receipt_path,raw_response_json) VALUES (?,?,?,?,?,?,?,?)');
            $payment->execute([$orderId, $method === 'cod' ? 'cash_on_delivery' : 'bank', $method, $orderNumber . '-PAY', $amount, 'pending', $receipt, json_encode(['payment_option' => $option, 'balance_due' => max(0, $total - $amount), 'bank_account_id'=>$bankId ?: null, 'bank_name'=>$bankAccount['bank_name'] ?? null, 'account_number'=>$bankAccount['account_number'] ?? null])]);
            $notice = $pdo->prepare("INSERT INTO admin_notifications (type,title,message,entity_type,entity_id,status) VALUES ('new_order','New order awaiting verification',?,'order',?,'unread')");
            $notice->execute(["{$orderNumber}: {$method}, LKR " . number_format($amount, 2) . ' receipt uploaded.', $orderId]);
            $pdo->commit();

            SmsService::send((string) getenv('ADMIN_SMS_PHONE'), "New GiftVibe order {$orderNumber} requires verification.");
            $_SESSION['placed_order'] = $orderNumber;
            header('Location: /checkout/success', true, 303);
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->checkoutError('The order could not be placed. Please try again.', $selection);
        }
    }

    private function storeReceipt(array $file, string $selection): string
    {
        $errCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($errCode !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE     => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder in PHP configuration.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
            ];
            $detail = $uploadErrors[$errCode] ?? 'Unknown upload error code: ' . $errCode;
            $this->checkoutError('Upload your bank payment receipt failed: ' . $detail, $selection);
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size < 1 || $size > 5 * 1024 * 1024) {
            $this->checkoutError('Receipt must be a non-empty file no larger than 5 MB. Uploaded size: ' . number_format($size / 1024, 1) . ' KB', $selection);
        }

        $temporaryPath = (string) ($file['tmp_name'] ?? '');
        $mime = $this->receiptMimeType($file);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        if (!isset($extensions[$mime])) {
            $this->checkoutError('Receipt must be JPG, PNG, WebP, or PDF. Detected type: ' . htmlspecialchars($mime), $selection);
        }

        $directory = BASE_PATH . '/public/assets/uploads/receipts';
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        if (!is_writable($directory)) {
            $this->checkoutError('Upload directory is not writable by web server: ' . $directory, $selection);
        }

        $name = 'receipt_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $name)) {
            $this->checkoutError('Failed to move uploaded receipt to destination directory.', $selection);
        }
        return '/assets/uploads/receipts/' . $name;
    }

    private function receiptMimeType(array $file): string
    {
        $path = (string) ($file['tmp_name'] ?? '');
        if ($path === '' || !is_file($path)) return '';

        if (class_exists(\finfo::class) && defined('FILEINFO_MIME_TYPE')) {
            $detector = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $detector->file($path);
            if (is_string($mime) && $mime !== '') return $mime;
        }

        $image = @getimagesize($path);
        if (is_array($image) && isset($image['mime']) && in_array($image['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return (string) $image['mime'];
        }

        $handle = @fopen($path, 'rb');
        if ($handle !== false) {
            $signature = (string) fread($handle, 5);
            fclose($handle);
            if ($signature === '%PDF-') return 'application/pdf';
        }

        return (string) ($file['type'] ?? '');
    }

    private function checkoutError(string $message, string $selection): never
    {
        $_SESSION['checkout_error'] = $message;
        header('Location: /checkout?items=' . rawurlencode($selection), true, 303);
        exit;
    }

    private function products(PDO $pdo, string $selection): array
    {
        $requested = [];
        foreach (array_slice(explode(',', $selection), 0, 20) as $entry) {
            [$typedSlug, $quantity] = array_pad(explode(':', $entry, 2), 2, '1');
            [$type, $slug] = str_contains($typedSlug, '~') ? array_pad(explode('~', $typedSlug, 2), 2, '') : ['p', $typedSlug];
            if (in_array($type, ['p','c'], true) && preg_match('/^[a-z0-9-]+$/', $slug)) $requested[$type . '~' . $slug] = max(1, min(10, (int) $quantity));
        }
        if (!$requested) return [];
        $products = [];
        $productStmt = $pdo->prepare("SELECT id,name,slug,sku,base_price,stock_quantity FROM products WHERE slug=? AND status='active' LIMIT 1");
        $comboStmt = $pdo->prepare("SELECT id,name,slug,CONCAT('COMBO-',id) sku,price base_price,1 stock_quantity FROM combos WHERE slug=? AND status='active' LIMIT 1");
        foreach ($requested as $typedSlug => $quantity) {
            [$type, $slug] = explode('~', $typedSlug, 2);
            $stmt = $type === 'c' ? $comboStmt : $productStmt;
            $stmt->execute([$slug]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) continue;
            $product['product_id'] = $type === 'c' ? null : (int) $product['id'];
            $product['source_type'] = $type === 'c' ? 'combo' : 'product';
            $product['quantity'] = $quantity;
            $product['line_total'] = round((float) $product['base_price'] * $product['quantity'], 2);
            $products[] = $product;
        }
        return $products;
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
