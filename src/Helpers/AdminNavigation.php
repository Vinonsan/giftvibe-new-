<?php

namespace Helpers;

class AdminNavigation
{
    public static function make(string $active = 'dashboard'): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => url('/admin/dashboard'), 'active' => $active === 'dashboard'],
            [
                'label' => 'Catalog',
                'icon' => 'shopping-bag',
                'children' => [
                    ['label' => 'Products', 'url' => url('/admin/products'), 'active' => $active === 'products'],
                    ['label' => 'Categories', 'url' => url('/admin/categories'), 'active' => $active === 'categories'],
                ],
            ],
            ['label' => 'Orders', 'icon' => 'shopping-bag', 'url' => url('/admin/orders'), 'active' => $active === 'orders'],
            ['label' => 'Deliveries', 'icon' => 'archive-box', 'url' => url('/admin/deliveries'), 'active' => $active === 'deliveries'],
            ['label' => 'Reviews', 'icon' => 'star', 'url' => url('/admin/reviews'), 'active' => $active === 'reviews'],
            ['label' => 'Messages', 'icon' => 'mail', 'url' => url('/admin/messages'), 'active' => $active === 'messages'],
            ['label' => 'Custom Requests', 'icon' => 'gift', 'url' => url('/admin/custom-requests'), 'active' => $active === 'requests'],
            ['label' => 'Notifications', 'icon' => 'bell', 'url' => url('/admin/notifications'), 'active' => $active === 'notifications'],
            ['label' => 'Expenses', 'icon' => 'currency-dollar', 'url' => url('/admin/expenses'), 'active' => $active === 'expenses'],
            ['label' => 'Reports', 'icon' => 'chart-bar', 'url' => url('/admin/reports'), 'active' => $active === 'reports'],
            ['label' => 'Customers', 'icon' => 'users', 'url' => url('/admin/customers'), 'active' => $active === 'customers'],
            ['label' => 'Settings', 'icon' => 'cog', 'url' => url('/admin/settings'), 'active' => $active === 'settings'],
        ];
    }
}
