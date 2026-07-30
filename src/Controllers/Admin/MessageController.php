<?php

namespace Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class MessageController
{
    private array $contactStatuses = ['new', 'read', 'replied', 'archived'];
    private array $requestStatuses = ['new', 'reviewing', 'quoted', 'approved', 'rejected', 'completed', 'cancelled'];

    public function contacts(): void
    {
        $status = in_array($_GET['status'] ?? '', $this->contactStatuses, true) ? $_GET['status'] : '';
        $where = $status ? 'WHERE status = :status' : '';
        $messages = Database::instance()->fetchAll(
            'SELECT * FROM contact_messages ' . $where . ' ORDER BY created_at DESC LIMIT 150',
            $status ? ['status' => $status] : []
        );
        View::renderPage('admin/pages/messages/index', [
            'title' => 'Contact Messages',
            'adminNavigation' => AdminNavigation::make('messages'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Messages']],
            'messages' => $messages,
            'statuses' => $this->contactStatuses,
            'filters' => ['status' => $status],
        ], 'admin/layouts/admin');
    }

    public function contactShow(string $id): void
    {
        $message = Database::instance()->fetch('SELECT * FROM contact_messages WHERE id = :id LIMIT 1', ['id' => (int) $id]);
        if (!$message) {
            http_response_code(404);
            echo 'Message not found.';
            return;
        }
        Database::instance()->execute('UPDATE contact_messages SET status = "read" WHERE id = :id AND status = "new"', ['id' => (int) $id]);
        View::renderPage('admin/pages/messages/show', [
            'title' => 'Message from ' . $message['name'],
            'adminNavigation' => AdminNavigation::make('messages'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Messages', 'url' => url('/admin/messages')], ['label' => $message['name']]],
            'message' => $message,
            'replies' => Database::instance()->fetchAll('SELECT * FROM message_replies WHERE entity_type = "contact" AND entity_id = :id ORDER BY created_at ASC', ['id' => (int) $id]),
            'statuses' => $this->contactStatuses,
        ], 'admin/layouts/admin');
    }

    public function contactReply(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/messages/' . $id);
        }
        $reply = trim((string) ($_POST['message'] ?? ''));
        $status = in_array($_POST['status'] ?? '', $this->contactStatuses, true) ? $_POST['status'] : 'replied';
        if ($reply !== '') {
            Database::instance()->execute(
                'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
                 VALUES ("contact", :entity_id, "admin", :user_id, :sender_name, :message)',
                ['entity_id' => (int) $id, 'user_id' => Auth::id(), 'sender_name' => Auth::user()['name'] ?? 'Admin', 'message' => $reply]
            );
        }
        Database::instance()->execute('UPDATE contact_messages SET status = :status WHERE id = :id', ['status' => $status, 'id' => (int) $id]);
        redirect('/admin/messages/' . $id);
    }

    public function requests(): void
    {
        $status = in_array($_GET['status'] ?? '', $this->requestStatuses, true) ? $_GET['status'] : '';
        $where = $status ? 'WHERE status = :status' : '';
        $requests = Database::instance()->fetchAll(
            'SELECT * FROM custom_gift_requests ' . $where . ' ORDER BY created_at DESC LIMIT 150',
            $status ? ['status' => $status] : []
        );
        View::renderPage('admin/pages/custom-requests/index', [
            'title' => 'Custom Gift Requests',
            'adminNavigation' => AdminNavigation::make('requests'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Custom Requests']],
            'requests' => $requests,
            'statuses' => $this->requestStatuses,
            'filters' => ['status' => $status],
        ], 'admin/layouts/admin');
    }

    public function requestShow(string $id): void
    {
        $request = Database::instance()->fetch('SELECT * FROM custom_gift_requests WHERE id = :id LIMIT 1', ['id' => (int) $id]);
        if (!$request) {
            http_response_code(404);
            echo 'Request not found.';
            return;
        }
        View::renderPage('admin/pages/custom-requests/show', [
            'title' => $request['request_number'],
            'adminNavigation' => AdminNavigation::make('requests'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Custom Requests', 'url' => url('/admin/custom-requests')], ['label' => $request['request_number']]],
            'request' => $request,
            'images' => Database::instance()->fetchAll('SELECT * FROM custom_gift_request_images WHERE request_id = :id', ['id' => (int) $id]),
            'replies' => Database::instance()->fetchAll('SELECT * FROM message_replies WHERE entity_type = "gift_request" AND entity_id = :id ORDER BY created_at ASC', ['id' => (int) $id]),
            'statuses' => $this->requestStatuses,
        ], 'admin/layouts/admin');
    }

    public function requestReply(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/custom-requests/' . $id);
        }
        $reply = trim((string) ($_POST['message'] ?? ''));
        $status = in_array($_POST['status'] ?? '', $this->requestStatuses, true) ? $_POST['status'] : 'reviewing';
        $notes = trim((string) ($_POST['admin_notes'] ?? ''));
        if ($reply !== '') {
            Database::instance()->execute(
                'INSERT INTO message_replies (entity_type, entity_id, sender_type, sender_user_id, sender_name, message)
                 VALUES ("gift_request", :entity_id, "admin", :user_id, :sender_name, :message)',
                ['entity_id' => (int) $id, 'user_id' => Auth::id(), 'sender_name' => Auth::user()['name'] ?? 'Admin', 'message' => $reply]
            );
        }
        Database::instance()->execute(
            'UPDATE custom_gift_requests SET status = :status, admin_notes = :notes WHERE id = :id',
            ['status' => $status, 'notes' => $notes, 'id' => (int) $id]
        );
        redirect('/admin/custom-requests/' . $id);
    }

}
