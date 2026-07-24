<?php
namespace Middleware;

class StaffAuth {
    public function handle() {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
