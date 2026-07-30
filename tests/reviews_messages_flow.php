<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Controllers\PublicSite\CatalogController;
use Services\AdminNotificationService;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$_SERVER['REQUEST_URI'] = '/product/test-review-product';
$db = Database::instance()->connection();
$db->beginTransaction();

try {
    $suffix = bin2hex(random_bytes(4));
    $email = 'reviews-' . $suffix . '@example.test';
    $slug = 'review-product-' . $suffix;

    $role = $db->query('SELECT id FROM roles WHERE slug = "customer" LIMIT 1')->fetch();
    $db->prepare(
        'INSERT INTO users (role_id, first_name, last_name, email, phone, password_hash, status)
         VALUES (?, "Review", "Customer", ?, ?, ?, "active")'
    )->execute([(int) $role['id'], $email, '0772' . random_int(100000, 999999), password_hash('password123', PASSWORD_DEFAULT)]);
    $customerId = (int) $db->lastInsertId();
    Auth::login(['id' => $customerId, 'first_name' => 'Review', 'last_name' => 'Customer', 'email' => $email, 'role_slug' => 'customer']);

    $db->prepare(
        'INSERT INTO products (sku, name, slug, short_description, base_price, cost_price, stock_quantity, status)
         VALUES (?, ?, ?, "Review flow product", 3500, 1800, 10, "active")'
    )->execute(['REV-' . $suffix, 'Review Product ' . $suffix, $slug]);
    $productId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO product_images (product_id, image_path, alt_text, is_primary)
         VALUES (?, "public/assets/images/hero_gift_box.jpg", "Review product image", 1)'
    )->execute([$productId]);

    $db->prepare(
        'INSERT INTO reviews (product_id, user_id, rating, title, body, status)
         VALUES (?, ?, 5, "Approved sparkle", "Approved review body", "approved")'
    )->execute([$productId, $customerId]);
    $approvedReviewId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO reviews (product_id, user_id, rating, title, body, status)
         VALUES (?, ?, 1, "Pending hidden", "Pending review body", "pending")'
    )->execute([$productId, $customerId]);
    $pendingReviewId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO reviews (product_id, user_id, rating, title, body, status)
         VALUES (?, ?, 2, "Rejected hidden", "Rejected review body", "rejected")'
    )->execute([$productId, $customerId]);
    $rejectedReviewId = (int) $db->lastInsertId();

    $rating = Database::instance()->fetch(
        'SELECT COALESCE(AVG(rating), 0) AS average_rating, COUNT(*) AS reviews_count
         FROM reviews WHERE product_id = :product_id AND status = "approved"',
        ['product_id' => $productId]
    );
    $assert((float) $rating['average_rating'] === 5.0 && (int) $rating['reviews_count'] === 1, 'average rating uses approved reviews only');

    $db->prepare('UPDATE reviews SET status = "approved" WHERE id = ?')->execute([$pendingReviewId]);
    $db->prepare('UPDATE reviews SET status = "rejected" WHERE id = ?')->execute([$approvedReviewId]);
    $moderation = Database::instance()->fetchAll(
        'SELECT status, COUNT(*) AS total FROM reviews WHERE id IN (?, ?, ?) GROUP BY status',
        [$approvedReviewId, $pendingReviewId, $rejectedReviewId]
    );
    $statusTotals = array_column($moderation, 'total', 'status');
    $assert((int) ($statusTotals['approved'] ?? 0) === 1 && (int) ($statusTotals['rejected'] ?? 0) === 2, 'admin review approval and rejection statuses save');

    ob_start();
    (new CatalogController())->product($slug);
    $productHtml = ob_get_clean();
    $assert(str_contains($productHtml, 'Pending review body'), 'public page renders newly approved review');
    $assert(!str_contains($productHtml, 'Approved review body') && !str_contains($productHtml, 'Rejected review body'), 'public page hides rejected reviews');
    $assert(str_contains($productHtml, 'aggregateRating'), 'product structured data includes approved aggregate rating');

    $db->prepare(
        'INSERT INTO contact_messages (name, email, phone, subject, message)
         VALUES ("Contact Test", ?, "0771234567", "Support", "Please help with my gift.")'
    )->execute(['contact-' . $suffix . '@example.test']);
    $contactId = (int) $db->lastInsertId();
    AdminNotificationService::create('contact', 'New contact message', 'Contact Test sent a message.', 'contact', $contactId);
    $db->prepare(
        'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
         VALUES ("contact", ?, "admin", 1, "GiftVibe Admin", "We can help.")'
    )->execute([$contactId]);
    $db->prepare('UPDATE contact_messages SET status = "replied" WHERE id = ?')->execute([$contactId]);
    $contact = Database::instance()->fetch(
        'SELECT contact_messages.status, COUNT(message_replies.id) AS replies
         FROM contact_messages
         LEFT JOIN message_replies ON message_replies.entity_type = "contact" AND message_replies.entity_id = contact_messages.id
         WHERE contact_messages.id = :id
         GROUP BY contact_messages.id',
        ['id' => $contactId]
    );
    $assert($contact['status'] === 'replied' && (int) $contact['replies'] === 1, 'contact message status and admin chat reply save');

    $requestNumber = 'CGR-TEST-' . strtoupper($suffix);
    $db->prepare(
        'INSERT INTO custom_gift_requests
         (user_id, request_number, customer_name, customer_email, customer_phone, occasion, recipient, budget, delivery_date, message)
         VALUES (?, ?, "Review Customer", ?, "0777654321", "Birthday", "Ammachi", 9500, CURRENT_DATE(), "Custom hamper with reference image.")'
    )->execute([$customerId, $requestNumber, $email]);
    $requestId = (int) $db->lastInsertId();
    $db->prepare(
        'INSERT INTO custom_gift_request_images (request_id, image_path, original_name, mime_type)
         VALUES (?, "storage/uploads/requests/test-reference.jpg", "reference.jpg", "image/jpeg")'
    )->execute([$requestId]);
    $db->prepare(
        'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
         VALUES ("gift_request", ?, "customer", ?, "Review Customer", "Can you add flowers?")'
    )->execute([$requestId, $customerId]);
    AdminNotificationService::create('custom_request_reply', 'Customer replied', $requestNumber . ' has a new customer reply.', 'gift_request', $requestId);
    $db->prepare(
        'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
         VALUES ("gift_request", ?, "admin", 1, "GiftVibe Admin", "Yes, quote is ready.")'
    )->execute([$requestId]);
    $db->prepare('UPDATE custom_gift_requests SET status = "quoted", admin_notes = "Quote sent." WHERE id = ?')->execute([$requestId]);

    $request = Database::instance()->fetch(
        'SELECT custom_gift_requests.status, custom_gift_requests.admin_notes, COUNT(DISTINCT custom_gift_request_images.id) AS images, COUNT(DISTINCT message_replies.id) AS replies
         FROM custom_gift_requests
         LEFT JOIN custom_gift_request_images ON custom_gift_request_images.request_id = custom_gift_requests.id
         LEFT JOIN message_replies ON message_replies.entity_type = "gift_request" AND message_replies.entity_id = custom_gift_requests.id
         WHERE custom_gift_requests.id = :id
         GROUP BY custom_gift_requests.id',
        ['id' => $requestId]
    );
    $assert($request['status'] === 'quoted' && $request['admin_notes'] === 'Quote sent.', 'custom gift request status and admin notes save');
    $assert((int) $request['images'] === 1 && (int) $request['replies'] === 2, 'reference image and chat replies connect to custom request');

    $unreadBefore = AdminNotificationService::unreadCount();
    $notification = Database::instance()->fetch('SELECT id FROM admin_notifications WHERE status = "unread" ORDER BY id DESC LIMIT 1');
    $db->prepare('UPDATE admin_notifications SET status = "read", read_at = NOW() WHERE id = ?')->execute([(int) $notification['id']]);
    $unreadAfter = AdminNotificationService::unreadCount();
    $assert($unreadBefore > $unreadAfter, 'admin notification unread count and mark-read flow work');

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
