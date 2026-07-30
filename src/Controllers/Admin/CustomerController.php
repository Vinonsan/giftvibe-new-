<?php

namespace Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class CustomerController
{
    public function index(): void
    {
        $customers = Database::instance()->fetchAll(
            'SELECT users.*,
                    (SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id) AS orders_count,
                    (SELECT COALESCE(SUM(grand_total), 0) FROM orders WHERE orders.user_id = users.id) AS lifetime_value,
                    (SELECT COUNT(*) FROM customer_addresses WHERE customer_addresses.user_id = users.id) AS address_count,
                    (SELECT COUNT(*) FROM custom_gift_requests WHERE custom_gift_requests.user_id = users.id) AS request_count
             FROM users
             INNER JOIN roles ON roles.id = users.role_id AND roles.slug = "customer"
             ORDER BY users.created_at DESC
             LIMIT 150'
        );

        View::renderPage('admin/pages/customers/index', [
            'title' => 'Customers',
            'adminNavigation' => AdminNavigation::make('customers'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Customers']],
            'customers' => $customers,
        ], 'admin/layouts/admin');
    }
}
