<?php

namespace Controllers\Customer;

use App\Core\Auth;
use App\Core\Database;
use Services\AdminNotificationService;

class ReviewController
{
    public function store(): void
    {
        if (!Auth::isCustomer()) {
            redirect('/login');
        }
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/shop');
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 0)));
        $body = trim((string) ($_POST['body'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        $product = Database::instance()->fetch('SELECT id, slug, name FROM products WHERE id = :id AND status = "active"', ['id' => $productId]);

        if (!$product || $body === '') {
            redirect('/shop');
        }

        Database::instance()->execute(
            'INSERT INTO reviews (product_id, user_id, rating, title, body, status)
             VALUES (:product_id, :user_id, :rating, :title, :body, "pending")',
            [
                'product_id' => $productId,
                'user_id' => Auth::id(),
                'rating' => $rating,
                'title' => $title,
                'body' => $body,
            ]
        );
        $id = (int) Database::instance()->connection()->lastInsertId();
        AdminNotificationService::create('review', 'New product review', 'A review is waiting for approval: ' . $product['name'], 'review', $id);

        redirect('/product/' . $product['slug']);
    }
}
