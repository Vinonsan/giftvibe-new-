<?php
namespace Components\Base;

use Helpers\Ui;

final class Alert
{
    public static function render(string $message, string $type = 'info'): string
    {
        $colors = [
            'primary' => 'border-primary-200 bg-primary-50 text-primary-700',
            'secondary' => 'border-secondary-200 bg-secondary-50 text-secondary-700',
            'dark' => 'border-dark-700 bg-dark text-white',
            'light' => 'border-secondary-200 bg-light text-dark',
            'success' => 'border-success-200 bg-success-50 text-success-700',
            'danger' => 'border-danger-200 bg-danger-50 text-danger-700',
            'error' => 'border-danger-200 bg-danger-50 text-danger-700',
            'warning' => 'border-warning-200 bg-warning-50 text-warning-700',
            'info' => 'border-info-200 bg-info-50 text-info-700',
        ];
        $class = $colors[$type] ?? $colors['info'];
        return '<div role="alert" class="mb-4 flex items-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium ' . $class . '">' . Ui::escape($message) . '</div>';
    }
}
