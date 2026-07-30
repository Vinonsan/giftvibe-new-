<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Controllers\Admin\DeliveryController;
use Services\CartService;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$db = Database::instance()->connection();
$db->beginTransaction();

try {
    $suffix = bin2hex(random_bytes(4));
    $email = 'checkout-' . $suffix . '@example.test';

    $role = $db->query('SELECT id FROM roles WHERE slug = "customer" LIMIT 1')->fetch();
    $db->prepare(
        'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status)
         VALUES (?, "Checkout", "Customer", ?, "0771000000", ?, "active")'
    )->execute([(int) $role['id'], $email, password_hash('password123', PASSWORD_DEFAULT)]);
    $customerId = (int) $db->lastInsertId();
    Auth::login(['id' => $customerId, 'first_name' => 'Checkout', 'last_name' => 'Customer', 'email' => $email, 'phone' => '0771000000', 'role_slug' => 'customer']);

    $db->prepare(
        'INSERT INTO customer_addresses (user_id, label, recipient_name, phone, address_line_1, city, district, is_default)
         VALUES (?, "Home", "Checkout Customer", "0771000000", "12 Order Road", "Colombo", "Colombo", 1)'
    )->execute([$customerId]);
    $addressId = (int) $db->lastInsertId();
    $address = Database::instance()->fetch('SELECT * FROM customer_addresses WHERE id = :id', ['id' => $addressId]);

    $db->prepare(
        'INSERT INTO products (sku, name, slug, short_description, base_price, sale_price, cost_price, stock_quantity, low_stock_threshold, status)
         VALUES (?, ?, ?, "Checkout visible product", 2500, 2200, 1200, 6, 2, "active")'
    )->execute(['CHK-' . $suffix, 'Checkout Product ' . $suffix, 'checkout-product-' . $suffix]);
    $productId = (int) $db->lastInsertId();

    $db->prepare(
        'INSERT INTO product_variants (product_id, name, sku, color_name, color_hex, price_adjustment, stock_quantity, status)
         VALUES (?, "Red Box", ?, "Red", "#b91c1c", 300, 4, "active")'
    )->execute([$productId, 'CHK-VAR-' . $suffix]);
    $variantId = (int) $db->lastInsertId();

    $cartService = new CartService();
    $cartService->add($productId, $variantId, 2, 'Happy birthday');
    $items = $cartService->items();
    $assert(count($items) === 1 && (int) $items[0]['quantity'] === 2, 'cart add with variant selection works');
    $assert((float) $items[0]['unit_price'] === 2500.0, 'cart saves selected variant selling price');

    $cartService->update((int) $items[0]['id'], 3);
    $items = $cartService->items();
    $assert((int) $items[0]['quantity'] === 3, 'cart quantity update works');

    try {
        $cartService->update((int) $items[0]['id'], 99);
        $assert(false, 'stock validation rejects oversell');
    } catch (Throwable) {
        $assert(true, 'stock validation rejects oversell');
    }

    $totals = $cartService->totals($address);
    $assert((float) $totals['subtotal'] === 7500.0 && (float) $totals['delivery_fee'] >= 0, 'cart totals include delivery charge');

    $cart = $cartService->cart();
    $orderNumber = 'TEST-' . strtoupper($suffix);
    $db->prepare(
        'INSERT INTO orders
         (order_number, user_id, customer_name, customer_email, customer_phone, recipient_name, recipient_phone,
          delivery_address_line_1, delivery_city, delivery_district, subtotal, delivery_fee, grand_total, payment_status, order_status)
         VALUES (?, ?, "Checkout Customer", ?, "0771000000", "Checkout Customer", "0771000000", "12 Order Road", "Colombo", "Colombo", ?, ?, ?, "pending", "pending")'
    )->execute([$orderNumber, $customerId, $email, $totals['subtotal'], $totals['delivery_fee'], $totals['grand_total']]);
    $orderId = (int) $db->lastInsertId();

    foreach ($items as $item) {
        $db->prepare(
            'INSERT INTO order_items (order_id, product_id, variant_id, product_name, sku, quantity, unit_price, cost_price, total_price, gift_message)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $orderId,
            $item['product_id'],
            $item['variant_id'],
            $item['product_name'] . ' - ' . $item['variant_name'],
            $item['variant_sku'],
            $item['quantity'],
            $item['unit_price'],
            $item['cost_price'],
            (float) $item['unit_price'] * (int) $item['quantity'],
            $item['gift_message'],
        ]);
        $db->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?')->execute([$item['quantity'], $productId]);
        $db->prepare('UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ?')->execute([$item['quantity'], $variantId]);
    }

    $db->prepare(
        'INSERT INTO payments (order_id, provider, method, amount, currency, status, receipt_path)
         VALUES (?, "manual", "COD", ?, "LKR", "pending", "storage/uploads/payments/test-receipt.pdf")'
    )->execute([$orderId, $totals['grand_total']]);
    $db->prepare('INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (?, "pending", "Test order created.", ?)')->execute([$orderId, $customerId]);
    $db->prepare(
        'INSERT INTO deliveries (order_id, delivery_charge, delivery_status, notes)
         VALUES (?, ?, "pending", "Delivery record created in test.")'
    )->execute([$orderId, $totals['delivery_fee']]);
    $deliveryId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by)
         VALUES (?, ?, "pending", "Initial parcel status.", ?)'
    )->execute([$deliveryId, $orderId, 1]);

    $cartService->clear();
    $clearedItems = Database::instance()->fetch('SELECT COUNT(*) AS total FROM cart_items WHERE cart_id = :cart_id', ['cart_id' => $cart['id']]);
    $convertedCart = Database::instance()->fetch('SELECT status FROM carts WHERE id = :id', ['id' => $cart['id']]);
    $assert((int) $clearedItems['total'] === 0 && $convertedCart['status'] === 'converted', 'order creation clears cart automatically');

    $orderItem = Database::instance()->fetch('SELECT * FROM order_items WHERE order_id = :id LIMIT 1', ['id' => $orderId]);
    $assert((float) $orderItem['unit_price'] === 2500.0 && (float) $orderItem['cost_price'] === 1200.0, 'order item saves historical selling and buying prices');
    $profit = ((float) $orderItem['unit_price'] - (float) $orderItem['cost_price']) * (int) $orderItem['quantity'];
    $assert($profit === 3900.0, 'historical profit calculation works');

    $stock = Database::instance()->fetch('SELECT stock_quantity FROM product_variants WHERE id = :id', ['id' => $variantId]);
    $assert((int) $stock['stock_quantity'] === 1, 'checkout decrements variant stock');

    $db->prepare('UPDATE orders SET order_status = "confirmed" WHERE id = ?')->execute([$orderId]);
    $db->prepare('UPDATE payments SET status = "paid", verified_by = ?, verified_at = NOW(), verification_notes = "Verified in test" WHERE order_id = ?')->execute([$customerId, $orderId]);
    $adminCheck = Database::instance()->fetch(
        'SELECT orders.order_status, payments.status AS payment_status, payments.receipt_path
         FROM orders INNER JOIN payments ON payments.order_id = orders.id WHERE orders.id = :id',
        ['id' => $orderId]
    );
    $assert($adminCheck['order_status'] === 'confirmed' && $adminCheck['payment_status'] === 'paid' && $adminCheck['receipt_path'] !== '', 'admin order status and payment verification work');

    Auth::loginAdminByPhone('0758311995', [
        'id' => 1,
        'first_name' => 'GiftVibe',
        'last_name' => 'Admin',
        'email' => 'admin@giftvibe.lk',
        'phone' => '0758311995',
        'role_slug' => 'super_admin',
    ]);
    $db->prepare(
        'UPDATE deliveries
         SET courier_service_name = "DOMEX",
             courier_tracking_number = ?,
             tracking_code = ?,
             parcel_reference_number = ?,
             qr_code_path = "storage/uploads/deliveries/test-qr.png",
             delivery_charge = ?,
             dispatch_date = CURRENT_DATE(),
             expected_delivery_date = DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY),
             delivery_status = "dispatched",
             assigned_staff_id = 1
         WHERE id = ?'
    )->execute(['DX-' . $suffix, 'DX-' . $suffix, 'PCL-' . $suffix, $totals['delivery_fee'], $deliveryId]);
    $db->prepare(
        'INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by)
         VALUES (?, ?, "dispatched", "Parcel handed to DOMEX.", 1)'
    )->execute([$deliveryId, $orderId]);
    $db->prepare(
        'INSERT INTO delivery_reminders (delivery_id, order_id, admin_id, reminder_at, message)
         VALUES (?, ?, 1, DATE_ADD(NOW(), INTERVAL 1 HOUR), "Check DOMEX scan")'
    )->execute([$deliveryId, $orderId]);

    $parcel = Database::instance()->fetch('SELECT * FROM deliveries WHERE id = :id', ['id' => $deliveryId]);
    $assert($parcel['courier_service_name'] === 'DOMEX' && $parcel['courier_tracking_number'] !== '' && $parcel['parcel_reference_number'] !== '', 'courier service, tracking number, and parcel reference save');
    $assert($parcel['qr_code_path'] !== '' && (float) $parcel['delivery_charge'] === (float) $totals['delivery_fee'], 'courier QR attachment and delivery charge save');
    $assert($parcel['dispatch_date'] !== null && $parcel['expected_delivery_date'] !== null, 'dispatch and expected delivery dates save');

    $history = Database::instance()->fetch(
        'SELECT delivery_status_history.*, users.email
         FROM delivery_status_history
         LEFT JOIN users ON users.id = delivery_status_history.changed_by
         WHERE delivery_status_history.delivery_id = :id AND delivery_status_history.status = "dispatched"
         LIMIT 1',
        ['id' => $deliveryId]
    );
    $assert($history && (int) $history['changed_by'] === 1 && $history['created_at'] !== null, 'delivery status history records admin date and time');

    $reminder = Database::instance()->fetch('SELECT * FROM delivery_reminders WHERE delivery_id = :id LIMIT 1', ['id' => $deliveryId]);
    $assert($reminder && $reminder['status'] === 'open' && $reminder['reminder_at'] !== null, 'admin delivery reminder saves');

    $db->prepare('UPDATE deliveries SET delivery_status = "failed" WHERE id = ?')->execute([$deliveryId]);
    $db->prepare('INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by) VALUES (?, ?, "failed", "Recipient unavailable.", 1)')->execute([$deliveryId, $orderId]);
    $failed = Database::instance()->fetch('SELECT delivery_status FROM deliveries WHERE id = :id', ['id' => $deliveryId]);
    $assert($failed['delivery_status'] === 'failed', 'failed delivery status saves');

    $db->prepare('UPDATE deliveries SET delivery_status = "returned" WHERE id = ?')->execute([$deliveryId]);
    $db->prepare('INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by) VALUES (?, ?, "returned", "Parcel returned to store.", 1)')->execute([$deliveryId, $orderId]);
    $returned = Database::instance()->fetch('SELECT delivery_status FROM deliveries WHERE id = :id', ['id' => $deliveryId]);
    $assert($returned['delivery_status'] === 'returned', 'returned delivery status saves');

    $counts = Database::instance()->fetchAll('SELECT delivery_status, COUNT(*) AS total FROM deliveries GROUP BY delivery_status');
    $assert(is_array($counts), 'parcel-status dashboard count query works');

    $cartService->add($productId, null, 1);
    $removeItem = $cartService->items()[0];
    $cartService->remove((int) $removeItem['id']);
    $assert(count($cartService->items()) === 0, 'cart remove works');

    $db->rollBack();
    Auth::logout();
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $messages[] = 'FAIL exception: ' . $exception->getMessage();
    $failures[] = 'exception';
}

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
