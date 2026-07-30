<?php

namespace Controllers\PublicSite;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use Services\AdminNotificationService;

class ContactController
{
    public function show(?string $message = null, ?string $error = null): void
    {
        View::renderPage('public/pages/contact', [
            'title' => 'Contact GiftVibe.lk',
            'navigation' => $this->navigation('contact'),
            'message' => $message,
            'error' => $error,
            'meta' => [
                'description' => 'Contact GiftVibe.lk for gifting help, order support, and custom gift requests.',
                'url' => url('/contact'),
            ],
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contact GiftVibe.lk',
                'url' => url('/contact'),
            ],
        ]);
    }

    public function store(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->show(null, 'Your session expired. Try again.');
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $body = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $body === '') {
            $this->show(null, 'Name, valid email, and message are required.');
            return;
        }

        Database::instance()->execute(
            'INSERT INTO contact_messages (name, email, phone, subject, message)
             VALUES (:name, :email, :phone, :subject, :message)',
            [
                'name' => $name,
                'email' => $email,
                'phone' => trim((string) ($_POST['phone'] ?? '')),
                'subject' => trim((string) ($_POST['subject'] ?? 'GiftVibe.lk enquiry')),
                'message' => $body,
            ]
        );
        $id = (int) Database::instance()->connection()->lastInsertId();
        AdminNotificationService::create('contact', 'New contact message', $name . ' sent a message.', 'contact', $id);

        $this->show('Thanks, your message has been sent.');
    }

    private function navigation(string $active): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => false],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => false],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => false],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => false],
            ['label' => 'About', 'url' => url('/about'), 'active' => false],
            ['label' => 'Contact', 'url' => url('/contact'), 'active' => $active === 'contact'],
            ['label' => Auth::isCustomer() ? 'My Portal' : 'Login', 'url' => Auth::isCustomer() ? url('/customer/dashboard') : url('/login'), 'active' => false],
        ];
    }
}
