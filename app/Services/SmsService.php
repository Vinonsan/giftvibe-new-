<?php

declare(strict_types=1);

namespace App\Services;

final class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $webhook = trim((string) getenv('SMS_WEBHOOK_URL'));
        if ($webhook === '' || !function_exists('curl_init')) {
            return false;
        }

        $handle = curl_init($webhook);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['phone' => $phone, 'message' => $message], JSON_THROW_ON_ERROR),
        ]);
        curl_exec($handle);
        $successful = curl_errno($handle) === 0 && (int) curl_getinfo($handle, CURLINFO_HTTP_CODE) < 300;
        curl_close($handle);

        return $successful;
    }
}
