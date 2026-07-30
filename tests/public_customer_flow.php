<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Controllers\PublicSite\CatalogController;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$_SERVER['REQUEST_URI'] = '/shop';
$db = Database::instance()->connection();
$db->beginTransaction();

try {
    $suffix = bin2hex(random_bytes(4));
    $categorySlug = 'test-category-' . $suffix;
    $activeSlug = 'published-gift-' . $suffix;
    $inactiveSlug = 'hidden-gift-' . $suffix;
    $email = 'customer-' . $suffix . '@example.test';

    $db->prepare(
        'INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, "active")'
    )->execute(['Test Category ' . $suffix, $categorySlug, 'Temporary test category']);
    $categoryId = (int) $db->lastInsertId();

    $db->prepare(
        'INSERT INTO products (sku, name, slug, short_description, base_price, sale_price, cost_price, stock_quantity, low_stock_threshold, is_featured, status)
         VALUES (?, ?, ?, ?, 5000, 4500, 3000, 9, 3, 1, "active")'
    )->execute(['PUB-' . $suffix, 'Published Gift ' . $suffix, $activeSlug, 'Visible published product',]);
    $activeProductId = (int) $db->lastInsertId();

    $db->prepare(
        'INSERT INTO products (sku, name, slug, short_description, base_price, stock_quantity, status)
         VALUES (?, ?, ?, ?, 4000, 4, "inactive")'
    )->execute(['HID-' . $suffix, 'Hidden Gift ' . $suffix, $inactiveSlug, 'Should not render']);
    $inactiveProductId = (int) $db->lastInsertId();

    $db->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)')->execute([$activeProductId, $categoryId]);
    $db->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)')->execute([$inactiveProductId, $categoryId]);
    $db->prepare(
        'INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary) VALUES (?, ?, ?, 0, 1)'
    )->execute([$activeProductId, 'public/assets/images/hero_gift_box.jpg', 'Published Gift test image alt text']);

    ob_start();
    $_GET = [];
    (new CatalogController())->shop();
    $shopHtml = ob_get_clean();
    $assert(str_contains($shopHtml, 'Published Gift ' . $suffix), 'shop renders published product');
    $assert(!str_contains($shopHtml, 'Hidden Gift ' . $suffix), 'shop hides unpublished product');

    ob_start();
    (new CatalogController())->product($activeSlug);
    $productHtml = ob_get_clean();
    $assert(str_contains($productHtml, 'application/ld+json'), 'product detail includes structured data');
    $assert(str_contains($productHtml, 'main product image'), 'product detail includes meaningful image alt text');

    $customerRole = $db->query('SELECT id FROM roles WHERE slug = "customer" LIMIT 1')->fetch();
    $db->prepare(
        'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status)
         VALUES (?, "Flow", "Customer", ?, "0771234567", ?, "active")'
    )->execute([(int) $customerRole['id'], $email, password_hash('password123', PASSWORD_DEFAULT)]);
    $customerId = (int) $db->lastInsertId();

    Auth::login([
        'id' => $customerId,
        'first_name' => 'Flow',
        'last_name' => 'Customer',
        'email' => $email,
        'phone' => '0771234567',
        'role_slug' => 'customer',
    ]);
    $assert(Auth::isCustomer(), 'customer session authenticates');

    $db->prepare('INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)')->execute([$customerId, $activeProductId]);
    $wishlistCount = $db->prepare('SELECT COUNT(*) AS total FROM wishlists WHERE user_id = ?');
    $wishlistCount->execute([$customerId]);
    $assert((int) $wishlistCount->fetch()['total'] === 1, 'wishlist create/count works');
    $db->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?')->execute([$customerId, $activeProductId]);

    $db->prepare(
        'INSERT INTO customer_addresses
         (user_id, label, recipient_name, phone, address_line_1, city, district, is_default)
         VALUES (?, "Home", "Flow Customer", "0771234567", "123 Test Road", "Colombo", "Colombo", 1)'
    )->execute([$customerId]);
    $addressId = (int) $db->lastInsertId();
    $db->prepare('UPDATE customer_addresses SET label = "Office" WHERE id = ? AND user_id = ?')->execute([$addressId, $customerId]);
    $address = $db->prepare('SELECT label, is_default FROM customer_addresses WHERE id = ? AND user_id = ?');
    $address->execute([$addressId, $customerId]);
    $row = $address->fetch();
    $assert($row['label'] === 'Office' && (int) $row['is_default'] === 1, 'address create/update/default works');
    $db->prepare('DELETE FROM customer_addresses WHERE id = ? AND user_id = ?')->execute([$addressId, $customerId]);
    $address->execute([$addressId, $customerId]);
    $assert($address->fetch() === false, 'address delete works');

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
