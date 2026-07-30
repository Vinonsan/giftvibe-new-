<?php

namespace Services;

use App\Core\Auth;
use App\Core\Database;
use RuntimeException;

class CartService
{
    public function cart(): array
    {
        $db = Database::instance();
        $sessionId = session_id();
        $userId = Auth::isCustomer() ? Auth::id() : null;

        if ($userId) {
            $cart = $db->fetch(
                'SELECT * FROM carts WHERE status = "active" AND (user_id = :user_id OR session_id = :session_id) ORDER BY user_id DESC, updated_at DESC LIMIT 1',
                ['user_id' => $userId, 'session_id' => $sessionId]
            );
            if ($cart) {
                $db->execute('UPDATE carts SET user_id = :user_id, session_id = :session_id WHERE id = :id', [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'id' => $cart['id'],
                ]);
                $cart['user_id'] = $userId;
                $cart['session_id'] = $sessionId;
                return $cart;
            }
        } else {
            $cart = $db->fetch(
                'SELECT * FROM carts WHERE status = "active" AND session_id = :session_id LIMIT 1',
                ['session_id' => $sessionId]
            );
            if ($cart) {
                return $cart;
            }
        }

        $db->execute(
            'INSERT INTO carts (user_id, session_id, status) VALUES (:user_id, :session_id, "active")',
            ['user_id' => $userId, 'session_id' => $sessionId]
        );

        return $db->fetch('SELECT * FROM carts WHERE id = LAST_INSERT_ID()') ?? [];
    }

    public function items(): array
    {
        $cart = $this->cart();
        return Database::instance()->fetchAll(
            'SELECT cart_items.*,
                    products.name AS product_name,
                    products.slug,
                    products.sku AS product_sku,
                    products.base_price,
                    products.sale_price,
                    products.cost_price,
                    products.stock_quantity AS product_stock,
                    products.status AS product_status,
                    product_variants.name AS variant_name,
                    product_variants.sku AS variant_sku,
                    product_variants.color_name,
                    product_variants.color_hex,
                    product_variants.stock_quantity AS variant_stock,
                    COALESCE(product_variants.image_path, primary_image.image_path, "") AS image_path
             FROM cart_items
             INNER JOIN products ON products.id = cart_items.product_id
             LEFT JOIN product_variants ON product_variants.id = cart_items.variant_id
             LEFT JOIN product_images primary_image ON primary_image.product_id = products.id AND primary_image.is_primary = 1
             WHERE cart_items.cart_id = :cart_id
             ORDER BY cart_items.created_at DESC',
            ['cart_id' => $cart['id']]
        );
    }

    public function add(int $productId, ?int $variantId, int $quantity, string $giftMessage = ''): void
    {
        $quantity = max(1, $quantity);
        $product = $this->publishedProduct($productId);
        $variant = $variantId ? $this->variant($productId, $variantId) : null;
        $available = $variant ? (int) $variant['stock_quantity'] : (int) $product['stock_quantity'];

        if ($quantity > $available) {
            throw new RuntimeException('Requested quantity exceeds available stock.');
        }

        $unitPrice = (float) ($product['sale_price'] ?: $product['base_price']) + (float) ($variant['price_adjustment'] ?? 0);
        $cart = $this->cart();
        $existing = Database::instance()->fetch(
            'SELECT * FROM cart_items
             WHERE cart_id = :cart_id
               AND product_id = :product_id
               AND ((variant_id = :variant_id_one) OR (variant_id IS NULL AND :variant_id_two IS NULL))
             LIMIT 1',
            ['cart_id' => $cart['id'], 'product_id' => $productId, 'variant_id_one' => $variantId, 'variant_id_two' => $variantId]
        );

        if ($existing) {
            $newQuantity = (int) $existing['quantity'] + $quantity;
            if ($newQuantity > $available) {
                throw new RuntimeException('Cart quantity exceeds available stock.');
            }
            Database::instance()->execute(
                'UPDATE cart_items SET quantity = :quantity, unit_price = :unit_price, gift_message = :gift_message WHERE id = :id',
                ['quantity' => $newQuantity, 'unit_price' => $unitPrice, 'gift_message' => $giftMessage, 'id' => $existing['id']]
            );
            return;
        }

        Database::instance()->execute(
            'INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, unit_price, gift_message)
             VALUES (:cart_id, :product_id, :variant_id, :quantity, :unit_price, :gift_message)',
            [
                'cart_id' => $cart['id'],
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'gift_message' => $giftMessage,
            ]
        );
    }

    public function update(int $itemId, int $quantity): void
    {
        $quantity = max(1, $quantity);
        $item = $this->ownedItem($itemId);
        $available = $item['variant_id'] ? (int) $item['variant_stock'] : (int) $item['product_stock'];
        if ($quantity > $available) {
            throw new RuntimeException('Requested quantity exceeds available stock.');
        }
        Database::instance()->execute(
            'UPDATE cart_items SET quantity = :quantity WHERE id = :id',
            ['quantity' => $quantity, 'id' => $itemId]
        );
    }

    public function remove(int $itemId): void
    {
        $item = $this->ownedItem($itemId);
        Database::instance()->execute('DELETE FROM cart_items WHERE id = :id', ['id' => $item['id']]);
    }

    public function validateStock(): void
    {
        foreach ($this->items() as $item) {
            if ($item['product_status'] !== 'active') {
                throw new RuntimeException($item['product_name'] . ' is no longer published.');
            }
            $available = $item['variant_id'] ? (int) $item['variant_stock'] : (int) $item['product_stock'];
            if ((int) $item['quantity'] > $available) {
                throw new RuntimeException($item['product_name'] . ' does not have enough stock.');
            }
        }
    }

    public function totals(?array $address = null): array
    {
        $subtotal = 0.0;
        foreach ($this->items() as $item) {
            $subtotal += (float) $item['unit_price'] * (int) $item['quantity'];
        }
        $deliveryFee = $this->deliveryFee($address, $subtotal);
        return [
            'subtotal' => $subtotal,
            'discount_total' => 0.0,
            'tax_total' => 0.0,
            'delivery_fee' => $deliveryFee,
            'grand_total' => $subtotal + $deliveryFee,
        ];
    }

    public function clear(): void
    {
        $cart = $this->cart();
        Database::instance()->execute('DELETE FROM cart_items WHERE cart_id = :cart_id', ['cart_id' => $cart['id']]);
        Database::instance()->execute('UPDATE carts SET status = "converted" WHERE id = :id', ['id' => $cart['id']]);
    }

    public function deliveryFee(?array $address, float $subtotal): float
    {
        if (!$address) {
            return 450.0;
        }

        $zone = Database::instance()->fetch(
            'SELECT * FROM delivery_zones
             WHERE status = "active"
               AND district = :district
               AND (city = :city OR city IS NULL OR city = "")
             ORDER BY city DESC
             LIMIT 1',
            ['district' => $address['district'], 'city' => $address['city']]
        );

        if (!$zone) {
            return 450.0;
        }

        if ($subtotal >= (float) $zone['minimum_order_amount']) {
            return (float) $zone['fee'];
        }

        return (float) $zone['fee'];
    }

    private function publishedProduct(int $productId): array
    {
        $product = Database::instance()->fetch(
            'SELECT * FROM products WHERE id = :id AND status = "active" LIMIT 1',
            ['id' => $productId]
        );
        if (!$product) {
            throw new RuntimeException('Product is not available.');
        }
        return $product;
    }

    private function variant(int $productId, int $variantId): array
    {
        $variant = Database::instance()->fetch(
            'SELECT * FROM product_variants WHERE id = :id AND product_id = :product_id AND status = "active" LIMIT 1',
            ['id' => $variantId, 'product_id' => $productId]
        );
        if (!$variant) {
            throw new RuntimeException('Selected variant is not available.');
        }
        return $variant;
    }

    private function ownedItem(int $itemId): array
    {
        $cart = $this->cart();
        $item = Database::instance()->fetch(
            'SELECT cart_items.*,
                    products.stock_quantity AS product_stock,
                    product_variants.stock_quantity AS variant_stock
             FROM cart_items
             INNER JOIN products ON products.id = cart_items.product_id
             LEFT JOIN product_variants ON product_variants.id = cart_items.variant_id
             WHERE cart_items.id = :id AND cart_items.cart_id = :cart_id
             LIMIT 1',
            ['id' => $itemId, 'cart_id' => $cart['id']]
        );
        if (!$item) {
            throw new RuntimeException('Cart item was not found.');
        }
        return $item;
    }
}
