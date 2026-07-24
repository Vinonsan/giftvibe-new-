<?php
namespace Components\Base;

use Helpers\Ui;

final class Input
{
    public static function render(string $name, array $attrs = []): string
    {
        $label = $attrs['label'] ?? '';
        $error = $attrs['error'] ?? '';
        $help = $attrs['help'] ?? '';
        unset($attrs['label'], $attrs['error'], $attrs['help']);

        $id = (string) ($attrs['id'] ?? $name);
        $customClass = $attrs['class'] ?? '';
        unset($attrs['class']);
        $attrs = array_merge(['type' => 'text'], $attrs, ['name' => $name, 'id' => $id]);

        $stateClass = $error
            ? 'border-danger focus:border-danger focus:ring-danger-100'
            : 'border-secondary-300 focus:border-primary focus:ring-primary-100';

        $html = '<div>';
        if ($label !== '') {
            $html .= '<label for="' . Ui::escape($id) . '" class="mb-1.5 block text-sm font-semibold text-dark">' . Ui::escape((string) $label) . '</label>';
        }
        $html .= '<input class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-dark outline-none transition placeholder:text-secondary-400 focus:ring-2 disabled:cursor-not-allowed disabled:bg-secondary-100 ' . $stateClass . ' ' . Ui::escape((string) $customClass) . '"' . Ui::attributes($attrs) . '>';
        if ($error !== '') {
            $html .= '<p class="mt-1.5 text-xs font-medium text-danger">' . Ui::escape((string) $error) . '</p>';
        } elseif ($help !== '') {
            $html .= '<p class="mt-1.5 text-xs text-secondary">' . Ui::escape((string) $help) . '</p>';
        }
        return $html . '</div>';
    }
}
