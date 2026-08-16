<?php

declare(strict_types=1);

namespace App\Services;

final class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $endpoint = trim((string) (getenv('SMS_API_URL') ?: getenv('SMS_WEBHOOK_URL')));
        $apiKey = trim((string) getenv('SMS_API_KEY'));
        $senderId = trim((string) getenv('SMS_SENDER_ID'));
        $userId = trim((string) getenv('SMS_USER_ID'));
        $authMode = strtolower(trim((string) (getenv('SMS_AUTH_MODE') ?: 'header')));
        $apiKeyHeader = trim((string) (getenv('SMS_API_KEY_HEADER') ?: 'Authorization'));
        $apiKeyPrefix = trim((string) (getenv('SMS_API_KEY_PREFIX') ?: 'Bearer'));
        $enabled = filter_var(getenv('SMS_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN);
        if (!$enabled || $endpoint === '' || $apiKey === '' || $senderId === '' || !function_exists('curl_init')) {
            return false;
        }

        $headers = ['Content-Type: application/json'];
        if ($authMode === 'header') {
            $authorization = trim($apiKeyPrefix . ' ' . $apiKey);
            $headers[] = $apiKeyHeader . ': ' . $authorization;
        }
        $payload = [
            'recipient' => $phone,
            'message' => $message,
            'sender_id' => $senderId,
        ];
        if ($authMode === 'body') $payload['api_key'] = $apiKey;
        if ($userId !== '') $payload['user_id'] = $userId;

        $handle = curl_init($endpoint);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => max(1, (int) (getenv('SMS_TIMEOUT_SECONDS') ?: 10)),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);
        curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $successful = curl_errno($handle) === 0 && $status >= 200 && $status < 300;
        curl_close($handle);

        return $successful;
    }
}
