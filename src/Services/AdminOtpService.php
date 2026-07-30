<?php

namespace Services;

class AdminOtpService
{
    private array $config;
    private SmsGateway $gateway;

    public function __construct(?SmsGateway $gateway = null)
    {
        $this->config = require CONFIG_PATH . '/admin.php';
        $this->gateway = $gateway ?? new DevelopmentSmsGateway();
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '94') && strlen($digits) === 11) {
            return '0' . substr($digits, 2);
        }

        return $digits;
    }

    public function isAuthorizedPhone(string $phone): bool
    {
        return in_array($this->normalizePhone($phone), $this->config['authorized_phones'], true);
    }

    public function issue(string $phone): array
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $otp = (string) $this->config['development_otp'];
        $expiresAt = time() + ((int) $this->config['otp_ttl_minutes'] * 60);

        $_SESSION['admin_otp_challenge'] = [
            'phone' => $normalizedPhone,
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'expires_at' => $expiresAt,
            'attempts' => 0,
        ];

        $this->gateway->send($normalizedPhone, "Your GiftVibe.lk admin OTP is {$otp}.");

        return [
            'phone' => $normalizedPhone,
            'expires_at' => $expiresAt,
        ];
    }

    public function verify(string $otp): bool
    {
        $challenge = $_SESSION['admin_otp_challenge'] ?? null;
        if (!$challenge || time() > (int) $challenge['expires_at']) {
            unset($_SESSION['admin_otp_challenge']);
            return false;
        }

        $challenge['attempts'] = ((int) ($challenge['attempts'] ?? 0)) + 1;
        $_SESSION['admin_otp_challenge'] = $challenge;

        if ($challenge['attempts'] > 5) {
            unset($_SESSION['admin_otp_challenge']);
            return false;
        }

        return password_verify(trim($otp), $challenge['otp_hash']);
    }

    public function pendingPhone(): ?string
    {
        $challenge = $_SESSION['admin_otp_challenge'] ?? null;
        if (!$challenge || time() > (int) $challenge['expires_at']) {
            unset($_SESSION['admin_otp_challenge']);
            return null;
        }

        return $challenge['phone'];
    }

    public function consume(): ?string
    {
        $phone = $_SESSION['admin_otp_challenge']['phone'] ?? null;
        unset($_SESSION['admin_otp_challenge']);

        return $phone;
    }
}
