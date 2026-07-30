<?php

/**
 * Application colour tokens used by Tailwind's CDN configuration.
 *
 * The brand palette is intentionally centered on four shared colours:
 * primary, secondary, dark, and light. Supporting semantic colours are kept for
 * interface states such as success, warning, danger, and info.
 */
function brandColors(): array
{
    return [
        'primary' => '#0C2B4E',
        'secondary' => '#1A3D64',
        'dark' => '#1D546C',
        'light' => '#F4F4F4',
    ];
}

function themeColors(): array
{
    return [
        'primary' => [
            '50' => '#eef6ff', '100' => '#d8eaff', '200' => '#badbff',
            '300' => '#8bc6ff', '400' => '#55a6f7', '500' => '#2f83d8',
            '600' => '#1d64b0', '700' => '#164f8f', '800' => '#123f70',
            '900' => '#0C2B4E', '950' => '#06182d', 'DEFAULT' => '#0C2B4E',
        ],
        'secondary' => [
            '50' => '#f0f7fb', '100' => '#d9ecf7', '200' => '#b7dcef',
            '300' => '#84c4e2', '400' => '#4aa4ce', '500' => '#2888b4',
            '600' => '#216d92', '700' => '#1d5a78', '800' => '#1A3D64',
            '900' => '#153452', '950' => '#0b1f33', 'DEFAULT' => '#1A3D64',
        ],
        'dark' => [
            '50' => '#eef8fa', '100' => '#d6edf2', '200' => '#b2dde7',
            '300' => '#7fc5d4', '400' => '#46a4b9', '500' => '#2f879e',
            '600' => '#286d82', '700' => '#1D546C', '800' => '#1a475a',
            '900' => '#183c4c', '950' => '#0b2733', 'DEFAULT' => '#1D546C',
        ],
        'light' => [
            '50' => '#ffffff', '100' => '#F4F4F4', '200' => '#e8e8e8',
            '300' => '#d4d4d4', '400' => '#a3a3a3', '500' => '#737373',
            '600' => '#525252', '700' => '#404040', '800' => '#262626',
            '900' => '#171717', 'DEFAULT' => '#F4F4F4',
        ],
        'danger' => [
            '50' => '#fef2f2', '100' => '#fee2e2', '200' => '#fecaca',
            '300' => '#fca5a5', '400' => '#f87171', '500' => '#ef4444',
            '600' => '#dc2626', '700' => '#b91c1c', '800' => '#991b1b',
            '900' => '#7f1d1d', '950' => '#450a0a', 'DEFAULT' => '#dc2626',
        ],
        'info' => [
            '50' => '#ecfeff', '100' => '#cffafe', '200' => '#a5f3fc',
            '300' => '#67e8f9', '400' => '#22d3ee', '500' => '#06b6d4',
            '600' => '#0891b2', '700' => '#0e7490', '800' => '#155e75',
            '900' => '#164e63', '950' => '#083344', 'DEFAULT' => '#0891b2',
        ],
        'success' => [
            '50' => '#f0fdf4', '100' => '#dcfce7', '200' => '#bbf7d0',
            '500' => '#22c55e', '600' => '#16a34a', '700' => '#15803d',
            'DEFAULT' => '#16a34a',
        ],
        'warning' => [
            '50' => '#fffbeb', '100' => '#fef3c7', '200' => '#fde68a',
            '500' => '#f59e0b', '600' => '#d97706', '700' => '#b45309',
            'DEFAULT' => '#d97706',
        ],
    ];
}

function themeCssVariables(): string
{
    $variables = [];
    foreach (brandColors() as $name => $value) {
        $variables[] = "--color-{$name}: {$value};";
    }

    return ':root{' . implode('', $variables) . '}';
}

function themeTailwindColorsJs(): string
{
    return json_encode(themeColors(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
}

function adminTailwindColorsJs(): string
{
    return json_encode(array_merge(themeColors(), [
        'sidebar' => '#0C2B4E',
        'surface' => '#ffffff',
    ]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
}
