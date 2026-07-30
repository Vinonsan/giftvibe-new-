<?php

namespace Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class ReviewController
{
    private array $statuses = ['pending', 'approved', 'rejected'];

    public function index(): void
    {
        $status = in_array($_GET['status'] ?? '', $this->statuses, true) ? $_GET['status'] : '';
        $where = $status ? 'WHERE reviews.status = :status' : '';
        $params = $status ? ['status' => $status] : [];
        $reviews = Database::instance()->fetchAll(
            'SELECT reviews.*, products.name AS product_name, products.slug, users.first_name, users.last_name
             FROM reviews
             INNER JOIN products ON products.id = reviews.product_id
             LEFT JOIN users ON users.id = reviews.user_id
             ' . $where . '
             ORDER BY reviews.created_at DESC
             LIMIT 150',
            $params
        );

        View::renderPage('admin/pages/reviews/index', [
            'title' => 'Reviews',
            'adminNavigation' => AdminNavigation::make('reviews'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Reviews']],
            'reviews' => $reviews,
            'statuses' => $this->statuses,
            'filters' => ['status' => $status],
        ], 'admin/layouts/admin');
    }

    public function updateStatus(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            redirect('/admin/reviews');
        }
        $status = in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'pending';
        Database::instance()->execute(
            'UPDATE reviews SET status = :status WHERE id = :id',
            ['status' => $status, 'id' => (int) $id]
        );
        redirect('/admin/reviews');
    }

}
