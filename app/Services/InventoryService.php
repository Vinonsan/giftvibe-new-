<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

final class InventoryService
{
    public static function ensureSchema(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS inventory_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NULL,
                name VARCHAR(190) NOT NULL,
                sku VARCHAR(80) NULL,
                quantity INT NOT NULL DEFAULT 0,
                unit VARCHAR(40) NULL DEFAULT 'pcs',
                cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                low_stock_threshold INT NOT NULL DEFAULT 5,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_inventory_product (product_id),
                KEY idx_inventory_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS inventory_movements (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                inventory_item_id BIGINT UNSIGNED NOT NULL,
                change_qty INT NOT NULL,
                movement_type ENUM('purchase','adjustment','sale','expense') NOT NULL DEFAULT 'purchase',
                expense_id BIGINT UNSIGNED NULL,
                note VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_inventory_movements_item (inventory_item_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        try {
            $pdo->query('SELECT inventory_item_id FROM expenses LIMIT 1');
        } catch (\PDOException) {
            try {
                $pdo->exec('ALTER TABLE expenses ADD inventory_item_id BIGINT UNSIGNED NULL AFTER order_id');
                $pdo->exec('ALTER TABLE expenses ADD stock_quantity INT UNSIGNED NULL DEFAULT NULL AFTER inventory_item_id');
            } catch (\PDOException) {
                // Columns may already exist.
            }
        }
    }

    /** @return list<array<string, mixed>> */
    public static function listItems(PDO $pdo): array
    {
        self::ensureSchema($pdo);
        self::syncCatalogProducts($pdo);

        return $pdo->query(
            "SELECT i.*,
                p.slug AS product_slug,
                p.status AS product_status,
                CASE WHEN i.product_id IS NOT NULL THEN 'catalog' ELSE 'stock' END AS source_type
             FROM inventory_items i
             LEFT JOIN products p ON p.id = i.product_id
             ORDER BY i.product_id IS NOT NULL DESC, i.name ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function syncCatalogProducts(PDO $pdo): void
    {
        $products = $pdo->query(
            "SELECT id, name, sku, stock_quantity, cost_price, low_stock_threshold
             FROM products WHERE status IN ('active','inactive','draft')"
        )->fetchAll(PDO::FETCH_ASSOC);

        $upsert = $pdo->prepare(
            'INSERT INTO inventory_items (product_id, name, sku, quantity, cost_price, low_stock_threshold)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                sku = VALUES(sku),
                quantity = VALUES(quantity),
                cost_price = VALUES(cost_price),
                low_stock_threshold = VALUES(low_stock_threshold)'
        );

        foreach ($products as $product) {
            $upsert->execute([
                (int) $product['id'],
                (string) $product['name'],
                (string) ($product['sku'] ?? ''),
                (int) $product['stock_quantity'],
                (float) ($product['cost_price'] ?? 0),
                (int) ($product['low_stock_threshold'] ?? 5),
            ]);
        }
    }

    /**
     * @param array{title: string, quantity: int, product_id?: int|null, inventory_item_id?: int|null, cost_per_unit?: float, expense_id?: int|null} $data
     * @return int Inventory item id that received stock
     */
    public static function receiveFromExpense(PDO $pdo, array $data): int
    {
        self::ensureSchema($pdo);

        $qty = max(1, (int) ($data['quantity'] ?? 1));
        $productId = (int) ($data['product_id'] ?? 0) ?: null;
        $inventoryItemId = (int) ($data['inventory_item_id'] ?? 0) ?: null;
        $title = trim((string) ($data['title'] ?? ''));
        $costPerUnit = max(0, (float) ($data['cost_per_unit'] ?? 0));
        $expenseId = (int) ($data['expense_id'] ?? 0) ?: null;

        if ($inventoryItemId) {
            self::adjustQuantity($pdo, $inventoryItemId, $qty, 'expense', $expenseId, 'Stock from expense');
            return $inventoryItemId;
        }

        if ($productId) {
            $itemId = self::findOrCreateForProduct($pdo, $productId);
            self::adjustQuantity($pdo, $itemId, $qty, 'expense', $expenseId, 'Stock from expense');
            if ($costPerUnit > 0) {
                $pdo->prepare('UPDATE products SET cost_price = ? WHERE id = ?')->execute([$costPerUnit, $productId]);
                $pdo->prepare('UPDATE inventory_items SET cost_price = ? WHERE id = ?')->execute([$costPerUnit, $itemId]);
            }
            return $itemId;
        }

        if ($title === '') {
            return 0;
        }

        $pdo->prepare(
            'INSERT INTO inventory_items (name, quantity, cost_price, unit, notes) VALUES (?, ?, ?, ?, ?)'
        )->execute([$title, $qty, $costPerUnit, 'pcs', 'Added from expense']);

        $newId = (int) $pdo->lastInsertId();
        self::recordMovement($pdo, $newId, $qty, 'expense', $expenseId, 'Initial stock from expense');
        return $newId;
    }

    public static function saveStockItem(PDO $pdo, array $data): int
    {
        self::ensureSchema($pdo);

        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $sku = trim((string) ($data['sku'] ?? ''));
        $quantity = max(0, (int) ($data['quantity'] ?? 0));
        $costPrice = max(0, (float) ($data['cost_price'] ?? 0));
        $unit = trim((string) ($data['unit'] ?? 'pcs')) ?: 'pcs';
        $lowStock = max(0, (int) ($data['low_stock_threshold'] ?? 5));
        $notes = trim((string) ($data['notes'] ?? ''));

        if ($name === '') {
            throw new \InvalidArgumentException('Item name is required.');
        }

        if ($id > 0) {
            $row = $pdo->prepare('SELECT product_id FROM inventory_items WHERE id = ? LIMIT 1');
            $row->execute([$id]);
            $existing = $row->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                throw new \InvalidArgumentException('Inventory item not found.');
            }
            if (!empty($existing['product_id'])) {
                throw new \InvalidArgumentException('Catalog products must be edited from Products page. Use Adjust stock here.');
            }

            $pdo->prepare(
                'UPDATE inventory_items SET name = ?, sku = ?, quantity = ?, cost_price = ?, unit = ?, low_stock_threshold = ?, notes = ? WHERE id = ?'
            )->execute([$name, $sku ?: null, $quantity, $costPrice, $unit, $lowStock, $notes ?: null, $id]);

            return $id;
        }

        $pdo->prepare(
            'INSERT INTO inventory_items (name, sku, quantity, cost_price, unit, low_stock_threshold, notes) VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([$name, $sku ?: null, $quantity, $costPrice, $unit, $lowStock, $notes ?: null]);

        return (int) $pdo->lastInsertId();
    }

    public static function adjustQuantity(PDO $pdo, int $itemId, int $change, string $type = 'adjustment', ?int $expenseId = null, ?string $note = null): void
    {
        $stmt = $pdo->prepare('SELECT id, product_id, quantity FROM inventory_items WHERE id = ? LIMIT 1');
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) {
            throw new \InvalidArgumentException('Inventory item not found.');
        }

        $newQty = max(0, (int) $item['quantity'] + $change);
        $pdo->prepare('UPDATE inventory_items SET quantity = ? WHERE id = ?')->execute([$newQty, $itemId]);

        if (!empty($item['product_id'])) {
            $pdo->prepare('UPDATE products SET stock_quantity = ? WHERE id = ?')->execute([$newQty, (int) $item['product_id']]);
        }

        self::recordMovement($pdo, $itemId, $change, $type, $expenseId, $note);
    }

    private static function findOrCreateForProduct(PDO $pdo, int $productId): int
    {
        $stmt = $pdo->prepare('SELECT id FROM inventory_items WHERE product_id = ? LIMIT 1');
        $stmt->execute([$productId]);
        $id = $stmt->fetchColumn();
        if ($id) {
            return (int) $id;
        }

        $product = $pdo->prepare('SELECT name, sku, stock_quantity, cost_price, low_stock_threshold FROM products WHERE id = ? LIMIT 1');
        $product->execute([$productId]);
        $row = $product->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new \InvalidArgumentException('Product not found.');
        }

        $pdo->prepare(
            'INSERT INTO inventory_items (product_id, name, sku, quantity, cost_price, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?)'
        )->execute([
            $productId,
            (string) $row['name'],
            (string) ($row['sku'] ?? ''),
            (int) $row['stock_quantity'],
            (float) ($row['cost_price'] ?? 0),
            (int) ($row['low_stock_threshold'] ?? 5),
        ]);

        return (int) $pdo->lastInsertId();
    }

    private static function recordMovement(PDO $pdo, int $itemId, int $change, string $type, ?int $expenseId, ?string $note): void
    {
        $pdo->prepare(
            'INSERT INTO inventory_movements (inventory_item_id, change_qty, movement_type, expense_id, note) VALUES (?, ?, ?, ?, ?)'
        )->execute([$itemId, $change, $type, $expenseId, $note]);
    }
}
