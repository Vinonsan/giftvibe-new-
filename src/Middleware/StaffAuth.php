<?php
namespace Middleware;

use App\Core\Auth;

class StaffAuth
{
    public function handle(): void
    {
        if (!Auth::check()) {
            redirect('/admin/login');
        }

        if (!Auth::isStaff()) {
            Auth::logout();
            redirect('/admin/login');
        }
    }
}
