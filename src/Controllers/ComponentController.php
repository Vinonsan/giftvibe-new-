<?php
namespace Controllers;

final class ComponentController
{
    public function index(): void
    {
        require_once __DIR__ . '/../Views/layouts/app_layout.php';
        renderAppLayout('Component Showcase', __DIR__ . '/../Views/public/components.php');
    }
}
