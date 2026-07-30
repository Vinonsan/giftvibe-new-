<?php

namespace Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class OrderController
{
    private array $statuses = ['pending', 'confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered', 'cancelled', 'refunded'];

    public function index(): void
    {
        $status = in_array($_GET['status'] ?? '', $this->statuses, true) ? $_GET['status'] : '';
        $deliveryStatuses = (new DeliveryController())->statuses();
        $parcelStatus = in_array($_GET['parcel_status'] ?? '', $deliveryStatuses, true) ? $_GET['parcel_status'] : '';
        $q = trim((string) ($_GET['q'] ?? ''));
        $where = ['1=1'];
        $params = [];
        if ($status) {
            $where[] = 'orders.order_status = :status';
            $params['status'] = $status;
        }
        if ($parcelStatus) {
            $where[] = 'deliveries.delivery_status = :parcel_status';
            $params['parcel_status'] = $parcelStatus;
        }
        if ($q !== '') {
            $where[] = '(orders.order_number LIKE :q OR orders.customer_name LIKE :q OR orders.customer_phone LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $orders = Database::instance()->fetchAll(
            'SELECT orders.*, payments.method AS payment_method, payments.status AS gateway_status,
                    deliveries.delivery_status AS parcel_status,
                    deliveries.courier_service_name,
                    deliveries.courier_tracking_number
             FROM orders
             LEFT JOIN payments ON payments.order_id = orders.id
             LEFT JOIN deliveries ON deliveries.order_id = orders.id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY orders.created_at DESC
             LIMIT 100',
            $params
        );

        View::renderPage('admin/pages/orders/index', [
            'title' => 'Orders',
            'adminNavigation' => AdminNavigation::make('orders'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Orders']],
            'orders' => $orders,
            'statuses' => $this->statuses,
            'deliveryStatuses' => $deliveryStatuses,
            'filters' => ['status' => $status, 'parcel_status' => $parcelStatus, 'q' => $q],
        ], 'admin/layouts/admin');
    }

    public function show(string $id): void
    {
        $order = Database::instance()->fetch('SELECT * FROM orders WHERE id = :id LIMIT 1', ['id' => (int) $id]);
        if (!$order) {
            http_response_code(404);
            echo 'Order not found.';
            return;
        }
        $items = Database::instance()->fetchAll('SELECT * FROM order_items WHERE order_id = :id', ['id' => $order['id']]);
        $payment = Database::instance()->fetch('SELECT * FROM payments WHERE order_id = :id ORDER BY id DESC LIMIT 1', ['id' => $order['id']]);
        $history = Database::instance()->fetchAll('SELECT * FROM order_status_history WHERE order_id = :id ORDER BY created_at DESC', ['id' => $order['id']]);
        $deliveryController = new DeliveryController();
        $delivery = $deliveryController->ensureDelivery((int) $order['id']);
        $deliveryHistory = Database::instance()->fetchAll(
            'SELECT delivery_status_history.*, users.first_name, users.last_name
             FROM delivery_status_history
             LEFT JOIN users ON users.id = delivery_status_history.changed_by
             WHERE delivery_status_history.order_id = :id
             ORDER BY delivery_status_history.created_at DESC',
            ['id' => $order['id']]
        );
        $deliveryReminders = Database::instance()->fetchAll(
            'SELECT * FROM delivery_reminders WHERE order_id = :id ORDER BY reminder_at ASC',
            ['id' => $order['id']]
        );
        $totals = $this->totals($items, $order);

        View::renderPage('admin/pages/orders/show', [
            'title' => 'Order ' . $order['order_number'],
            'adminNavigation' => $this->navigation('orders'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Orders', 'url' => url('/admin/orders')], ['label' => $order['order_number']]],
            'order' => $order,
            'items' => $items,
            'payment' => $payment,
            'history' => $history,
            'delivery' => $delivery,
            'deliveryHistory' => $deliveryHistory,
            'deliveryReminders' => $deliveryReminders,
            'deliveryStatuses' => $deliveryController->statuses(),
            'statuses' => $this->statuses,
            'totals' => $totals,
        ], 'admin/layouts/admin');
    }

    public function updateStatus(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/orders/' . $id);
        }
        $status = in_array($_POST['order_status'] ?? '', $this->statuses, true) ? $_POST['order_status'] : 'pending';
        $note = trim((string) ($_POST['note'] ?? ''));
        Database::instance()->execute(
            'UPDATE orders SET order_status = :status, admin_notes = :note WHERE id = :id',
            ['status' => $status, 'note' => $note, 'id' => (int) $id]
        );
        Database::instance()->execute(
            'INSERT INTO order_status_history (order_id, status, note, changed_by) VALUES (:order_id, :status, :note, :changed_by)',
            ['order_id' => (int) $id, 'status' => $status, 'note' => $note, 'changed_by' => Auth::id()]
        );
        redirect('/admin/orders/' . $id);
    }

    public function verifyPayment(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/orders/' . $id);
        }
        $status = ($_POST['payment_status'] ?? '') === 'paid' ? 'paid' : 'failed';
        Database::instance()->execute(
            'UPDATE payments
             SET status = :status,
                 verified_by = :verified_by,
                 verified_at = NOW(),
                 verification_notes = :notes,
                 paid_at = CASE WHEN :status_for_paid_at = "paid" THEN NOW() ELSE paid_at END
             WHERE order_id = :order_id',
            [
                'status' => $status,
                'status_for_paid_at' => $status,
                'verified_by' => Auth::id(),
                'notes' => trim((string) ($_POST['verification_notes'] ?? '')),
                'order_id' => (int) $id,
            ]
        );
        Database::instance()->execute(
            'UPDATE orders SET payment_status = :payment_status WHERE id = :id',
            ['payment_status' => $status === 'paid' ? 'paid' : 'failed', 'id' => (int) $id]
        );
        redirect('/admin/orders/' . $id);
    }

    private function totals(array $items, array $order): array
    {
        $sales = (float) $order['grand_total'];
        $cost = 0.0;
        foreach ($items as $item) {
            $cost += (float) $item['cost_price'] * (int) $item['quantity'];
        }
        return ['sales' => $sales, 'cost' => $cost, 'profit' => ((float) $order['subtotal'] - $cost - (float) $order['discount_total'])];
    }

}
