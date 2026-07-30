<?php
namespace Services;

class SMSService
{
    public static function sendSMS($recipient, $message): array
    {
        $gateway = new DevelopmentSmsGateway();
        $sent = $gateway->send((string) $recipient, (string) $message);

        return ['success' => $sent, 'response' => ['status' => $sent ? 'sent' : 'failed']];
    }
}
