<?php

declare(strict_types=1);

namespace App\Sms\Messages;

final class AdminNewOrderMessage
{
    /** @param array<int,array<string,mixed>> $items */
    public static function build(string $customerName, array $items, string $paymentMethod, float $total): string
    {
        return sprintf(
            'GiftVibeLK new order from %s. Products: %s. Payment: %s. Total: LKR %s.',
            trim($customerName) ?: 'Customer',
            self::products($items),
            self::paymentLabel($paymentMethod),
            number_format($total, 2, '.', '')
        );
    }

    /** @param array<int,array<string,mixed>> $items */
    private static function products(array $items): string
    {
        $labels = [];
        foreach (array_slice($items, 0, 3) as $item) {
            $name = trim((string) ($item['product_name'] ?? $item['name'] ?? 'Product'));
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $labels[] = $name . ($quantity > 1 ? ' x' . $quantity : '');
        }
        $remaining = count($items) - count($labels);
        return implode(', ', $labels) . ($remaining > 0 ? ' +' . $remaining . ' more' : '');
    }

    private static function paymentLabel(string $method): string
    {
        return $method === 'bank_deposit' ? 'Bank deposit' : 'Cash on delivery';
    }
}
