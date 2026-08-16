<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FinanceSummaryService;

class DashboardController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $stats = FinanceSummaryService::summary($pdo);

        $this->view('layouts/admin-layout', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'showPageTitle' => false,
            'content' => $this->render('admin/dashboard/index', ['stats' => $stats]),
        ]);
    }
}
