<?php

require_once __DIR__ . '/../config/config.php';

$router = new App\Core\Router();
$router
    ->load(BASE_PATH . '/routes/admin.php')
    ->dispatch();
