<?php

require __DIR__ . '/../config/config.php';

use App\Core\Auth;
use App\Core\Database;
use Services\AdminOtpService;

$failures = [];
$messages = [];
$assert = static function (bool $condition, string $label) use (&$failures, &$messages): void {
    $messages[] = ($condition ? 'PASS ' : 'FAIL ') . $label;
    if (!$condition) {
        $failures[] = $label;
    }
};

$otpService = new AdminOtpService();

$assert($otpService->isAuthorizedPhone('0758311995'), 'authorized phone 0758311995');
$assert($otpService->isAuthorizedPhone('0754476969'), 'authorized phone 0754476969');
$assert(!$otpService->isAuthorizedPhone('0711111111'), 'unauthorized phone rejected');

$otpService->issue('+94 75 831 1995');
$assert($otpService->pendingPhone() === '0758311995', 'phone normalized and OTP challenge created');
$assert(!$otpService->verify('111111'), 'wrong OTP rejected');
$assert($otpService->verify('000000'), 'development OTP accepted');

$phone = $otpService->consume();
$user = Database::instance()->fetch(
    'SELECT users.*, roles.slug AS role_slug
     FROM users
     INNER JOIN roles ON roles.id = users.role_id
     WHERE users.email = :email
     LIMIT 1',
    ['email' => 'admin@giftvibe.lk']
);

Auth::loginAdminByPhone($phone ?? '0758311995', $user);
$assert(Auth::isStaff(), 'admin OTP session grants staff access');

$_SESSION['admin_session_expires_at'] = time() - 1;
$assert(!Auth::isStaff(), 'expired admin session is rejected');

Auth::logout();
$assert(!Auth::check(), 'admin logout clears authenticated user');

$dashboard = Database::instance()->fetch(
    'SELECT COUNT(*) AS total_orders, COALESCE(SUM(grand_total),0) AS sales FROM orders'
);
$assert(is_array($dashboard) && array_key_exists('total_orders', $dashboard), 'dashboard summary query works');

foreach ($messages as $message) {
    echo $message . PHP_EOL;
}

exit($failures ? 1 : 0);
