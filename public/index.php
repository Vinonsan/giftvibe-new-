<?php

require_once __DIR__ . '/../config/config.php';

$router = new App\Core\Router();
$router
    ->load(BASE_PATH . '/routes/web.php')
    ->load(BASE_PATH . '/routes/customer.php')
    ->dispatch();
