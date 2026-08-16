<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\InventoryService;
use PDO;

final class InventoryController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        InventoryService::ensureSchema($pdo);
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $items = InventoryService::listItems($pdo);
        $totalUnits = array_sum(array_map(static fn(array $i): int => (int) $i['quantity'], $items));
        $lowStock = array_filter($items, static fn(array $i): bool => (int) $i['quantity'] <= (int) $i['low_stock_threshold']);

        $catalogProducts = $pdo->query(
            "SELECT id, name, sku FROM products WHERE status = 'active' ORDER BY name"
        )->fetchAll(PDO::FETCH_ASSOC);

        $stockOnlyItems = array_values(array_filter($items, static fn(array $i): bool => empty($i['product_id'])));
        $viewItem = null;
        $viewBatches = [];
        $editItem = null;
        $viewId = filter_input(INPUT_GET, 'view', FILTER_VALIDATE_INT);
        if ($viewId) {
            foreach ($items as $item) if ((int)$item['id'] === $viewId) { $viewItem = $item; break; }
            if ($viewItem) {
                $batchStmt = $pdo->prepare('SELECT * FROM inventory_batches WHERE inventory_item_id=? ORDER BY id DESC');
                $batchStmt->execute([$viewId]);
                $viewBatches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($editId) foreach ($items as $item) if ((int)$item['id'] === $editId) { $editItem = $item; break; }

        $flash = $_SESSION['inventory_flash'] ?? null;
        unset($_SESSION['inventory_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Inventory',
            'pageTitle' => 'Inventory',
            'showPageTitle' => false,
            'content' => $this->render('admin/inventory/index', [
                'items' => $items,
                'totalUnits' => $totalUnits,
                'lowStockCount' => count($lowStock),
                'catalogProducts' => $catalogProducts,
                'stockOnlyItems' => $stockOnlyItems,
                'viewItem' => $viewItem,
                'viewBatches' => $viewBatches,
                'editItem' => $editItem,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        $action = (string) ($_POST['action'] ?? '');

        try {
            if ($action === 'save_item') {
                $editingId = (int) ($_POST['id'] ?? 0);
                $quantity = (int) ($_POST['quantity'] ?? 0);
                $unitCost = (float) ($_POST['cost_price'] ?? 0);
                if ($editingId > 0) {
                    $linked = $pdo->prepare('SELECT product_id FROM inventory_items WHERE id=? LIMIT 1');
                    $linked->execute([$editingId]);
                    $productId = (int)$linked->fetchColumn();
                    if ($productId > 0) {
                        $name = trim((string)($_POST['name'] ?? ''));
                        if ($name === '') throw new \InvalidArgumentException('Item name is required.');
                        $pdo->beginTransaction();
                        $pdo->prepare('UPDATE inventory_items SET name=? WHERE id=?')->execute([$name,$editingId]);
                        $pdo->prepare('UPDATE products SET name=? WHERE id=?')->execute([$name,$productId]);
                        $pdo->commit();
                        $this->redirect('Inventory item updated.');
                    }
                }
                $pdo->beginTransaction();
                $itemId = InventoryService::saveStockItem($pdo, [
                    'id' => (int) ($_POST['id'] ?? 0),
                    'name' => (string) ($_POST['name'] ?? ''),
                    'sku' => (string) ($_POST['sku'] ?? ''),
                    'quantity' => $editingId < 1 ? 0 : (int) ($_POST['quantity'] ?? 0),
                    'cost_price' => (float) ($_POST['cost_price'] ?? 0),
                    'unit' => (string) ($_POST['unit'] ?? 'pcs'),
                    'low_stock_threshold' => (int) ($_POST['low_stock_threshold'] ?? 5),
                    'notes' => (string) ($_POST['notes'] ?? ''),
                ]);
                if ($editingId < 1 && $quantity > 0 && $unitCost > 0) {
                    InventoryService::receiveBatch($pdo, $itemId, $quantity, $unitCost);
                    $this->recordStockExpense($pdo, $itemId, (string) ($_POST['name'] ?? ''), $quantity, $unitCost);
                }
                $pdo->commit();
                $this->redirect(((int) ($_POST['id'] ?? 0)) > 0 ? 'Inventory item updated.' : 'Stock item added to inventory.');
            }

            if ($action === 'adjust_stock') {
                $itemId = (int) ($_POST['inventory_item_id'] ?? 0);
                $change = (int) ($_POST['change_qty'] ?? 0);
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('SELECT name,cost_price FROM inventory_items WHERE id = ? FOR UPDATE');
                $stmt->execute([$itemId]);
                $lockedItem = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$lockedItem) throw new \InvalidArgumentException('Inventory item not found.');
                $itemName = (string)$lockedItem['name'];
                $priceMode = (string)($_POST['price_mode'] ?? 'same');
                $unitCost = $priceMode === 'new' ? max(0,(float)($_POST['new_unit_cost'] ?? 0)) : (float)$lockedItem['cost_price'];
                if ($itemId < 1 || $change < 1 || $unitCost <= 0) throw new \InvalidArgumentException('Enter a valid stock quantity and product cost.');
                $newBatch = InventoryService::receiveBatch($pdo, $itemId, $change, $unitCost);
                $this->recordStockExpense($pdo, $itemId, $itemName, $change, $unitCost);
                $pdo->commit();
                $this->redirect($newBatch ? 'New stock batch created.' : 'Stock added to the current batch.');
            }

            if (in_array($action, ['update_batch','delete_batch'], true)) {
                $batchId = max(0,(int)($_POST['batch_id'] ?? 0));
                $itemId = max(0,(int)($_POST['inventory_item_id'] ?? 0));
                if ($batchId < 1 || $itemId < 1) throw new \InvalidArgumentException('Stock batch not found.');
                $pdo->beginTransaction();
                $batchStmt = $pdo->prepare('SELECT id,quantity_remaining FROM inventory_batches WHERE id=? AND inventory_item_id=? FOR UPDATE');
                $batchStmt->execute([$batchId,$itemId]);
                $batch = $batchStmt->fetch(PDO::FETCH_ASSOC);
                if (!$batch) throw new \InvalidArgumentException('Stock batch not found.');
                if ($action === 'delete_batch') {
                    $pdo->prepare('DELETE FROM inventory_batches WHERE id=?')->execute([$batchId]);
                } else {
                    $remaining = max(0,(int)($_POST['quantity_remaining'] ?? 0));
                    $pdo->prepare('UPDATE inventory_batches SET quantity_remaining=? WHERE id=?')->execute([$remaining,$batchId]);
                }
                $total = (int)$pdo->query('SELECT COALESCE(SUM(quantity_remaining),0) FROM inventory_batches WHERE inventory_item_id='.(int)$itemId)->fetchColumn();
                $pdo->prepare('UPDATE inventory_items SET quantity=? WHERE id=?')->execute([$total,$itemId]);
                $pdo->prepare('UPDATE products p INNER JOIN inventory_items i ON i.product_id=p.id SET p.stock_quantity=? WHERE i.id=?')->execute([$total,$itemId]);
                $pdo->commit();
                $this->redirect($action === 'delete_batch' ? 'Stock batch deleted.' : 'Batch stock updated.', 'success', $itemId);
            }

            if ($action === 'delete_item') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare('SELECT product_id FROM inventory_items WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row || !empty($row['product_id'])) {
                    $this->redirect('Only extra stock items can be deleted. Catalog products stay linked to Products.', 'error');
                }
                $pdo->prepare('UPDATE expenses SET inventory_item_id=NULL WHERE inventory_item_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM order_inventory_deductions WHERE inventory_item_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM inventory_movements WHERE inventory_item_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM inventory_batches WHERE inventory_item_id=?')->execute([$id]);
                $pdo->prepare('DELETE FROM inventory_items WHERE id = ?')->execute([$id]);
                $this->redirect('Stock item removed.');
            }
        } catch (\InvalidArgumentException $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->redirect($exception->getMessage(), 'error');
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->redirect('Inventory could not be saved.', 'error');
        }

        $this->redirect('Invalid action.', 'error');
    }

    private function recordStockExpense(PDO $pdo, int $itemId, string $name, int $quantity, float $unitCost): void
    {
        $amount = round($quantity * $unitCost, 2);
        if ($amount <= 0) return;
        $number = 'STK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $pdo->prepare(
            'INSERT INTO expenses (admin_id, paid_source, expense_number, category, title, description, amount, expense_date, status, inventory_item_id, stock_quantity)
             VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?)'
        )->execute([
            (int) ($_SESSION['admin_user']['id'] ?? 0) ?: null, 'company_cash', $number, 'other',
            'Inventory purchase: ' . trim($name), "{$quantity} units × LKR " . number_format($unitCost, 2, '.', ''),
            $amount, 'approved', $itemId, $quantity,
        ]);
    }

    private function redirect(string $message, string $type = 'success', ?int $viewId = null): never
    {
        $_SESSION['inventory_flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . app_url('/admin/inventory' . ($viewId ? '?view=' . $viewId : '')), true, 303);
        exit;
    }
}
