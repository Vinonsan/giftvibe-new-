<?php
namespace Components\Base;

use Helpers\Ui;

final class Card
{
    public static function render(string $title = '', string $body = '', array $opts = []): string
    {
        $class = 'overflow-hidden rounded-2xl border border-secondary-200 bg-white shadow-sm ' . ($opts['class'] ?? '');
        $html = '<div class="' . trim($class) . '">';
        if ($title) {
            $html .= '<div class="border-b border-secondary-100 px-5 py-4"><h3 class="text-sm font-bold text-dark">' . Ui::escape($title) . '</h3></div>';
        }
        $html .= '<div class="p-5">' . $body . '</div>';
        if (!empty($opts['footer'])) {
            $html .= '<div class="border-t border-secondary-100 bg-light px-5 py-4">' . $opts['footer'] . '</div>';
        }
        $html .= '</div>';
        return $html;
    }
}
