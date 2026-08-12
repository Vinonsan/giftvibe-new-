<?php

declare(strict_types=1);

$environmentFile = __DIR__ . '/.env';
if (is_file($environmentFile) && is_readable($environmentFile)) {
    foreach (file($environmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key === '' || getenv($key) !== false) {
            continue;
        }
        if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

/*
 * GiftVibe LK deployment configuration (production / cPanel).
 * For local XAMPP, create config.local.php — it overrides these values automatically.
 * Environment variables from the hosting panel override both files when set.
 */
$config = [
    'app_url' => getenv('APP_URL') ?: 'https://giftvibelk.lk',
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'riversid_giftvibe_lk_db',
        'user' => getenv('DB_USER') ?: 'riversid_giftvibe_lk_db',
        'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
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
