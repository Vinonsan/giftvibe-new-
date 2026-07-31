<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('layouts/public-layout', [
            'title'   => 'Hello World',
            'content' => $this->render('public/home/index', [
                'title'   => 'Hello World',
                'message' => 'Hello World',
            ]),
        ]);
    }
}
