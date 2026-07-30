<?php

namespace Services;

class DevelopmentSmsGateway implements SmsGateway
{
    public array $sentMessages = [];

    public function send(string $recipient, string $message): bool
    {
        $this->sentMessages[] = [
            'recipient' => $recipient,
            'message' => $message,
            'sent_at' => date('c'),
        ];

        return true;
    }
}
