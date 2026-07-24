<?php

if (!function_exists('component')) {
    /**
     * Render a view component helper.
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    function component(string $name, array $data = []): void
    {
        try {
            \App\Core\View::renderComponent($name, $data);
        } catch (Exception $e) {
            echo '<div style="color:red; border:1px solid red; padding:8px; margin:4px 0;">' .
                 '<strong>Component Error [' . htmlspecialchars($name) . ']:</strong> ' .
                 htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
