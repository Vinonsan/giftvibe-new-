<?php

declare(strict_types=1);

/*
 * GiftVibe LK deployment configuration (production / cPanel).
 * For local XAMPP, create config.local.php — it overrides these values automatically.
 * Environment variables from the hosting panel override both files when set.
 */
$config = [
    'app_url' => getenv('APP_URL') ?: 'https://giftvibelk.lk',
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'riversid_giftvibelk_db',
        'user' => getenv('DB_USER') ?: 'riversid_giftvibelk_db',
        'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '64_~pgZ-wM}=(Qq.',
    ],
];

$localConfigFile = __DIR__ . '/config.local.php';
if (is_file($localConfigFile)) {
    $local = (array) require $localConfigFile;
    if (isset($local['database']) && is_array($local['database'])) {
        $config['database'] = array_replace($config['database'], $local['database']);
    }
    if (isset($local['app_url']) && is_string($local['app_url'])) {
        $config['app_url'] = $local['app_url'];
    }
}

return $config;
