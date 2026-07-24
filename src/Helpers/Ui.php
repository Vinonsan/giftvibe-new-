<?php
namespace Helpers;

final class Ui {
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function attributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if (!preg_match('/^[a-zA-Z_:][a-zA-Z0-9:._-]*$/', (string) $name) || $value === false || $value === null) {
                continue;
            }
            $html .= ' ' . $name;
            if ($value !== true) {
                $html .= '="' . self::escape((string) $value) . '"';
            }
        }
        return $html;
    }
}
