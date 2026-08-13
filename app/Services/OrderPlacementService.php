<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use Throwable;

final class OrderPlacementService
{
    /** @return list<array<string, mixed>> */
    public static function resolveFromSelection(PDO $pdo, string $selection): array
    {
        $requested = [];
        foreach (array_slice(explode(',', $selection), 0, 20) as $entry) {
            [$typedSlug, $quantity] = array_pad(explode(':', $entry, 2), 2, '1');
            [$type, $slugWithVariant] = str_contains($typedSlug, '~')
                ? array_pad(explode('~', $typedSlug, 2), 2, '')
                : ['p', $typedSlug];
            [$slug, $variantId] = array_pad(explode('@', $slugWithVariant, 2), 2, '0');
            if (in_array($type, ['p', 'c'], true) && preg_match('/^[a-z0-9-]+$/', $slug)) {
                $requested[$type . '~' . $slug . '@' . max(0, (int) $variantId)] = max(1, min(10, (int) $quantity));
            }
        }

        if ($requested === []) {
            return [];
        }

        $items = [];
        $productStmt = $pdo->prepare(
            "SELECT id, name, slug, sku, base_price, cost_price, stock_quantity
             FROM products WHERE slug = ? AND status = 'active' LIMIT 1"
        );
        $comboStmt = $pdo->prepare(
            "SELECT id, name, slug, CONCAT('COMBO-', id) AS sku, price AS base_price, other_cost, 1 AS stock_quantity
             FROM combos WHERE slug = ? AND status = 'active' LIMIT 1"
        );

        foreach ($requested as $typedSlug => $quantity) {
            [$type, $slugWithVariant] = explode('~', $typedSlug, 2);
            [$slug, $variantId] = array_pad(explode('@', $slugWithVariant, 2), 2, '0');
            $stmt = $type === 'c' ? $comboStmt : $productStmt;
            $stmt->execute([$slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                continue;
            }

            $normalized = self::normalizeItem($pdo, $row, $type === 'c' ? 'combo' : 'product', $quantity);
            if ($type === 'p' && (int) $variantId < 1) {
                $hasVariants = $pdo->prepare("SELECT 1 FROM product_variants WHERE product_id=? AND status='active' LIMIT 1");
                $hasVariants->execute([(int) $row['id']]);
                if ($hasVariants->fetchColumn()) continue;
            }
            if ($type === 'p' && (int) $variantId > 0) {
                $variantStmt = $pdo->prepare("SELECT id,name,sku,color_name,price_adjustment,stock_quantity FROM product_variants WHERE id=? AND product_id=? AND status='active' LIMIT 1");
                $variantStmt->execute([(int) $variantId, (int) $row['id']]);
                $variant = $variantStmt->fetch(PDO::FETCH_ASSOC);
                if (!$variant || (int) $variant['stock_quantity'] < $quantity) continue;
                $normalized['variant_id'] = (int) $variant['id'];
                $normalized['variant_name'] = (string) ($variant['color_name'] ?: $variant['name']);
                $normalized['sku'] = (string) $variant['sku'];
                $normalized['base_price'] = round((float) $row['base_price'] + (float) $variant['price_adjustment'], 2);
                $normalized['line_total'] = round($normalized['base_price'] * $quantity, 2);
            }
            $items[] = $normalized;
        }

        return $items;
    }

    /**
     * @param list<array{type: string, id: int, qty?: int, quantity?: int}> $lineItems
     * @return list<array<string, mixed>>
     */
    public static function resolveFromLineItems(PDO $pdo, array $lineItems): array
    {
        $items = [];
        $productStmt = $pdo->prepare(
            "SELECT id, name, slug, sku, base_price, cost_price, stock_quantity
             FROM products WHERE id = ? AND status = 'active' LIMIT 1"
        );
        $comboStmt = $pdo->prepare(
            "SELECT id, name, slug, CONCAT('COMBO-', id) AS sku, price AS base_price, other_cost, 1 AS stock_quantity
             FROM combos WHERE id = ? AND status = 'active' LIMIT 1"
        );

        foreach (array_slice($lineItems, 0, 20) as $line) {
            $type = (string) ($line['type'] ?? '');
            $quantity = max(1, min(10, (int) ($line['qty'] ?? $line['quantity'] ?? 1)));

            if ($type === 'custom') {
                $name = trim((string) ($line['name'] ?? ''));
                $sellPrice = max(0, (float) ($line['sell_price'] ?? $line['price'] ?? 0));
                $costPrice = max(0, (float) ($line['cost_price'] ?? $line['cost'] ?? 0));
                if ($name === '' || $sellPrice <= 0) {
                    continue;
                }
                $items[] = [
                    'product_id' => null,
                    'source_type' => 'custom',
                    'source_id' => 0,
                    'name' => $name,
                    'slug' => '',
                    'sku' => trim((string) ($line['sku'] ?? '')) ?: 'CUSTOM',
                    'base_price' => $sellPrice,
                    'cost_price' => $costPrice,
                    'quantity' => $quantity,
                    'line_total' => round($sellPrice * $quantity, 2),
                ];
                continue;
            }

            $id = (int) ($line['id'] ?? 0);
            if ($id < 1 || !in_array($type, ['product', 'combo'], true)) {
                continue;
            }

            $stmt = $type === 'combo' ? $comboStmt : $productStmt;
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $name = trim((string) ($line['name'] ?? ''));
                $sellPrice = max(0, (float) ($line['sell_price'] ?? $line['price'] ?? 0));
                $costPrice = max(0, (float) ($line['cost_price'] ?? $line['cost'] ?? 0));
                if ($name !== '' && $sellPrice > 0) {
                    $items[] = [
                        'product_id' => null,
                        'source_type' => 'custom',
                        'source_id' => 0,
                        'name' => $name,
                        'slug' => '',
                        'sku' => trim((string) ($line['sku'] ?? '')) ?: 'CUSTOM',
                        'base_price' => $sellPrice,
                        'cost_price' => $costPrice,
                        'quantity' => $quantity,
                        'line_total' => round($sellPrice * $quantity, 2),
                    ];
                }
                continue;
            }

            $item = self::normalizeItem($pdo, $row, $type, $quantity);

            if (isset($line['sell_price']) || isset($line['price'])) {
                $overrideSell = max(0, (float) ($line['sell_price'] ?? $line['price'] ?? 0));
                if ($overrideSell > 0) {
                    $item['base_price'] = $overrideSell;
                    $item['line_total'] = round($overrideSell * $quantity, 2);
                }
            }
            if (isset($line['cost_price']) || isset($line['cost'])) {
                $item['cost_price'] = max(0, (float) ($line['cost_price'] ?? $line['cost'] ?? 0));
            }
            $overrideName = trim((string) ($line['name'] ?? ''));
            if ($overrideName !== '') {
                $item['name'] = $overrideName;
            }

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param list<array<string, mixed>> $orderItems
     * @param array<string, mixed>       $data
     */
    public static function create(PDO $pdo, array $orderItems, array $data): int
    {
        if ($orderItems === []) {
            throw new \InvalidArgumentException('Add at least one product or combo.');
        }

        InventoryService::ensureSchema($pdo);
        self::ensureOrderColumns($pdo);
        $subtotal = round(array_sum(array_column($orderItems, 'line_total')), 2);
        $discount = max(0, min($subtotal, round((float) ($data['discount_total'] ?? 0), 2)));
        $total = round($subtotal - $discount, 2);
        $method = (string) ($data['payment_method'] ?? 'cod');
        if (!in_array($method, ['cod', 'bank_deposit'], true)) {
            $method = 'cod';
        }

        $option = $method === 'cod' ? 'advance' : 'full';
        $paymentAmount = (float) ($data['payment_amount'] ?? ($method === 'cod' ? min(500.00, $total) : $total));
        $paymentAmount = max(0, min($total, round($paymentAmount, 2)));

        $bankId = (int) ($data['bank_account_id'] ?? 0);
        $bankAccount = null;
        if ($bankId > 0) {
            $bankStmt = $pdo->prepare(
                "SELECT id, bank_name, account_name, account_number, branch
                 FROM bank_accounts WHERE id = ? AND status = 'active' LIMIT 1"
            );
            $bankStmt->execute([$bankId]);
            $bankAccount = $bankStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $orderStatus = (string) ($data['order_status'] ?? 'pending');
        if (!in_array($orderStatus, ['pending', 'confirmed'], true)) {
            $orderStatus = 'pending';
        }

        $paymentStatus = (string) ($data['payment_status'] ?? 'pending');
        if (!in_array($paymentStatus, ['pending', 'paid'], true)) {
            $paymentStatus = 'pending';
        }

        if ($paymentStatus === 'paid') {
            $paymentAmount = $total;
        }

        $orderNumber = 'GV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $userId = (int) ($data['user_id'] ?? 0) ?: null;
        $adminNotes = trim((string) ($data['admin_notes'] ?? ''));
        $customerNotes = trim((string) ($data['customer_notes'] ?? ''));

        $pdo->beginTransaction();
        try {
            $order = $pdo->prepare(
                'INSERT INTO orders (
                    order_number, user_id, customer_name, customer_email, customer_phone,
                    recipient_name, recipient_phone,
                    delivery_address_line_1, delivery_address_line_2, delivery_city, delivery_district, delivery_postal_code,
                    delivery_date, subtotal, discount_total, grand_total, payment_status, order_status, order_source, customer_notes, admin_notes
                 ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $order->execute([
                $orderNumber,
                $userId,
                trim((string) $data['customer_name']),
                trim((string) $data['customer_email']),
                trim((string) $data['customer_phone']),
                trim((string) $data['recipient_name']),
                trim((string) $data['recipient_phone']),
                trim((string) $data['delivery_address_line_1']),
                trim((string) ($data['delivery_address_line_2'] ?? '')),
                trim((string) $data['delivery_city']),
                trim((string) $data['delivery_district']),
                trim((string) ($data['delivery_postal_code'] ?? '')),
                trim((string) ($data['delivery_date'] ?? '')) ?: null,
                $subtotal,
                $discount,
                $total,
                $paymentStatus,
                $orderStatus,
                (string) ($data['order_source'] ?? (!empty($data['created_by_admin']) ? 'admin' : 'website')),
                $customerNotes,
                $adminNotes !== '' ? $adminNotes : null,
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $item = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, variant_id, product_name, sku, quantity, unit_price, cost_price, total_price, custom_options_json)
                 VALUES (?,?,?,?,?,?,?,?,?,?)'
            );
            foreach ($orderItems as $product) {
                $item->execute([
                    $orderId,
                    $product['product_id'],
                    $product['variant_id'] ?? null,
                    $product['name'],
                    $product['sku'],
                    $product['quantity'],
                    $product['base_price'],
                    $product['cost_price'],
                    $product['line_total'],
                    !empty($product['variant_name']) ? json_encode(['colour' => $product['variant_name']], JSON_UNESCAPED_UNICODE) : null,
                ]);
            }

            if ($orderStatus === 'confirmed') {
                InventoryService::deductOrderStock($pdo, $orderId);
            }

            $paymentStatusDb = match ($paymentStatus) {
                'paid' => 'paid',
                default => 'pending',
            };

            $payment = $pdo->prepare(
                'INSERT INTO payments (order_id, provider, method, transaction_reference, amount, status, receipt_path, raw_response_json, paid_at, verified_by, verified_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?)'
            );
            $payment->execute([
                $orderId,
                $method === 'cod' ? 'cash_on_delivery' : 'bank',
                $method,
                $orderNumber . '-PAY',
                $paymentAmount,
                $paymentStatusDb,
                $data['receipt_path'] ?? null,
                json_encode([
                    'payment_option' => $option,
                    'balance_due' => max(0, $total - $paymentAmount),
                    'bank_account_id' => $bankId ?: null,
                    'bank_name' => $bankAccount['bank_name'] ?? null,
                    'account_number' => $bankAccount['account_number'] ?? null,
                    'created_by_admin' => (bool) ($data['created_by_admin'] ?? false),
                ]),
                $paymentStatus === 'paid' ? date('Y-m-d H:i:s') : null,
                $paymentStatus === 'paid' ? (int) ($data['verified_by'] ?? 0) ?: null : null,
                $paymentStatus === 'paid' ? date('Y-m-d H:i:s') : null,
            ]);

            if (($data['notify_admin'] ?? true) === true) {
                $source = !empty($data['created_by_admin']) ? 'Admin manual order' : 'Website checkout';
                $notice = $pdo->prepare(
                    "INSERT INTO admin_notifications (type, title, message, entity_type, entity_id, status)
                     VALUES ('new_order', 'New order awaiting verification', ?, 'order', ?, 'unread')"
                );
                $notice->execute([
                    "{$orderNumber}: {$source}, LKR " . number_format($paymentAmount, 2) . '.',
                    $orderId,
                ]);
            }

            $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }

        return $orderId;
    }

    private static function ensureOrderColumns(PDO $pdo): void
    {
        $columns = $pdo->query('DESCRIBE orders')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('order_source', $columns, true)) {
            $pdo->exec("ALTER TABLE orders ADD order_source VARCHAR(30) NOT NULL DEFAULT 'website' AFTER order_status");
        }
    }

    public static function storeReceipt(array $file, bool $required = true): ?string
    {
        $errCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($errCode === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                throw new \InvalidArgumentException('Upload a payment receipt.');
            }
            return null;
        }
        if ($errCode !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Receipt upload failed. Please try again.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size < 1 || $size > 5 * 1024 * 1024) {
            throw new \InvalidArgumentException('Receipt must be no larger than 5 MB.');
        }

        $mime = self::receiptMimeType($file);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        if (!isset($extensions[$mime])) {
            throw new \InvalidArgumentException('Receipt must be JPG, PNG, WebP, or PDF.');
        }

        $directory = BASE_PATH . '/public/assets/uploads/receipts';
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $name = 'receipt_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $name)) {
            throw new \InvalidArgumentException('Failed to save receipt file.');
        }

        return '/assets/uploads/receipts/' . $name;
    }

    /** @param array<string, mixed> $row */
    private static function normalizeItem(PDO $pdo, array $row, string $sourceType, int $quantity): array
    {
        $unitPrice = (float) $row['base_price'];
        $costPrice = $sourceType === 'combo'
            ? self::comboCostPrice($pdo, (int) $row['id'], (float) ($row['other_cost'] ?? 0))
            : (float) ($row['cost_price'] ?? 0);

        return [
            'product_id' => $sourceType === 'combo' ? null : (int) $row['id'],
            'source_type' => $sourceType,
            'source_id' => (int) $row['id'],
            'name' => (string) $row['name'],
            'slug' => (string) $row['slug'],
            'sku' => (string) $row['sku'],
            'base_price' => $unitPrice,
            'cost_price' => $costPrice,
            'quantity' => $quantity,
            'line_total' => round($unitPrice * $quantity, 2),
        ];
    }

    private static function comboCostPrice(PDO $pdo, int $comboId, float $otherCost): float
    {
        $stmt = $pdo->prepare(
            'SELECT COALESCE(SUM(p.cost_price), 0) AS product_cost
             FROM combo_products cp
             INNER JOIN products p ON p.id = cp.product_id
             WHERE cp.combo_id = ?'
        );
        $stmt->execute([$comboId]);
        $productCost = (float) ($stmt->fetchColumn() ?: 0);

        return round($productCost + $otherCost, 2);
    }

    private static function receiptMimeType(array $file): string
    {
        $path = (string) ($file['tmp_name'] ?? '');
        if ($path === '' || !is_file($path)) {
            return '';
        }

        if (class_exists(\finfo::class) && defined('FILEINFO_MIME_TYPE')) {
            $detector = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $detector->file($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }

        $image = @getimagesize($path);
        if (is_array($image) && isset($image['mime']) && in_array($image['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return (string) $image['mime'];
        }

        $handle = @fopen($path, 'rb');
        if ($handle !== false) {
            $signature = (string) fread($handle, 5);
            fclose($handle);
            if ($signature === '%PDF-') {
                return 'application/pdf';
            }
        }

        return (string) ($file['type'] ?? '');
    }
}
