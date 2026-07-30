<?php

namespace Controllers\Customer;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Services\AdminNotificationService;

class CustomGiftRequestController
{
    private array $statuses = ['new', 'reviewing', 'quoted', 'approved', 'rejected', 'completed', 'cancelled'];

    public function create(?string $message = null, ?string $error = null): void
    {
        View::renderPage('customer/pages/custom-request-form', [
            'title' => 'Custom Gift Request | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'message' => $message,
            'error' => $error,
            'meta' => ['description' => 'Request a customised GiftVibe.lk gift with reference images.', 'url' => url('/custom-gifts')],
        ], 'public/layouts/main');
    }

    public function store(): void
    {
        if (!Auth::isCustomer()) {
            redirect('/login');
        }
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->create(null, 'Your session expired. Try again.');
            return;
        }

        $user = Auth::user();
        $body = trim((string) ($_POST['message'] ?? ''));
        if ($body === '') {
            $this->create(null, 'Tell us what you want to customise.');
            return;
        }

        $requestNumber = 'CGR-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        Database::instance()->execute(
            'INSERT INTO custom_gift_requests
             (user_id, request_number, customer_name, customer_email, customer_phone, occasion, recipient, budget, delivery_date, message)
             VALUES (:user_id, :request_number, :customer_name, :customer_email, :customer_phone, :occasion, :recipient, :budget, :delivery_date, :message)',
            [
                'user_id' => Auth::id(),
                'request_number' => $requestNumber,
                'customer_name' => $user['name'] ?? 'Customer',
                'customer_email' => $user['email'] ?? '',
                'customer_phone' => $user['phone'] ?? '',
                'occasion' => trim((string) ($_POST['occasion'] ?? '')),
                'recipient' => trim((string) ($_POST['recipient'] ?? '')),
                'budget' => ($_POST['budget'] ?? '') === '' ? null : (float) $_POST['budget'],
                'delivery_date' => ($_POST['delivery_date'] ?? '') === '' ? null : $_POST['delivery_date'],
                'message' => $body,
            ]
        );
        $requestId = (int) Database::instance()->connection()->lastInsertId();
        $this->storeReferenceImages($requestId);
        AdminNotificationService::create('custom_request', 'New custom gift request', $requestNumber . ' needs review.', 'gift_request', $requestId);

        redirect('/customer/requests/' . $requestNumber);
    }

    public function index(): void
    {
        $requests = Database::instance()->fetchAll(
            'SELECT * FROM custom_gift_requests WHERE user_id = :user_id ORDER BY created_at DESC',
            ['user_id' => Auth::id()]
        );
        View::renderPage('customer/pages/requests', [
            'title' => 'My Custom Gift Requests | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'requests' => $requests,
        ], 'public/layouts/main');
    }

    public function show(string $requestNumber, ?string $error = null): void
    {
        $request = Database::instance()->fetch(
            'SELECT * FROM custom_gift_requests WHERE request_number = :request_number AND user_id = :user_id LIMIT 1',
            ['request_number' => $requestNumber, 'user_id' => Auth::id()]
        );
        if (!$request) {
            http_response_code(404);
            View::renderPage('public/pages/404', ['title' => 'Request Not Found | GiftVibe.lk']);
            return;
        }
        View::renderPage('customer/pages/request-detail', [
            'title' => $request['request_number'] . ' | GiftVibe.lk',
            'navigation' => $this->navigation(),
            'request' => $request,
            'images' => Database::instance()->fetchAll('SELECT * FROM custom_gift_request_images WHERE request_id = :id', ['id' => $request['id']]),
            'replies' => Database::instance()->fetchAll('SELECT * FROM message_replies WHERE entity_type = "gift_request" AND entity_id = :id ORDER BY created_at ASC', ['id' => $request['id']]),
            'error' => $error,
        ], 'public/layouts/main');
    }

    public function reply(string $requestNumber): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->show($requestNumber, 'Your session expired. Try again.');
            return;
        }
        $request = Database::instance()->fetch(
            'SELECT * FROM custom_gift_requests WHERE request_number = :request_number AND user_id = :user_id LIMIT 1',
            ['request_number' => $requestNumber, 'user_id' => Auth::id()]
        );
        $message = trim((string) ($_POST['message'] ?? ''));
        if (!$request || $message === '') {
            redirect('/customer/requests');
        }
        Database::instance()->execute(
            'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
             VALUES ("gift_request", :entity_id, "customer", :user_id, :sender_name, :message)',
            ['entity_id' => $request['id'], 'user_id' => Auth::id(), 'sender_name' => Auth::user()['name'] ?? 'Customer', 'message' => $message]
        );
        AdminNotificationService::create('custom_request_reply', 'Customer replied', $request['request_number'] . ' has a new customer reply.', 'gift_request', (int) $request['id']);
        redirect('/customer/requests/' . $requestNumber);
    }

    private function storeReferenceImages(int $requestId): void
    {
        if (empty($_FILES['reference_images']['tmp_name']) || !is_array($_FILES['reference_images']['tmp_name'])) {
            return;
        }
        $targetDir = UPLOAD_PATH . '/requests';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        foreach ($_FILES['reference_images']['tmp_name'] as $index => $tmp) {
            if (!$tmp || !is_uploaded_file($tmp)) {
                continue;
            }
            $mime = mime_content_type($tmp);
            if (!isset($allowed[$mime]) || (int) $_FILES['reference_images']['size'][$index] > 5 * 1024 * 1024) {
                continue;
            }
            $relative = 'storage/uploads/requests/reference-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];
            if (move_uploaded_file($tmp, BASE_PATH . '/' . $relative)) {
                Database::instance()->execute(
                    'INSERT INTO custom_gift_request_images (request_id, image_path, original_name, mime_type)
                     VALUES (:request_id, :image_path, :original_name, :mime_type)',
                    [
                        'request_id' => $requestId,
                        'image_path' => $relative,
                        'original_name' => $_FILES['reference_images']['name'][$index] ?? null,
                        'mime_type' => $mime,
                    ]
                );
            }
        }
    }

    private function navigation(): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => false],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => false],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => false],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => true],
            ['label' => 'About', 'url' => url('/about'), 'active' => false],
            ['label' => 'Contact', 'url' => url('/contact'), 'active' => false],
            ['label' => 'My Portal', 'url' => url('/customer/dashboard'), 'active' => false],
        ];
    }
}
