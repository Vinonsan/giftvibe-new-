<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('layouts/admin-layout', [
            'title'   => 'Dashboard',
            'content' => $this->render('admin/dashboard/index'),
        ]);
    }
}
