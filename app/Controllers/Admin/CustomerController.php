<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class CustomerController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        
        // Fetch all customers (role_id = 2 or role name = 'customer')
        $customers = $pdo->query("SELECT u.* FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.role_id = 2 OR r.name = 'customer' ORDER BY u.id DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch all orders grouped by user_id
        $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        $customerOrders = [];
        foreach ($orders as $order) {
            $customerOrders[(int)$order['user_id']][] = $order;
        }

        $content = $this->render('admin/customers/index', [
            'customers' => $customers,
            'customerOrders' => $customerOrders
        ]);
        $this->view('layouts/admin-layout', ['title' => 'Customers', 'showPageTitle' => false, 'content' => $content]);
    }
}
