<?php

declare(strict_types=1);

namespace App\Services;

final class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $smsLenzEnabled = filter_var(getenv('SMSLENZ_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN);
        if ($smsLenzEnabled) {
            return self::sendViaSmsLenz($phone, $message);
        }

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

    private static function sendViaSmsLenz(string $phone, string $message): bool
    {
        $baseUrl = rtrim(trim((string) getenv('SMSLENZ_API_URL')), '/');
        $userId = trim((string) getenv('SMSLENZ_USER_ID'));
        $apiKey = trim((string) getenv('SMSLENZ_API_KEY'));
        $senderId = trim((string) getenv('SMSLENZ_SENDER_ID'));
        $contact = self::normalizeSriLankanPhone($phone);

        if ($baseUrl === '' || $userId === '' || $apiKey === '' || $senderId === '' || $contact === '' || trim($message) === '' || !function_exists('curl_init')) {
            return false;
        }

        $endpoint = str_ends_with($baseUrl, '/send-sms') ? $baseUrl : $baseUrl . '/send-sms';
        $handle = curl_init($endpoint);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => max(1, (int) (getenv('SMS_TIMEOUT_SECONDS') ?: 10)),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'user_id' => $userId,
                'api_key' => $apiKey,
                'sender_id' => $senderId,
                'contact' => $contact,
                'message' => mb_substr(trim($message), 0, 1500),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]);
        $response = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $curlOk = curl_errno($handle) === 0;
        curl_close($handle);

        if (!$curlOk || $status < 200 || $status >= 300 || !is_string($response)) {
            return false;
        }
        $result = json_decode($response, true);
        return is_array($result) && ($result['success'] ?? false) === true;
    }

    private static function normalizeSriLankanPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phone)) ?? '';
        if (str_starts_with($phone, '+94')) return $phone;
        if (str_starts_with($phone, '94')) return '+' . $phone;
        if (str_starts_with($phone, '0')) return '+94' . substr($phone, 1);
        if (strlen($phone) === 9) return '+94' . $phone;
        return '';
    }
}
