<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use Controllers\Admin\CatalogController as AdminCatalogController;
use Controllers\Admin\CustomerController;
use Controllers\Admin\SettingController;
use Controllers\PublicSite\CatalogController as PublicCatalogController;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

try {
    Auth::login(['id' => 1, 'first_name' => 'GiftVibe', 'last_name' => 'Admin', 'email' => 'admin@giftvibe.lk', 'role_slug' => 'super_admin']);
    $_SESSION['admin_session_expires_at'] = time() + 3600;

    $routes = file_get_contents(BASE_PATH . '/routes/admin.php') ?: '';
    foreach (['/products', '/categories', '/customers', '/settings'] as $path) {
        $assert(str_contains($routes, "'path' => '{$path}'"), "admin route {$path} is implemented");
    }

    $topbar = file_get_contents(BASE_PATH . '/resources/views/admin/components/navigation/topbar.php') ?: '';
    $assert(!str_contains($topbar, 'View Site') && !str_contains($topbar, 'View Website'), 'admin topbar public home button removed');

    ob_start();
    (new AdminCatalogController())->products();
    $productsHtml = ob_get_clean();
    $assert(str_contains($productsHtml, 'Add product') && str_contains($productsHtml, 'Catalog control'), 'admin products page renders management UI');

    ob_start();
    (new AdminCatalogController())->categories();
    $categoriesHtml = ob_get_clean();
    $assert(str_contains($categoriesHtml, 'Add category') && str_contains($categoriesHtml, 'Catalog map'), 'admin categories page renders management UI');

    ob_start();
    (new CustomerController())->index();
    $customersHtml = ob_get_clean();
    $assert(str_contains($customersHtml, 'Customer intelligence'), 'admin customers page renders');

    ob_start();
    (new SettingController())->index();
    $settingsHtml = ob_get_clean();
    $assert(str_contains($settingsHtml, 'Store configuration') && str_contains($settingsHtml, 'Save settings'), 'admin settings page renders editable settings');

    ob_start();
    (new PublicCatalogController())->about();
    $aboutHtml = ob_get_clean();
    $assert(str_contains($aboutHtml, 'modern gifting studio') && str_contains($aboutHtml, 'AboutPage'), 'public about page renders modern SEO-ready content');

    $schema = file_get_contents(BASE_PATH . '/database/schema.sql') ?: '';
    $seed = file_get_contents(BASE_PATH . '/database/sample_catalog_seed.sql') ?: '';
    $assert(str_contains($schema, 'Birthday Glow Gift Box') && str_contains($seed, 'Luxe Celebration Hamper'), 'schema and seed include starter product/category data');

    Auth::logout();
} catch (Throwable $exception) {
    $messages[] = 'FAIL exception: ' . $exception->getMessage();
    $failures[] = 'exception';
}

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
