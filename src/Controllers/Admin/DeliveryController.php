<?php

namespace Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class DeliveryController
{
    private array $statuses = ['pending', 'assigned', 'dispatched', 'picked_up', 'out_for_delivery', 'delivered', 'failed', 'returned'];

    public function index(): void
    {
        $status = in_array($_GET['status'] ?? '', $this->statuses, true) ? $_GET['status'] : '';
        $q = trim((string) ($_GET['q'] ?? ''));
        $where = ['1=1'];
        $params = [];

        if ($status !== '') {
            $where[] = 'deliveries.delivery_status = :status';
            $params['status'] = $status;
        }
        if ($q !== '') {
            $where[] = '(orders.order_number LIKE :q OR deliveries.courier_tracking_number LIKE :q OR deliveries.parcel_reference_number LIKE :q OR deliveries.courier_service_name LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $deliveries = Database::instance()->fetchAll(
            'SELECT deliveries.*, orders.order_number, orders.customer_name, orders.recipient_name, orders.delivery_city, orders.delivery_district
             FROM deliveries
             INNER JOIN orders ON orders.id = deliveries.order_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY deliveries.updated_at DESC
             LIMIT 150',
            $params
        );

        $counts = $this->counts();
        $reminders = $this->openReminders();

        View::renderPage('admin/pages/deliveries/index', [
            'title' => 'Deliveries',
            'adminNavigation' => AdminNavigation::make('deliveries'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Deliveries']],
            'deliveries' => $deliveries,
            'counts' => $counts,
            'reminders' => $reminders,
            'statuses' => $this->statuses,
            'filters' => ['status' => $status, 'q' => $q],
        ], 'admin/layouts/admin');
    }

    public function saveParcel(string $orderId): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/orders/' . $orderId);
        }

        $delivery = $this->ensureDelivery((int) $orderId);
        $qrPath = $this->storeQrCode($delivery['qr_code_path'] ?? null);
        $charge = (float) ($_POST['delivery_charge'] ?? 0);
        $status = in_array($_POST['delivery_status'] ?? '', $this->statuses, true) ? $_POST['delivery_status'] : ($delivery['delivery_status'] ?? 'pending');
        $trackingNumber = $this->nullableString($_POST['courier_tracking_number'] ?? null);

        Database::instance()->execute(
            'UPDATE deliveries
             SET courier_service_name = :courier_service_name,
                 courier_tracking_number = :courier_tracking_number,
                 parcel_reference_number = :parcel_reference_number,
                 qr_code_path = :qr_code_path,
                 delivery_charge = :delivery_charge,
                 dispatch_date = :dispatch_date,
                 expected_delivery_date = :expected_delivery_date,
                 delivered_date = :delivered_date,
                 delivered_at = :delivered_at,
                 delivery_status = :delivery_status,
                 tracking_code = :tracking_code,
                 notes = :notes,
                 assigned_staff_id = :assigned_staff_id
             WHERE id = :id',
            [
                'courier_service_name' => trim((string) ($_POST['courier_service_name'] ?? '')),
                'courier_tracking_number' => $trackingNumber,
                'parcel_reference_number' => $this->nullableString($_POST['parcel_reference_number'] ?? null),
                'qr_code_path' => $qrPath,
                'delivery_charge' => $charge,
                'dispatch_date' => $this->nullableDate($_POST['dispatch_date'] ?? null),
                'expected_delivery_date' => $this->nullableDate($_POST['expected_delivery_date'] ?? null),
                'delivered_date' => $this->nullableDate($_POST['delivered_date'] ?? null),
                'delivered_at' => $status === 'delivered' ? date('Y-m-d H:i:s') : ($delivery['delivered_at'] ?? null),
                'delivery_status' => $status,
                'tracking_code' => $trackingNumber,
                'notes' => trim((string) ($_POST['notes'] ?? '')),
                'assigned_staff_id' => Auth::id(),
                'id' => $delivery['id'],
            ]
        );

        $this->recordHistory((int) $delivery['id'], (int) $orderId, $status, trim((string) ($_POST['history_note'] ?? 'Parcel details updated.')));
        $this->syncOrderStatus((int) $orderId, $status);

        $reminderAt = trim((string) ($_POST['reminder_at'] ?? ''));
        $reminderMessage = trim((string) ($_POST['reminder_message'] ?? ''));
        if ($reminderAt !== '' && $reminderMessage !== '') {
            Database::instance()->execute(
                'INSERT INTO delivery_reminders (delivery_id, order_id, admin_id, reminder_at, message)
                 VALUES (:delivery_id, :order_id, :admin_id, :reminder_at, :message)',
                [
                    'delivery_id' => $delivery['id'],
                    'order_id' => (int) $orderId,
                    'admin_id' => Auth::id(),
                    'reminder_at' => date('Y-m-d H:i:s', strtotime($reminderAt)),
                    'message' => $reminderMessage,
                ]
            );
        }

        redirect('/admin/orders/' . $orderId);
    }

    public function updateStatus(string $orderId): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/orders/' . $orderId);
        }

        $delivery = $this->ensureDelivery((int) $orderId);
        $status = in_array($_POST['delivery_status'] ?? '', $this->statuses, true) ? $_POST['delivery_status'] : 'pending';
        $note = trim((string) ($_POST['note'] ?? ''));

        Database::instance()->execute(
            'UPDATE deliveries
             SET delivery_status = :status,
                 delivered_date = CASE WHEN :status_for_date = "delivered" THEN CURRENT_DATE() ELSE delivered_date END,
                 delivered_at = CASE WHEN :status_for_at = "delivered" THEN NOW() ELSE delivered_at END,
                 assigned_staff_id = :admin_id
             WHERE id = :id',
            [
                'status' => $status,
                'status_for_date' => $status,
                'status_for_at' => $status,
                'admin_id' => Auth::id(),
                'id' => $delivery['id'],
            ]
        );

        $this->recordHistory((int) $delivery['id'], (int) $orderId, $status, $note);
        $this->syncOrderStatus((int) $orderId, $status);

        redirect('/admin/orders/' . $orderId);
    }

    public function completeReminder(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/deliveries');
        }

        Database::instance()->execute(
            'UPDATE delivery_reminders SET status = "completed", completed_at = NOW() WHERE id = :id',
            ['id' => (int) $id]
        );

        redirect('/admin/deliveries');
    }

    public function ensureDelivery(int $orderId): array
    {
        $delivery = Database::instance()->fetch('SELECT * FROM deliveries WHERE order_id = :order_id LIMIT 1', ['order_id' => $orderId]);
        if ($delivery) {
            return $delivery;
        }

        $order = Database::instance()->fetch('SELECT delivery_fee FROM orders WHERE id = :id LIMIT 1', ['id' => $orderId]);
        Database::instance()->execute(
            'INSERT INTO deliveries (order_id, assigned_staff_id, delivery_charge, delivery_status)
             VALUES (:order_id, :admin_id, :delivery_charge, "pending")',
            ['order_id' => $orderId, 'admin_id' => Auth::id(), 'delivery_charge' => (float) ($order['delivery_fee'] ?? 0)]
        );
        $delivery = Database::instance()->fetch('SELECT * FROM deliveries WHERE id = LAST_INSERT_ID()') ?? [];
        $this->recordHistory((int) $delivery['id'], $orderId, 'pending', 'Delivery record created.');

        return $delivery;
    }

    public function statuses(): array
    {
        return $this->statuses;
    }

    private function counts(): array
    {
        $rows = Database::instance()->fetchAll('SELECT delivery_status, COUNT(*) AS total FROM deliveries GROUP BY delivery_status');
        $counts = array_fill_keys($this->statuses, 0);
        foreach ($rows as $row) {
            $counts[$row['delivery_status']] = (int) $row['total'];
        }
        $counts['due_reminders'] = (int) (Database::instance()->fetch(
            'SELECT COUNT(*) AS total FROM delivery_reminders WHERE status = "open" AND reminder_at <= NOW()'
        )['total'] ?? 0);

        return $counts;
    }

    private function openReminders(): array
    {
        return Database::instance()->fetchAll(
            'SELECT delivery_reminders.*, orders.order_number
             FROM delivery_reminders
             INNER JOIN orders ON orders.id = delivery_reminders.order_id
             WHERE delivery_reminders.status = "open"
             ORDER BY delivery_reminders.reminder_at ASC
             LIMIT 10'
        );
    }

    private function recordHistory(int $deliveryId, int $orderId, string $status, string $note = ''): void
    {
        Database::instance()->execute(
            'INSERT INTO delivery_status_history (delivery_id, order_id, status, note, changed_by)
             VALUES (:delivery_id, :order_id, :status, :note, :changed_by)',
            [
                'delivery_id' => $deliveryId,
                'order_id' => $orderId,
                'status' => $status,
                'note' => $note,
                'changed_by' => Auth::id(),
            ]
        );
    }

    private function syncOrderStatus(int $orderId, string $deliveryStatus): void
    {
        $map = [
            'dispatched' => 'out_for_delivery',
            'picked_up' => 'out_for_delivery',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'failed' => 'processing',
            'returned' => 'cancelled',
        ];

        if (!isset($map[$deliveryStatus])) {
            return;
        }

        Database::instance()->execute(
            'UPDATE orders SET order_status = :status WHERE id = :id',
            ['status' => $map[$deliveryStatus], 'id' => $orderId]
        );
        Database::instance()->execute(
            'INSERT INTO order_status_history (order_id, status, note, changed_by)
             VALUES (:order_id, :status, :note, :changed_by)',
            [
                'order_id' => $orderId,
                'status' => $map[$deliveryStatus],
                'note' => 'Order status synced from parcel status: ' . $deliveryStatus,
                'changed_by' => Auth::id(),
            ]
        );
    }

    private function storeQrCode(?string $existing): ?string
    {
        if (empty($_FILES['qr_code']['tmp_name']) || !is_uploaded_file($_FILES['qr_code']['tmp_name'])) {
            return $existing;
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        $mime = mime_content_type($_FILES['qr_code']['tmp_name']);
        if (!isset($allowed[$mime]) || (int) $_FILES['qr_code']['size'] > 5 * 1024 * 1024) {
            return $existing;
        }

        $relative = 'storage/uploads/deliveries/courier-qr-' . date('YmdHis') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
        if (!move_uploaded_file($_FILES['qr_code']['tmp_name'], BASE_PATH . '/' . $relative)) {
            return $existing;
        }

        return $relative;
    }

    private function nullableDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

}
