<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;

/**
 * Renders the base UI component preview page (route: /base).
 */
class BaseController extends Controller
{
    public function index(): void
    {
        $this->view('layouts/public-layout', [
            'title'   => 'Base Components',
            'content' => $this->render('base/preview'),
        ]);
    }
}
