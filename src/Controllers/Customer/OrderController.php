<?php

namespace Controllers\Customer;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class OrderController
{
    public function index(): void
    {
        $orders = Database::instance()->fetchAll(
            'SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC',
            ['user_id' => Auth::id()]
        );
        View::renderPage('customer/pages/orders', [
            'title' => 'My Orders | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'orders' => $orders,
        ], 'public/layouts/main');
    }

    public function show(string $orderNumber): void
    {
        $order = Database::instance()->fetch(
            'SELECT * FROM orders WHERE order_number = :order_number AND user_id = :user_id LIMIT 1',
            ['order_number' => $orderNumber, 'user_id' => Auth::id()]
        );
        if (!$order) {
            http_response_code(404);
            View::renderPage('public/pages/404', ['title' => 'Order Not Found | GiftVibe.lk']);
            return;
        }
        $items = Database::instance()->fetchAll('SELECT * FROM order_items WHERE order_id = :id', ['id' => $order['id']]);
        $payment = Database::instance()->fetch('SELECT * FROM payments WHERE order_id = :id ORDER BY id DESC LIMIT 1', ['id' => $order['id']]);
        $history = Database::instance()->fetchAll('SELECT * FROM order_status_history WHERE order_id = :id ORDER BY created_at DESC', ['id' => $order['id']]);
        $delivery = Database::instance()->fetch('SELECT * FROM deliveries WHERE order_id = :id LIMIT 1', ['id' => $order['id']]);
        $deliveryHistory = Database::instance()->fetchAll(
            'SELECT status, note, created_at FROM delivery_status_history WHERE order_id = :id ORDER BY created_at DESC',
            ['id' => $order['id']]
        );

        View::renderPage('customer/pages/order-detail', [
            'title' => 'Order ' . $order['order_number'] . ' | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'order' => $order,
            'items' => $items,
            'payment' => $payment,
            'history' => $history,
            'delivery' => $delivery,
            'deliveryHistory' => $deliveryHistory,
        ], 'public/layouts/main');
    }

    private function navigation(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => false],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => false],
            ['label' => 'My Portal', 'url' => url('/customer/dashboard'), 'active' => true],
        ];
    }
}
