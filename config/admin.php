<?php

return [
    'authorized_phones' => [
        '0758311995',
        '0754476969',
    ],
    'development_otp' => env('ADMIN_DEV_OTP', '000000'),
    'otp_ttl_minutes' => 5,
    'session_ttl_minutes' => 120,
];
