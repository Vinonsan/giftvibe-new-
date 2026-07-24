<?php
namespace Components\Base;

use Helpers\Ui;

final class Button
{
    public static function render(string $label, string $variant = 'primary', array $attrs = []): string
    {
        $variantClass = match ($variant) {
            'secondary' => 'bg-secondary text-white hover:bg-secondary-700 focus:ring-secondary-200',
            'dark' => 'bg-dark text-white hover:bg-dark-800 focus:ring-dark-200',
            'light' => 'border border-secondary-200 bg-light text-dark hover:bg-secondary-100 focus:ring-secondary-100',
            'danger', 'error' => 'bg-danger text-white hover:bg-danger-700 focus:ring-danger-200',
            'info' => 'bg-info text-white hover:bg-info-700 focus:ring-info-200',
            'success' => 'bg-success text-white hover:bg-success-700 focus:ring-success-200',
            'ghost' => 'bg-transparent text-secondary-700 hover:bg-secondary-100 focus:ring-secondary-100',
            default => 'bg-primary text-white hover:bg-primary-700 focus:ring-primary-200',
        };

        $customClass = $attrs['class'] ?? '';
        unset($attrs['class']);
        $attrs += ['type' => 'button'];

        return sprintf(
            '<button class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 %s %s"%s>%s</button>',
            $variantClass, Ui::escape((string) $customClass), Ui::attributes($attrs), Ui::escape($label)
        );
    }
}
