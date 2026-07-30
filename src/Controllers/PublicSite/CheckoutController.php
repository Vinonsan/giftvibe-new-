<?php

namespace Controllers\PublicSite;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use RuntimeException;
use Services\CartService;
use Throwable;

class CheckoutController
{
    public function show(?string $error = null): void
    {
        if (!Auth::isCustomer()) {
            redirect('/login');
        }

        $cart = new CartService();
        $items = $cart->items();
        if (!$items) {
            redirect('/cart');
        }

        $addresses = Database::instance()->fetchAll(
            'SELECT * FROM customer_addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC',
            ['user_id' => Auth::id()]
        );
        $selectedAddress = $this->selectedAddress($addresses, null);

        View::renderPage('public/pages/checkout', [
            'title' => 'Checkout | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'items' => $items,
            'addresses' => $addresses,
            'totals' => $cart->totals($selectedAddress),
            'error' => $error,
            'meta' => ['description' => 'Select delivery address and payment method for your GiftVibe.lk order.', 'url' => url('/checkout')],
        ]);
    }

    public function store(): void
    {
        if (!Auth::isCustomer()) {
            redirect('/login');
        }
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->show('Your session expired. Try again.');
            return;
        }

        $db = Database::instance()->connection();
        $cart = new CartService();
        $items = $cart->items();
        if (!$items) {
            redirect('/cart');
        }

        $address = Database::instance()->fetch(
            'SELECT * FROM customer_addresses WHERE id = :id AND user_id = :user_id LIMIT 1',
            ['id' => (int) ($_POST['address_id'] ?? 0), 'user_id' => Auth::id()]
        );
        if (!$address) {
            $this->show('Choose a delivery address before placing the order.');
            return;
        }

        $paymentMethod = in_array($_POST['payment_method'] ?? '', ['cod', 'bank_transfer'], true) ? $_POST['payment_method'] : 'cod';
        $receiptPath = null;
        if ($paymentMethod === 'bank_transfer') {
            $receiptPath = $this->storeReceipt();
            if (!$receiptPath) {
                $this->show('Upload a JPG, PNG, WEBP, or PDF payment receipt for bank transfer.');
                return;
            }
        }

        try {
            $cart->validateStock();
            $totals = $cart->totals($address);
            $user = Auth::user();
            $orderNumber = $this->orderNumber();

            $db->beginTransaction();
            $statement = $db->prepare(
                'INSERT INTO orders
                 (order_number, user_id, customer_name, customer_email, customer_phone, recipient_name, recipient_phone,
                  delivery_address_line_1, delivery_address_line_2, delivery_city, delivery_district, delivery_province, delivery_postal_code,
                  delivery_date, subtotal, discount_total, delivery_fee, tax_total, grand_total, payment_status, order_status, customer_notes)
                 VALUES
                 (:order_number, :user_id, :customer_name, :customer_email, :customer_phone, :recipient_name, :recipient_phone,
                  :line1, :line2, :city, :district, :province, :postal_code, :delivery_date, :subtotal, :discount_total, :delivery_fee,
                  :tax_total, :grand_total, "pending", "pending", :customer_notes)'
            );
            $statement->execute([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'customer_name' => $user['name'] ?? $address['recipient_name'],
                'customer_email' => $user['email'] ?? '',
                'customer_phone' => $user['phone'] ?? $address['phone'],
                'recipient_name' => $address['recipient_name'],
                'recipient_phone' => $address['phone'],
                'line1' => $address['address_line_1'],
                'line2' => $address['address_line_2'],
                'city' => $address['city'],
                'district' => $address['district'],
                'province' => $address['province'],
                'postal_code' => $address['postal_code'],
                'delivery_date' => $_POST['delivery_date'] ?: null,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'delivery_fee' => $totals['delivery_fee'],
                'tax_total' => $totals['tax_total'],
                'grand_total' => $totals['grand_total'],
                'customer_notes' => trim((string) ($_POST['customer_notes'] ?? '')),
            ]);
            $orderId = (int) $db->lastInsertId();

            foreach ($items as $item) {
                $unitPrice = (float) $item['unit_price'];
                $costPrice = (float) ($item['cost_price'] ?? 0);
                $qty = (int) $item['quantity'];
                $db->prepare(
                    'INSERT INTO order_items
                     (order_id, product_id, variant_id, product_name, sku, quantity, unit_price, cost_price, total_price, gift_message, custom_options_json)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                )->execute([
                    $orderId,
                    $item['product_id'],
                    $item['variant_id'],
                    $item['product_name'] . ($item['variant_name'] ? ' - ' . $item['variant_name'] : ''),
                    $item['variant_sku'] ?: $item['product_sku'],
                    $qty,
                    $unitPrice,
                    $costPrice,
                    $unitPrice * $qty,
                    $item['gift_message'],
                    json_encode(['color' => $item['color_name'], 'color_hex' => $item['color_hex']], JSON_UNESCAPED_SLASHES),
                ]);

                $db->prepare('UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE id = ?')->execute([$qty, $item['product_id']]);
                if ($item['variant_id']) {
                    $db->prepare('UPDATE product_variants SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE id = ?')->execute([$qty, $item['variant_id']]);
                }
            }

            $db->prepare(
                'INSERT INTO payments (order_id, provider, method, amount, currency, status, receipt_path, raw_response_json)
                 VALUES (?, "manual", ?, ?, "LKR", "pending", ?, ?)'
            )->execute([
                $orderId,
                $paymentMethod === 'cod' ? 'COD' : 'Bank Transfer',
                $totals['grand_total'],
                $receiptPath,
                json_encode(['source' => 'checkout'], JSON_UNESCAPED_SLASHES),
            ]);

            $db->prepare('INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (?, "pending", "Order created from checkout.", ?)')->execute([$orderId, Auth::id()]);
            $db->prepare(
                'INSERT INTO deliveries (order_id, delivery_charge, delivery_status, notes)
                 VALUES (?, ?, "pending", "Delivery record created from checkout.")'
            )->execute([$orderId, $totals['delivery_fee']]);
            $deliveryId = (int) $db->lastInsertId();
            $db->prepare(
                'INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by)
                 VALUES (?, ?, "pending", "Delivery record created from checkout.", ?)'
            )->execute([$deliveryId, $orderId, Auth::id()]);
            $cart->clear();
            $db->commit();

            redirect('/customer/orders/' . $orderNumber);
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $this->show($exception instanceof RuntimeException ? $exception->getMessage() : 'Could not place the order. Try again.');
        }
    }

    private function selectedAddress(array $addresses, ?int $id): ?array
    {
        foreach ($addresses as $address) {
            if (($id && (int) $address['id'] === $id) || (!$id && (int) $address['is_default'] === 1)) {
                return $address;
            }
        }
        return $addresses[0] ?? null;
    }

    private function storeReceipt(): ?string
    {
        if (empty($_FILES['receipt']['tmp_name']) || !is_uploaded_file($_FILES['receipt']['tmp_name'])) {
            return null;
        }
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        $mime = mime_content_type($_FILES['receipt']['tmp_name']);
        if (!isset($allowed[$mime]) || (int) $_FILES['receipt']['size'] > 5 * 1024 * 1024) {
            return null;
        }
        $targetDir = UPLOAD_PATH . '/payments';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $relative = 'storage/uploads/payments/receipt-' . date('YmdHis') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
        $target = BASE_PATH . '/' . $relative;
        if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $target)) {
            return null;
        }
        return $relative;
    }

    private function orderNumber(): string
    {
        return 'GV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function navigation(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => false],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => false],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => false],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => false],
            ['label' => 'Cart', 'url' => url('/cart'), 'active' => true],
            ['label' => 'My Portal', 'url' => url('/customer/dashboard'), 'active' => false],
        ];
    }
}
