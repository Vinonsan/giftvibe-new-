<?php
namespace Components\Base;

use Helpers\Ui;

final class Badge
{
    public static function render(string $text, string $color = 'secondary'): string
    {
        $colors = [
            'primary', 'blue' => 'bg-primary-100 text-primary-700',
            'secondary', 'gray' => 'bg-secondary-100 text-secondary-700',
            'dark' => 'bg-dark text-white',
            'light' => 'border border-secondary-200 bg-light text-dark',
            'danger', 'error', 'red' => 'bg-danger-100 text-danger-700',
            'info' => 'bg-info-100 text-info-700',
            'success', 'green' => 'bg-success-100 text-success-700',
            'warning', 'orange' => 'bg-warning-100 text-warning-700',
            'purple' => 'bg-primary-100 text-primary-700',
        ];
        $class = $colors[$color] ?? $colors['secondary'];
        return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ' . $class . '">' . Ui::escape($text) . '</span>';
    }
}
