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
                InventoryService::saveStockItem($pdo, [
                    'id' => (int) ($_POST['id'] ?? 0),
                    'name' => (string) ($_POST['name'] ?? ''),
                    'sku' => (string) ($_POST['sku'] ?? ''),
                    'quantity' => (int) ($_POST['quantity'] ?? 0),
                    'cost_price' => (float) ($_POST['cost_price'] ?? 0),
                    'unit' => (string) ($_POST['unit'] ?? 'pcs'),
                    'low_stock_threshold' => (int) ($_POST['low_stock_threshold'] ?? 5),
                    'notes' => (string) ($_POST['notes'] ?? ''),
                ]);
                $this->redirect(((int) ($_POST['id'] ?? 0)) > 0 ? 'Inventory item updated.' : 'Stock item added to inventory.');
            }

            if ($action === 'adjust_stock') {
                $itemId = (int) ($_POST['inventory_item_id'] ?? 0);
                $change = (int) ($_POST['change_qty'] ?? 0);
                if ($itemId < 1 || $change === 0) {
                    $this->redirect('Enter a valid quantity change.', 'error');
                }
                InventoryService::adjustQuantity($pdo, $itemId, $change, 'adjustment', null, trim((string) ($_POST['note'] ?? '')) ?: null);
                $this->redirect('Stock updated.');
            }

            if ($action === 'delete_item') {
                $id = (int) ($_POST['id'] ?? 0);
                $stmt = $pdo->prepare('SELECT product_id FROM inventory_items WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row || !empty($row['product_id'])) {
                    $this->redirect('Only extra stock items can be deleted. Catalog products stay linked to Products.', 'error');
                }
                $pdo->prepare('DELETE FROM inventory_items WHERE id = ?')->execute([$id]);
                $this->redirect('Stock item removed.');
            }
        } catch (\InvalidArgumentException $exception) {
            $this->redirect($exception->getMessage(), 'error');
        }

        $this->redirect('Invalid action.', 'error');
    }

    private function redirect(string $message, string $type = 'success'): never
    {
        $_SESSION['inventory_flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . app_url('/admin/inventory'), true, 303);
        exit;
    }
}
