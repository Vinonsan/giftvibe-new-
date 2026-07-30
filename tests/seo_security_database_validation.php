<?php

require __DIR__ . '/../config/config.php';

use App\Core\Database;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

try {
    $schema = file_get_contents(BASE_PATH . '/database/schema.sql') ?: '';
    $assert(str_contains($schema, 'CREATE TABLE expenses'), 'database schema includes expenses table');
    $assert(str_contains($schema, 'receipt_path'), 'database schema includes expense receipt storage');
    $assert(str_contains($schema, 'CONSTRAINT fk_expenses_admin'), 'database schema links expenses to responsible admin');

    $storageHtaccess = file_get_contents(BASE_PATH . '/storage/.htaccess') ?: '';
    $assert(str_contains($storageHtaccess, 'Require all denied'), 'storage directory denies direct web access');
    $assert(verify_csrf(csrf_token()), 'CSRF helper validates current token');
    $assert(!verify_csrf('not-the-token'), 'CSRF helper rejects invalid token');

    $layout = file_get_contents(BASE_PATH . '/resources/views/public/layouts/main.php') ?: '';
    $seo = file_get_contents(BASE_PATH . '/resources/views/public/components/common/seo-meta.php') ?: '';
    $home = file_get_contents(BASE_PATH . '/resources/views/public/pages/home.php') ?: '';
    $expensesView = file_get_contents(BASE_PATH . '/resources/views/admin/pages/expenses/index.php') ?: '';
    $reportsView = file_get_contents(BASE_PATH . '/resources/views/admin/pages/reports/index.php') ?: '';
    $assert(str_contains($layout, 'viewport'), 'public layout includes responsive viewport meta');
    $assert(str_contains($seo, 'og:title') && str_contains($seo, 'canonical'), 'SEO component includes Open Graph and canonical tags');
    $assert(str_contains($layout, 'structured-data'), 'public layout supports JSON-LD structured data');
    $assert(str_contains($home, 'sm:') && str_contains($home, 'lg:'), 'home page includes responsive layout classes');
    $assert(str_contains($expensesView, 'md:') && str_contains($expensesView, 'lg:'), 'expense management view includes responsive layout classes');
    $assert(str_contains($reportsView, 'sm:') && str_contains($reportsView, 'lg:'), 'reports view includes responsive layout classes');

    $tables = Database::instance()->fetchAll(
        'SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ("expenses","orders","reviews","custom_gift_requests","admin_notifications")'
    );
    $found = array_column($tables, 'name');
    $assert(in_array('orders', $found, true) && in_array('reviews', $found, true), 'database connection validates core commerce/review tables');
    $assert(in_array('expenses', $found, true) || str_contains($schema, 'CREATE TABLE expenses'), 'expense table is present or ready to import from schema');
} catch (Throwable $exception) {
    $messages[] = 'FAIL exception: ' . $exception->getMessage();
    $failures[] = 'exception';
}

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
