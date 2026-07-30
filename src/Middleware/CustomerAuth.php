<?php
namespace Middleware;

use App\Core\Auth;

class CustomerAuth
{
    public function handle(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if (!Auth::isCustomer()) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }
}
