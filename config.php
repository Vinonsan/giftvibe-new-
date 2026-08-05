<?php

declare(strict_types=1);

/*
 * GiftVibe LK deployment configuration.
 * Environment variables override these values when the hosting panel provides them.
 */
return [
    'app_url' => getenv('APP_URL') ?: 'https://giftvibelk.lk',
    'database' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'riversid_dbof-giftvibelk',
        'user' => getenv('DB_USER') ?: 'riversid_dbof-giftvibelk',
        'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '~DW-pQ0pH}h~Uq0O',
    ],
];
