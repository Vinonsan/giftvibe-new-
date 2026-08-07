<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class ComboController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $this->ensureComboSchema($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $combos = $pdo->query("SELECT c.*, ci.image_path, COUNT(DISTINCT cp.product_id) product_count 
            FROM combos c 
            LEFT JOIN combo_images ci ON ci.combo_id = c.id AND ci.is_primary = 1 
            LEFT JOIN combo_products cp ON cp.combo_id = c.id 
            GROUP BY c.id 
            ORDER BY c.id DESC")->fetchAll();
            
        $products = $pdo->query("SELECT id, name, sku, cost_price, base_price FROM products WHERE status = 'active' ORDER BY name")->fetchAll();
        
        $editCombo = null; 
        $selectedProducts = []; 
        $comboImages = []; 
        $comboVideos = [];

        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($editId) {
            $statement = $pdo->prepare('SELECT * FROM combos WHERE id = ?');
            $statement->execute([$editId]);
            $editCombo = $statement->fetch() ?: null;
            
            $statement = $pdo->prepare('SELECT product_id FROM combo_products WHERE combo_id = ? ORDER BY sort_order');
            $statement->execute([$editId]);
            $selectedProducts = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
            
            $statement = $pdo->prepare('SELECT * FROM combo_images WHERE combo_id = ? ORDER BY is_primary DESC, sort_order, id');
            $statement->execute([$editId]);
            $comboImages = $statement->fetchAll();
            
            $statement = $pdo->prepare('SELECT video_url FROM combo_videos WHERE combo_id = ? ORDER BY sort_order, id');
            $statement->execute([$editId]);
            $comboVideos = $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['combo_flash'] ?? null;
        unset($_SESSION['combo_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Combos',
            'pageTitle' => 'Combos',
            'showPageTitle' => false,
            'content' => $this->render('admin/combos/index', compact(
                'combos', 'products', 'editCombo', 'selectedProducts', 'comboImages', 'comboVideos', 'flash'
            ) + ['csrfToken' => $_SESSION['csrf_token']])
        ]);
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $this->ensureComboSchema($pdo);
        
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('Combo not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT * FROM combos WHERE id = ?');
        $statement->execute([$id]);
        $combo = $statement->fetch();
        if (!$combo) {
            $this->redirect('Combo not found.', 'error');
        }

        $products = $pdo->query("SELECT id, name, sku, cost_price, base_price FROM products WHERE status = 'active' ORDER BY name")->fetchAll();
        
        $prodStatement = $pdo->prepare('SELECT p.id, p.name, p.sku, p.cost_price, p.base_price FROM combo_products cp INNER JOIN products p ON p.id = cp.product_id WHERE cp.combo_id = ? ORDER BY cp.sort_order');
        $prodStatement->execute([$id]);
        $comboProducts = $prodStatement->fetchAll();
        $selectedProductIds = array_map('intval', array_column($comboProducts, 'id'));

        $imageStatement = $pdo->prepare('SELECT id, image_path, alt_text, is_primary FROM combo_images WHERE combo_id = ? ORDER BY is_primary DESC, sort_order, id');
        $imageStatement->execute([$id]);
        $comboImages = $imageStatement->fetchAll();
        
        $primaryImage = '/assets/images/hero_slide_1.jpg';
        foreach ($comboImages as $img) {
            if ($img['is_primary']) {
                $primaryImage = $img['image_path'];
                break;
            }
        }
        if ($comboImages && $primaryImage === '/assets/images/hero_slide_1.jpg') {
            $primaryImage = $comboImages[0]['image_path'];
        }
        $combo['primary_image'] = $primaryImage;

        $videoStatement = $pdo->prepare('SELECT video_url FROM combo_videos WHERE combo_id = ? ORDER BY sort_order, id');
        $videoStatement->execute([$id]);
        $comboVideos = $videoStatement->fetchAll(PDO::FETCH_COLUMN);

        $editCombo = $combo;

        $tabs = ['basic', 'products', 'images', 'seo', 'settings'];
        $tab = strtolower((string) ($_GET['tab'] ?? 'basic'));
        if (!in_array($tab, $tabs, true)) {
            $tab = 'basic';
        }
        $editMode = (($_GET['mode'] ?? '') === 'edit');

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['combo_flash'] ?? null;
        unset($_SESSION['combo_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'View Combo',
            'pageTitle' => 'View Combo',
            'showPageTitle' => false,
            'content' => $this->render('admin/combos/show', compact(
                'combo',
                'editCombo',
                'products',
                'comboProducts',
                'selectedProductIds',
                'comboImages',
                'comboVideos',
                'tab',
                'tabs',
                'editMode',
                'flash'
            ) + ['csrfToken' => $_SESSION['csrf_token']]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid request token.');
        }
        
        $this->ensureComboSchema($pdo);
        
        if (($_POST['action'] ?? 'save') === 'save_section') {
            $this->handleSectionSave($pdo);
        }
        
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        if (($_POST['action'] ?? 'save') === 'delete' && $id) {
            $pdo->prepare('DELETE FROM combos WHERE id = ?')->execute([$id]);
            $this->redirect('Combo deleted.');
        }
        
        $name = trim((string)($_POST['name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $price = max(0, (float)($_POST['price'] ?? 0));
        $otherCost = max(0, (float)($_POST['other_cost'] ?? 0));
        $productIds = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['product_ids'] ?? [])))));
        
        if ($name === '' || $price <= 0 || !$productIds) {
            $this->redirect('Combo name, price and at least one product are required.', 'error', $id);
        }
        
        $slug = strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        if ($id) {
            $slug .= '-' . $id;
        }
        
        $videoUrls = array_values(array_unique(array_filter(array_map('trim', (array)($_POST['video_urls'] ?? [])), static fn(string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false)));
        $deleteIds = array_values(array_filter(array_map('intval', (array)($_POST['delete_images'] ?? []))));
        $existing = [];
        if ($id) {
            $s = $pdo->prepare('SELECT id FROM combo_images WHERE combo_id = ?');
            $s->execute([$id]);
            $existing = array_map('intval', $s->fetchAll(PDO::FETCH_COLUMN));
        }
        
        $newImages = [];
        try {
            $files = $_FILES['combo_images'] ?? null;
            if ($files && is_array($files['name'] ?? null)) {
                foreach (array_keys($files['name']) as $index) {
                    if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                    $newImages[] = $this->upload([
                        'tmp_name' => $files['tmp_name'][$index] ?? '',
                        'error' => $files['error'][$index],
                        'size' => $files['size'][$index] ?? 0
                    ]);
                }
            }
        } catch (\RuntimeException $e) {
            $this->redirect($e->getMessage(), 'error', $id);
        }
        
        if (!array_diff($existing, $deleteIds) && !$newImages) {
            $this->redirect('Upload at least one combo image.', 'error', $id);
        }
        
        $metaTitle = trim((string)($_POST['meta_title'] ?? '')) ?: $name;
        $metaDescription = trim((string)($_POST['meta_description'] ?? '')) ?: $description;
        $searchKeywords = trim((string)($_POST['search_keywords'] ?? ''));

        $pdo->beginTransaction();
        try {
            if ($id) {
                $pdo->prepare('UPDATE combos SET name=?, slug=?, description=?, price=?, other_cost=?, status=?, meta_title=?, meta_description=?, search_keywords=? WHERE id=?')
                    ->execute([$name, $slug, $description, $price, $otherCost, isset($_POST['status']) ? 'active' : 'draft', $metaTitle, $metaDescription, $searchKeywords, $id]);
            } else {
                $pdo->prepare('INSERT INTO combos(name, slug, description, price, other_cost, status, meta_title, meta_description, search_keywords) VALUES(?,?,?,?,?,?,?,?,?)')
                    ->execute([$name, $slug, $description, $price, $otherCost, isset($_POST['status']) ? 'active' : 'draft', $metaTitle, $metaDescription, $searchKeywords]);
                $id = (int) $pdo->lastInsertId();
            }
            
            $pdo->prepare('DELETE FROM combo_products WHERE combo_id=?')->execute([$id]);
            $insert = $pdo->prepare('INSERT INTO combo_products(combo_id, product_id, sort_order) VALUES(?,?,?)');
            foreach ($productIds as $order => $productId) {
                $insert->execute([$id, $productId, $order]);
            }
            
            if ($deleteIds) {
                $marks = implode(',', array_fill(0, count($deleteIds), '?'));
                $pdo->prepare("DELETE FROM combo_images WHERE id IN ($marks) AND combo_id=?")->execute([...$deleteIds, $id]);
            }
            
            $sort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM combo_images WHERE combo_id=' . (int) $id)->fetchColumn();
            $insertImage = $pdo->prepare('INSERT INTO combo_images(combo_id, image_path, alt_text, sort_order, is_primary) VALUES(?,?,?,?,0)');
            foreach ($newImages as $index => $path) {
                $insertImage->execute([$id, $path, $name . ' ' . ($index + 1), ++$sort]);
            }
            
            $pdo->prepare('UPDATE combo_images SET is_primary=0 WHERE combo_id=?')->execute([$id]);
            $primary = $pdo->query('SELECT id FROM combo_images WHERE combo_id=' . (int) $id . ' ORDER BY sort_order, id LIMIT 1')->fetchColumn();
            if ($primary) {
                $pdo->prepare('UPDATE combo_images SET is_primary=1 WHERE id=?')->execute([$primary]);
            }
            
            $pdo->prepare('DELETE FROM combo_videos WHERE combo_id=?')->execute([$id]);
            $insertVideo = $pdo->prepare('INSERT INTO combo_videos(combo_id, video_url, sort_order) VALUES(?,?,?)');
            foreach ($videoUrls as $order => $url) {
                $insertVideo->execute([$id, $url, $order]);
            }
            
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $this->redirect('Combo details could not be saved.', 'error', $id);
        }
        
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/admin/combos/view')) {
            $this->redirectToView($id, 'basic', 'Combo saved.');
        }
        $this->redirect('Combo saved.');
    }

    private function handleSectionSave(PDO $pdo): never
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $section = (string) ($_POST['section'] ?? '');
        $tab = (string) ($_POST['tab'] ?? $section);
        
        if (!$id) {
            $this->redirect('Combo not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT * FROM combos WHERE id = ?');
        $statement->execute([$id]);
        $combo = $statement->fetch();
        if (!$combo) {
            $this->redirect('Combo not found.', 'error');
        }

        try {
            if ($section === 'basic') {
                $name = trim((string) ($_POST['name'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));
                $price = max(0, (float) ($_POST['price'] ?? 0));
                
                if ($name === '' || $price <= 0) {
                    $this->redirectToView($id, $tab, 'Name and price are required.', 'error', true);
                }
                
                $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
                $slug .= '-' . $id;

                $pdo->prepare('UPDATE combos SET name=?, slug=?, description=?, price=? WHERE id=?')->execute([
                    $name, $slug, $description, $price, $id
                ]);
            } elseif ($section === 'products') {
                $productIds = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['product_ids'] ?? [])))));
                $price = max(0, (float) ($_POST['price'] ?? 0));
                $otherCost = max(0, (float) ($_POST['other_cost'] ?? 0));
                
                if (!$productIds) {
                    $this->redirectToView($id, $tab, 'Select at least one product for the combo.', 'error', true);
                }
                if ($price <= 0) {
                    $this->redirectToView($id, $tab, 'Valid selling price is required.', 'error', true);
                }
                
                $pdo->prepare('UPDATE combos SET price=?, other_cost=? WHERE id=?')->execute([$price, $otherCost, $id]);
                
                $pdo->prepare('DELETE FROM combo_products WHERE combo_id=?')->execute([$id]);
                $insert = $pdo->prepare('INSERT INTO combo_products(combo_id, product_id, sort_order) VALUES(?,?,?)');
                foreach ($productIds as $order => $productId) {
                    $insert->execute([$id, $productId, $order]);
                }
            } elseif ($section === 'seo') {
                $pdo->prepare('UPDATE combos SET meta_title=?, meta_description=?, search_keywords=? WHERE id=?')->execute([
                    trim((string) ($_POST['meta_title'] ?? '')),
                    trim((string) ($_POST['meta_description'] ?? '')),
                    trim((string) ($_POST['search_keywords'] ?? '')),
                    $id
                ]);
            } elseif ($section === 'images') {
                $deleteIds = array_values(array_filter(array_map('intval', (array) ($_POST['delete_images'] ?? []))));
                $existingImageIds = array_map('intval', $pdo->query('SELECT id FROM combo_images WHERE combo_id=' . (int) $id)->fetchAll(PDO::FETCH_COLUMN));
                $newImages = [];
                $files = $_FILES['combo_images'] ?? null;
                if ($files && is_array($files['name'] ?? null)) {
                    foreach (array_keys($files['name']) as $index) {
                        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                        $newImages[] = $this->upload([
                            'tmp_name' => $files['tmp_name'][$index] ?? '',
                            'error' => $files['error'][$index],
                            'size' => $files['size'][$index] ?? 0
                        ]);
                    }
                }
                
                $remainingExisting = array_diff($existingImageIds, $deleteIds);
                if (!$remainingExisting && !$newImages) {
                    $this->redirectToView($id, $tab, 'Upload at least one combo image.', 'error', true);
                }
                
                if ($deleteIds) {
                    $inQuery = implode(',', array_fill(0, count($deleteIds), '?'));
                    $deleteStmt = $pdo->prepare("DELETE FROM combo_images WHERE id IN ($inQuery) AND combo_id = ?");
                    $deleteStmt->execute(array_merge($deleteIds, [$id]));
                }
                
                $sort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM combo_images WHERE combo_id=' . (int) $id)->fetchColumn();
                $insertImage = $pdo->prepare('INSERT INTO combo_images(combo_id, image_path, alt_text, sort_order, is_primary) VALUES(?,?,?,?,0)');
                foreach ($newImages as $index => $path) {
                    $insertImage->execute([$id, $path, $combo['name'] . ' ' . ($index + 1), ++$sort]);
                }
                
                $pdo->prepare('UPDATE combo_images SET is_primary=0 WHERE combo_id=?')->execute([$id]);
                $primaryId = $pdo->query('SELECT id FROM combo_images WHERE combo_id=' . (int) $id . ' ORDER BY sort_order,id LIMIT 1')->fetchColumn();
                if ($primaryId) {
                    $pdo->prepare('UPDATE combo_images SET is_primary=1 WHERE id=?')->execute([$primaryId]);
                }
                
                $videoUrls = array_values(array_unique(array_filter(
                    array_map('trim', (array) ($_POST['video_urls'] ?? [])),
                    static fn(string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false
                )));
                $pdo->prepare('DELETE FROM combo_videos WHERE combo_id=?')->execute([$id]);
                $insertVideo = $pdo->prepare('INSERT INTO combo_videos(combo_id, video_url, sort_order) VALUES(?,?,?)');
                foreach ($videoUrls as $index => $url) {
                    $insertVideo->execute([$id, $url, $index]);
                }
            } elseif ($section === 'settings') {
                $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'draft';
                $pdo->prepare('UPDATE combos SET status=? WHERE id=?')->execute([$status, $id]);
            } else {
                $this->redirectToView($id, $tab, 'Unknown section.', 'error', true);
            }
        } catch (\Throwable $e) {
            $this->redirectToView($id, $tab, $e->getMessage(), 'error', true);
        }

        $this->redirectToView($id, $tab, 'Changes saved.');
    }

    private function ensureComboSchema(PDO $pdo): void
    {
        $columns = $pdo->query("DESCRIBE `combos`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('meta_title', $columns, true)) {
            $pdo->exec("ALTER TABLE `combos` ADD `meta_title` VARCHAR(190) NULL DEFAULT NULL");
        }
        if (!in_array('meta_description', $columns, true)) {
            $pdo->exec("ALTER TABLE `combos` ADD `meta_description` VARCHAR(255) NULL DEFAULT NULL");
        }
        if (!in_array('search_keywords', $columns, true)) {
            $pdo->exec("ALTER TABLE `combos` ADD `search_keywords` TEXT NULL DEFAULT NULL");
        }
        if (!in_array('other_cost', $columns, true)) {
            $pdo->exec("ALTER TABLE `combos` ADD `other_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `price`");
        }
    }

    private function upload(array $file): string
    {
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int)($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Each combo image must be 5MB or smaller.');
        }
        $info = @getimagesize((string)$file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($types[$info['mime'] ?? ''])) {
            throw new \RuntimeException('Use JPG, PNG or WebP images.');
        }
        $dir = BASE_PATH . '/public/assets/uploads/combos';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $name = bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
        if (!move_uploaded_file((string)$file['tmp_name'], $dir . '/' . $name)) {
            throw new \RuntimeException('Combo image upload failed.');
        }
        return '/assets/uploads/combos/' . $name;
    }

    private function redirect(string $message, string $type = 'success', ?int $id = null): never
    {
        $_SESSION['combo_flash'] = compact('message', 'type');
        header('Location: /admin/combos' . ($id ? '?edit=' . $id : ''), true, 303);
        exit;
    }

    private function redirectToView(int $id, string $tab, string $message, string $type = 'success', bool $editMode = false): never
    {
        $_SESSION['combo_flash'] = compact('message', 'type');
        $url = '/admin/combos/view?id=' . $id . '&tab=' . rawurlencode($tab);
        if ($editMode) {
            $url .= '&mode=edit';
        }
        header('Location: ' . $url, true, 303);
        exit;
    }
}
