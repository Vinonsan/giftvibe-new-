<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Controllers\Admin\CatalogController as AdminCatalogController;
use Controllers\Admin\CustomerController;
use Controllers\Admin\DashboardController;
use Controllers\Admin\DeliveryController;
use Controllers\Admin\ExpenseController;
use Controllers\Admin\MessageController;
use Controllers\Admin\NotificationController;
use Controllers\Admin\OrderController;
use Controllers\Admin\ReviewController;
use Controllers\Admin\SettingController;
use Controllers\AuthController;
use Controllers\Customer\CustomGiftRequestController;
use Controllers\PublicSite\CartController;
use Controllers\PublicSite\CatalogController as PublicCatalogController;
use Controllers\PublicSite\ContactController;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$render = static function (callable $callback): string {
    ob_start();
    $callback();
    return ob_get_clean();
};

try {
    $db = Database::instance();
    $product = $db->fetch('SELECT slug FROM products WHERE status = "active" ORDER BY id ASC LIMIT 1');
    if (!$product) {
        $db->execute(
            'INSERT INTO products (sku, name, slug, short_description, base_price, cost_price, stock_quantity, status, is_featured)
             VALUES ("AUDIT-2026", "Audit Gift Box", "audit-gift-box", "A storefront audit product.", 5000, 2500, 5, "active", 1)'
        );
        $productId = (int) $db->connection()->lastInsertId();
        $db->execute(
            'INSERT INTO product_images (product_id, image_path, alt_text, is_primary)
             VALUES (:product_id, "public/assets/images/hero_gift_box.jpg", "Audit gift product image", 1)',
            ['product_id' => $productId]
        );
        $product = ['slug' => 'audit-gift-box'];
    }

    $public = new PublicCatalogController();
    $publicPages = [
        'public home' => [$render(fn () => $public->home()), ['Shop curated gifts', 'Featured collection', 'New arrivals']],
        'public shop' => [$render(fn () => $public->shop()), ['Published catalog', 'Filter']],
        'public categories' => [$render(fn () => $public->categoriesPage()), ['Gift collections', 'Open collection']],
        'public product detail' => [$render(fn () => $public->product($product['slug'])), ['Add to Cart', 'Customer reviews']],
        'public about' => [$render(fn () => $public->about()), ['modern gifting studio', 'AboutPage']],
        'public contact' => [$render(fn () => (new ContactController())->show()), ['Talk to GiftVibe.lk', 'Send message']],
        'public cart' => [$render(fn () => (new CartController())->index()), ['Shopping Cart']],
        'public custom gifts' => [$render(fn () => (new CustomGiftRequestController())->create()), ['Request a personalised gift', 'How it works']],
        'customer login' => [$render(fn () => (new AuthController())->showCustomerLogin()), ['Customer Sign In']],
        'customer signup' => [$render(fn () => (new AuthController())->showCustomerSignup()), ['Create GiftVibe.lk Account']],
    ];

    foreach ($publicPages as $name => [$html, $needles]) {
        $assert(strlen($html) > 1000 && !str_contains($html, 'Page Not Found'), "{$name} renders substantial content");
        foreach ($needles as $needle) {
            $assert(str_contains($html, $needle), "{$name} contains {$needle}");
        }
    }

    Auth::login(['id' => 1, 'first_name' => 'GiftVibe', 'last_name' => 'Admin', 'email' => 'admin@giftvibe.lk', 'role_slug' => 'super_admin']);
    $_SESSION['admin_session_expires_at'] = time() + 3600;

    $adminPages = [
        'admin dashboard' => [$render(fn () => (new DashboardController())->index()), ['GiftVibe.lk Performance']],
        'admin products' => [$render(fn () => (new AdminCatalogController())->products()), ['Catalog control', 'Add product']],
        'admin categories' => [$render(fn () => (new AdminCatalogController())->categories()), ['Catalog map', 'Add category']],
        'admin orders' => [$render(fn () => (new OrderController())->index()), ['Orders']],
        'admin deliveries' => [$render(fn () => (new DeliveryController())->index()), ['Deliveries']],
        'admin reviews' => [$render(fn () => (new ReviewController())->index()), ['Product reviews']],
        'admin messages' => [$render(fn () => (new MessageController())->contacts()), ['Contact messages']],
        'admin requests' => [$render(fn () => (new MessageController())->requests()), ['Custom Gift Requests']],
        'admin notifications' => [$render(fn () => (new NotificationController())->index()), ['Notifications']],
        'admin expenses' => [$render(fn () => (new ExpenseController())->index()), ['Business expenses', 'Add expense']],
        'admin reports' => [$render(fn () => (new ExpenseController())->reports()), ['Profit report', 'Product profit']],
        'admin customers' => [$render(fn () => (new CustomerController())->index()), ['Customer intelligence']],
        'admin settings' => [$render(fn () => (new SettingController())->index()), ['Store configuration', 'Save settings']],
        'admin login' => [$render(fn () => (new AuthController())->showAdminLogin()), ['Admin Sign In']],
    ];

    foreach ($adminPages as $name => [$html, $needles]) {
        $assert(strlen($html) > 800 && !str_contains($html, 'Page Not Found'), "{$name} renders substantial content");
        foreach ($needles as $needle) {
            $assert(str_contains($html, $needle), "{$name} contains {$needle}");
        }
    }

    $topbar = file_get_contents(BASE_PATH . '/resources/views/admin/components/navigation/topbar.php') ?: '';
    $assert(!str_contains($topbar, 'View Site') && !str_contains($topbar, 'View Website'), 'admin panel has no public-home button');

    Auth::logout();
} catch (Throwable $exception) {
    $messages[] = 'FAIL exception: ' . $exception->getMessage();
    $failures[] = 'exception';
}

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
