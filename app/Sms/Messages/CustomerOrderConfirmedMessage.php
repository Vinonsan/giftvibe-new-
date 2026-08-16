<?php

declare(strict_types=1);

namespace App\Sms\Messages;

final class CustomerOrderConfirmedMessage
{
    /** @param array<int,array<string,mixed>> $items */
    public static function build(array $items, string $paymentMethod, float $paidAmount, float $balanceDue): string
    {
        return sprintf(
            'GiftVibeLK: Your order is confirmed. Products: %s. Payment: %s. Paid: LKR %s. Balance to pay: LKR %s.',
            self::products($items),
            $paymentMethod === 'bank_deposit' ? 'Bank deposit' : 'Cash on delivery',
            number_format($paidAmount, 2, '.', ''),
            number_format($balanceDue, 2, '.', '')
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
}
