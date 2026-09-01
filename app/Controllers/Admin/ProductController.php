<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\InventoryService;
use PDO;

class ProductController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $this->ensureProductSchema($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->handlePost($pdo);
        $products = $pdo->query("SELECT p.*,pi.image_path,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') category_names FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id GROUP BY p.id ORDER BY p.id DESC")->fetchAll();
        $categories = $pdo->query("SELECT id,name FROM categories WHERE status='active' ORDER BY sort_order,id")->fetchAll();
        $editProduct = null; $editCategoryIds = []; $secondaryImages = []; $productVideos = []; $productSocialLinks = []; $productVariants = []; $productOptions = [];
        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($editId) {
            $statement = $pdo->prepare("SELECT p.*,pi.image_path,pi.alt_text FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 WHERE p.id=?"); $statement->execute([$editId]);
            $editProduct = $statement->fetch() ?: null;
            $statement = $pdo->prepare('SELECT category_id FROM product_categories WHERE product_id=?'); $statement->execute([$editId]); $editCategoryIds = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
            
            // Get every saved image for gallery management.
            $secImagesStmt = $pdo->prepare("SELECT id, image_path, alt_text, is_primary FROM product_images WHERE product_id=? ORDER BY is_primary DESC, sort_order, id");
            $secImagesStmt->execute([$editId]);
            $secondaryImages = $secImagesStmt->fetchAll();
            $videoStatement = $pdo->prepare('SELECT video_url FROM product_videos WHERE product_id=? ORDER BY sort_order,id');
            $videoStatement->execute([$editId]);
            $productVideos = $videoStatement->fetchAll(PDO::FETCH_COLUMN);
            if (!$productVideos && trim((string) ($editProduct['video_url'] ?? '')) !== '') $productVideos[] = $editProduct['video_url'];
            $productSocialLinks = $this->loadSocialLinks($pdo, $editId);
            $productVariants = $this->loadVariants($pdo, $editId);
            $productOptions = $this->loadOptions($pdo, $editId);
        }
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['product_flash'] ?? null; unset($_SESSION['product_flash']);
        $this->view('layouts/admin-layout', ['title'=>'Products','pageTitle'=>'Products','showPageTitle'=>false,'content'=>$this->render('admin/products/index', compact('products','categories','editProduct','editCategoryIds','flash','secondaryImages','productVideos','productSocialLinks','productVariants','productOptions') + ['csrfToken'=>$_SESSION['csrf_token']])]);
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $this->ensureProductSchema($pdo);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('Product not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT p.*, pi.image_path, pi.alt_text FROM products p LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1 WHERE p.id = ?');
        $statement->execute([$id]);
        $product = $statement->fetch();
        if (!$product) {
            $this->redirect('Product not found.', 'error');
        }

        $categories = $pdo->query("SELECT id,name FROM categories WHERE status='active' ORDER BY sort_order,id")->fetchAll();
        $categoryStatement = $pdo->prepare('SELECT c.name, pc.category_id FROM product_categories pc INNER JOIN categories c ON c.id = pc.category_id WHERE pc.product_id = ? ORDER BY c.name');
        $categoryStatement->execute([$id]);
        $categoryRows = $categoryStatement->fetchAll();
        $categoryNames = array_column($categoryRows, 'name');
        $editCategoryIds = array_map('intval', array_column($categoryRows, 'category_id'));

        $imageStatement = $pdo->prepare('SELECT id, image_path, alt_text, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order, id');
        $imageStatement->execute([$id]);
        $productImages = $imageStatement->fetchAll();
        $secondaryImages = $productImages;

        $videoStatement = $pdo->prepare('SELECT video_url FROM product_videos WHERE product_id = ? ORDER BY sort_order, id');
        $videoStatement->execute([$id]);
        $productVideos = $videoStatement->fetchAll(PDO::FETCH_COLUMN);
        if (!$productVideos && trim((string) ($product['video_url'] ?? '')) !== '') {
            $productVideos[] = $product['video_url'];
        }

        $productSocialLinks = $this->loadSocialLinks($pdo, $id);
        $productVariants = $this->loadVariants($pdo, $id);
        $productOptions = $this->loadOptions($pdo, $id);
        $editProduct = $product;

        $tabs = ['basic', 'images', 'social', 'pricing', 'options'];
        $tab = strtolower((string) ($_GET['tab'] ?? 'basic'));
        if (!in_array($tab, $tabs, true)) {
            $tab = 'basic';
        }
        $editMode = (($_GET['mode'] ?? '') === 'edit');

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['product_flash'] ?? null;
        unset($_SESSION['product_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'View Product',
            'pageTitle' => 'View Product',
            'showPageTitle' => false,
            'content' => $this->render('admin/products/show', compact(
                'product',
                'editProduct',
                'categories',
                'editCategoryIds',
                'categoryNames',
                'productImages',
                'secondaryImages',
                'productVideos',
                'productSocialLinks',
                'productVariants',
                'productOptions',
                'tab',
                'tabs',
                'editMode',
                'flash'
            ) + ['csrfToken' => $_SESSION['csrf_token']]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) { http_response_code(419); exit('Invalid request token.'); }
        $this->ensureProductSchema($pdo);
        if (($_POST['action'] ?? 'save') === 'save_section') {
            $this->handleSectionSave($pdo);
        }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        if (($_POST['action'] ?? 'save') === 'delete' && $id) {
            $pdo->beginTransaction();
            try { $pdo->prepare('DELETE FROM product_categories WHERE product_id=?')->execute([$id]); $pdo->prepare('DELETE FROM product_images WHERE product_id=?')->execute([$id]); $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]); $pdo->commit(); }
            catch (\Throwable $e) { $pdo->rollBack(); $this->redirect('This product could not be deleted.', 'error'); }
            $this->redirect('Product deleted.');
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $categoryIds = array_values(array_filter(array_map('intval', (array) ($_POST['category_ids'] ?? []))));
        if ($name === '' || !$categoryIds) $this->redirect('Name and at least one category are required.', 'error', $id);
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $sku = '';
        if ($id) {
            $slugStatement = $pdo->prepare('SELECT slug, sku FROM products WHERE id=?');
            $slugStatement->execute([$id]);
            $existingProduct = $slugStatement->fetch(PDO::FETCH_ASSOC) ?: [];
            $existingSlug = trim((string) ($existingProduct['slug'] ?? ''));
            $sku = trim((string) ($existingProduct['sku'] ?? ''));
            if ($existingSlug !== '') $slug = $existingSlug;
        }
        if ($sku === '') $sku = $this->generateSku($pdo, $name);
        $basePrice = max(0, (float) ($_POST['selling_price'] ?? 0));
        $costPrice = max(0, (float) ($_POST['buying_price'] ?? 0));
        $procurementType = (string) ($_POST['procurement_type'] ?? '');
        if (!in_array($procurementType, ['handcrafted', 'purchased'], true)) $this->redirect('Select whether this product is handcrafted or purchased externally.', 'error', $id);
        $videoUrls = array_values(array_unique(array_filter(array_map('trim', (array) ($_POST['video_urls'] ?? [])), static fn(string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false)));
        $videoUrl = $videoUrls[0] ?? '';
        $imageAltText = trim((string) ($_POST['image_alt_text'] ?? ''));
        if ($imageAltText === '') $imageAltText = $name;

        $deleteIds = array_values(array_filter(array_map('intval', (array) ($_POST['delete_images'] ?? []))));
        $existingImageIds = [];
        if ($id) { $s=$pdo->prepare('SELECT id FROM product_images WHERE product_id=?'); $s->execute([$id]); $existingImageIds=array_map('intval',$s->fetchAll(PDO::FETCH_COLUMN)); }
        $newImages = [];
        try {
            $files = $_FILES['product_images'] ?? null;
            if ($files && is_array($files['name'] ?? null)) {
                foreach (array_keys($files['name']) as $index) {
                    if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                    $newImages[] = $this->upload(['name'=>$files['name'][$index],'type'=>$files['type'][$index]??'','tmp_name'=>$files['tmp_name'][$index]??'','error'=>$files['error'][$index],'size'=>$files['size'][$index]??0]);
                }
            }
        } catch (\RuntimeException $e) { $this->redirect($e->getMessage(),'error',$id); }
        $remainingExisting = array_diff($existingImageIds, $deleteIds);
        if (!$remainingExisting && !$newImages) $this->redirect('Upload at least one product image.','error',$id);
        
        $metaTitle = trim((string) ($_POST['meta_title'] ?? '')) ?: $name;
        $metaDescription = trim((string) ($_POST['meta_description'] ?? '')) ?: trim((string) ($_POST['short_description'] ?? ''));
        $searchKeywords = trim((string) ($_POST['search_keywords'] ?? ''));
        
        $stockQuantity = $procurementType === 'handcrafted' ? 0 : max(0,(int)($_POST['stock_quantity'] ?? 0));
        $data = [$sku,$name,$slug,trim((string)($_POST['short_description'] ?? '')),trim((string)($_POST['description'] ?? '')),$basePrice,$costPrice,$stockQuantity,isset($_POST['is_featured'])?1:0,($_POST['status'] ?? '')==='active'?'active':'draft',$videoUrl,$metaTitle,$metaDescription,$searchKeywords,$procurementType];
        $pdo->beginTransaction();
        try {
            if ($id) { 
                $pdo->prepare('UPDATE products SET sku=?,name=?,slug=?,short_description=?,description=?,base_price=?,cost_price=?,sale_price=NULL,stock_quantity=?,is_featured=?,status=?,video_url=?,meta_title=?,meta_description=?,search_keywords=?,procurement_type=? WHERE id=?')->execute([...$data,$id]); 
            }
            else { 
                $pdo->prepare('INSERT INTO products (sku,name,slug,short_description,description,base_price,cost_price,sale_price,stock_quantity,is_featured,status,video_url,meta_title,meta_description,search_keywords,procurement_type) VALUES (?,?,?,?,?,?,?,NULL,?,?,?,?,?,?,?,?)')->execute($data); 
                $id=(int)$pdo->lastInsertId(); 
            }
            
            // Categories
            $pdo->prepare('DELETE FROM product_categories WHERE product_id=?')->execute([$id]);
            $relation=$pdo->prepare('INSERT INTO product_categories(product_id,category_id) VALUES(?,?)'); 
            foreach ($categoryIds as $categoryId) $relation->execute([$id,$categoryId]);
            
            if ($id && $deleteIds) {
                $inQuery = implode(',', array_fill(0, count($deleteIds), '?'));
                $deleteStmt = $pdo->prepare("DELETE FROM product_images WHERE id IN ($inQuery) AND product_id = ?");
                $deleteStmt->execute(array_merge($deleteIds, [$id]));
            }
            $sort=(int)$pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM product_images WHERE product_id='.(int)$id)->fetchColumn();
            $insertImage=$pdo->prepare('INSERT INTO product_images(product_id,image_path,alt_text,sort_order,is_primary) VALUES(?,?,?,?,0)');
            foreach($newImages as $index=>$path)$insertImage->execute([$id,$path,$imageAltText.' '.($index+1),++$sort]);
            $pdo->prepare('UPDATE product_images SET is_primary=0 WHERE product_id=?')->execute([$id]);
            $primaryId=$pdo->query('SELECT id FROM product_images WHERE product_id='.(int)$id.' ORDER BY sort_order,id LIMIT 1')->fetchColumn();
            $pdo->prepare('UPDATE product_images SET is_primary=1 WHERE id=?')->execute([$primaryId]);
            $pdo->prepare('DELETE FROM product_videos WHERE product_id=?')->execute([$id]);
            $insertVideo=$pdo->prepare('INSERT INTO product_videos(product_id,video_url,sort_order) VALUES(?,?,?)');
            foreach($videoUrls as $index=>$url)$insertVideo->execute([$id,$url,$index]);
            $this->saveSocialLinks($pdo, $id, array_map('trim', (array) ($_POST['social_url'] ?? [])));
            $this->syncProcurement($pdo, (int)$id, $procurementType, $costPrice, $stockQuantity);
            $this->syncVariants($pdo, (int) $id, $sku, $basePrice);
            $this->syncOptions($pdo, (int) $id);

            $pdo->commit();
        } catch (\Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $this->redirect('Product details could not be saved. ' . $e->getMessage(),'error',$id); }
        InventoryService::syncProduct($pdo, (int) $id);
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/admin/products/view')) {
            $this->redirectToView($id, 'basic', 'Product saved.');
        }
        $this->redirect('Product saved.');
    }

    private function handleSectionSave(PDO $pdo): never
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $section = (string) ($_POST['section'] ?? '');
        $tab = (string) ($_POST['tab'] ?? $section);
        if (!$id) {
            $this->redirect('Product not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $statement->execute([$id]);
        $product = $statement->fetch();
        if (!$product) {
            $this->redirect('Product not found.', 'error');
        }

        try {
            if ($section === 'basic') {
                $name = trim((string) ($_POST['name'] ?? ''));
                $categoryIds = array_values(array_filter(array_map('intval', (array) ($_POST['category_ids'] ?? []))));
                if ($name === '' || !$categoryIds) {
                    $this->redirectToView($id, $tab, 'Name and at least one category are required.', 'error', true);
                }
                $metaTitle = trim((string) ($_POST['meta_title'] ?? '')) ?: $name;
                $metaDescription = trim((string) ($_POST['meta_description'] ?? ''));
                $pdo->prepare('UPDATE products SET name=?, short_description=?, description=?, meta_title=?, meta_description=?, search_keywords=? WHERE id=?')->execute([
                    $name,
                    trim((string) ($_POST['short_description'] ?? '')),
                    trim((string) ($_POST['description'] ?? '')),
                    $metaTitle, $metaDescription,
                    trim((string) ($_POST['search_keywords'] ?? '')),
                    $id,
                ]);
                $pdo->prepare('DELETE FROM product_categories WHERE product_id=?')->execute([$id]);
                $relation = $pdo->prepare('INSERT INTO product_categories(product_id,category_id) VALUES(?,?)');
                foreach ($categoryIds as $categoryId) {
                    $relation->execute([$id, $categoryId]);
                }
            } elseif ($section === 'images') {
                $imageAltText = trim((string) ($_POST['image_alt_text'] ?? ''));
                if ($imageAltText === '') $imageAltText = (string) $product['name'];
                $deleteIds = array_values(array_filter(array_map('intval', (array) ($_POST['delete_images'] ?? []))));
                $existingImageIds = array_map('intval', $pdo->query('SELECT id FROM product_images WHERE product_id=' . (int) $id)->fetchAll(PDO::FETCH_COLUMN));
                $newImages = [];
                $files = $_FILES['product_images'] ?? null;
                if ($files && is_array($files['name'] ?? null)) {
                    foreach (array_keys($files['name']) as $index) {
                        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                        $newImages[] = $this->upload([
                            'name' => $files['name'][$index],
                            'type' => $files['type'][$index] ?? '',
                            'tmp_name' => $files['tmp_name'][$index] ?? '',
                            'error' => $files['error'][$index],
                            'size' => $files['size'][$index] ?? 0,
                        ]);
                    }
                }
                $remainingExisting = array_diff($existingImageIds, $deleteIds);
                if (!$remainingExisting && !$newImages) {
                    $this->redirectToView($id, $tab, 'Upload at least one product image.', 'error', true);
                }
                if ($deleteIds) {
                    $inQuery = implode(',', array_fill(0, count($deleteIds), '?'));
                    $deleteStmt = $pdo->prepare("DELETE FROM product_images WHERE id IN ($inQuery) AND product_id = ?");
                    $deleteStmt->execute(array_merge($deleteIds, [$id]));
                }
                $sort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM product_images WHERE product_id=' . (int) $id)->fetchColumn();
                $insertImage = $pdo->prepare('INSERT INTO product_images(product_id,image_path,alt_text,sort_order,is_primary) VALUES(?,?,?,?,0)');
                foreach ($newImages as $index => $path) {
                    $insertImage->execute([$id, $path, $imageAltText . ' ' . ($index + 1), ++$sort]);
                }
                $pdo->prepare('UPDATE product_images SET is_primary=0 WHERE product_id=?')->execute([$id]);
                $primaryId = $pdo->query('SELECT id FROM product_images WHERE product_id=' . (int) $id . ' ORDER BY sort_order,id LIMIT 1')->fetchColumn();
                if ($primaryId) {
                    $pdo->prepare('UPDATE product_images SET is_primary=1 WHERE id=?')->execute([$primaryId]);
                }
            } elseif ($section === 'social') {
                $videoUrls = array_values(array_unique(array_filter(
                    array_map('trim', (array) ($_POST['video_urls'] ?? [])),
                    static fn (string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false
                )));
                $pdo->prepare('UPDATE products SET video_url=? WHERE id=?')->execute([$videoUrls[0] ?? '', $id]);
                $pdo->prepare('DELETE FROM product_videos WHERE product_id=?')->execute([$id]);
                $insertVideo = $pdo->prepare('INSERT INTO product_videos(product_id,video_url,sort_order) VALUES(?,?,?)');
                foreach ($videoUrls as $index => $url) {
                    $insertVideo->execute([$id, $url, $index]);
                }
                $this->saveSocialLinks($pdo, $id, array_map('trim', (array) ($_POST['social_url'] ?? [])));
            } elseif ($section === 'pricing') {
                $basePrice = max(0, (float) ($_POST['selling_price'] ?? 0));
                $costPrice = max(0, (float) ($_POST['buying_price'] ?? 0));
                $procurementType = (string) ($_POST['procurement_type'] ?? '');
                if (!in_array($procurementType, ['handcrafted', 'purchased'], true)) {
                    $this->redirectToView($id, $tab, 'Select handcrafted or purchased externally.', 'error', true);
                }
                if ($basePrice <= 0 || $costPrice <= 0) {
                    $this->redirectToView($id, $tab, 'Selling price and cost price are required.', 'error', true);
                }
                $stockQuantity = $procurementType === 'handcrafted' ? 0 : max(0, (int) ($_POST['stock_quantity'] ?? 0));
                $pdo->prepare('UPDATE products SET base_price=?, cost_price=?, stock_quantity=?, procurement_type=?, is_featured=?, status=? WHERE id=?')->execute([
                    $basePrice,
                    $costPrice,
                    $stockQuantity,
                    $procurementType,
                    isset($_POST['is_featured']) ? 1 : 0,
                    ($_POST['status'] ?? '') === 'active' ? 'active' : 'draft',
                    $id,
                ]);
                $this->syncProcurement($pdo, (int)$id, $procurementType, $costPrice, $stockQuantity);
                $this->syncVariants($pdo, (int) $id, (string) $product['sku'], $basePrice);
            } elseif ($section === 'options') {
                $this->syncOptions($pdo, (int) $id);
            } else {
                $this->redirectToView($id, $tab, 'Unknown section.', 'error', true);
            }
        } catch (\Throwable $e) {
            $this->redirectToView($id, $tab, $e->getMessage(), 'error', true);
        }

        InventoryService::syncProduct($pdo, (int) $id);
        $this->redirectToView($id, $tab, 'Changes saved.');
    }

    private function ensureProductSchema(PDO $pdo): void
    {
        $columns = $pdo->query('DESCRIBE `products`')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('search_keywords', $columns, true)) {
            $pdo->exec('ALTER TABLE `products` ADD `search_keywords` TEXT NULL DEFAULT NULL');
        }
        if (!in_array('procurement_type', $columns, true)) {
            $pdo->exec("ALTER TABLE `products` ADD `procurement_type` ENUM('handcrafted','purchased') NOT NULL DEFAULT 'handcrafted' AFTER `cost_price`");
        }
        $pdo->exec("CREATE TABLE IF NOT EXISTS product_procurements (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,amount DECIMAL(12,2) NOT NULL DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,UNIQUE KEY uq_product_procurement(product_id),CONSTRAINT fk_product_procurement_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $procurementColumns = $pdo->query('DESCRIBE `product_procurements`')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('unit_cost', $procurementColumns, true)) $pdo->exec('ALTER TABLE `product_procurements` ADD `unit_cost` DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER `product_id`');
        if (!in_array('quantity', $procurementColumns, true)) $pdo->exec('ALTER TABLE `product_procurements` ADD `quantity` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `unit_cost`');
        $pdo->exec('UPDATE product_procurements pp INNER JOIN products p ON p.id=pp.product_id SET pp.unit_cost=p.cost_price,pp.quantity=p.stock_quantity WHERE pp.unit_cost=0 AND pp.quantity=0');
        $hasSocial = (bool) $pdo->query("SHOW TABLES LIKE 'product_social_links'")->fetchColumn();
        if (!$hasSocial) {
            $pdo->exec("CREATE TABLE `product_social_links` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `product_id` bigint(20) unsigned NOT NULL,
                `platform` enum('instagram','tiktok','facebook','youtube','whatsapp','website','other') NOT NULL DEFAULT 'other',
                `label` varchar(100) DEFAULT NULL,
                `url` varchar(500) NOT NULL,
                `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_product_social_product` (`product_id`),
                CONSTRAINT `fk_product_social_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
        $pdo->exec("CREATE TABLE IF NOT EXISTS product_variants (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,name VARCHAR(160) NOT NULL,sku VARCHAR(80) NOT NULL,color_name VARCHAR(120) NULL,color_hex CHAR(7) NULL,image_path VARCHAR(255) NULL,price_adjustment DECIMAL(12,2) NOT NULL DEFAULT 0,stock_quantity INT NOT NULL DEFAULT 0,status ENUM('active','inactive') NOT NULL DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,UNIQUE KEY sku(sku),KEY fk_product_variants_product(product_id),CONSTRAINT fk_product_variants_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS product_options (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            product_id bigint(20) unsigned NOT NULL,
            name varchar(160) NOT NULL,
            type enum('text','textarea','select','radio','checkbox','file','image_select') NOT NULL DEFAULT 'text',
            is_required tinyint(1) NOT NULL DEFAULT 0,
            sort_order int(10) unsigned NOT NULL DEFAULT 0,
            KEY fk_product_options_product (product_id),
            CONSTRAINT fk_product_options_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $pdo->exec("ALTER TABLE product_options MODIFY COLUMN type ENUM('text','textarea','select','radio','checkbox','file','image_select') NOT NULL DEFAULT 'text'");

        $pdo->exec("CREATE TABLE IF NOT EXISTS product_option_values (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            option_id bigint(20) unsigned NOT NULL,
            label varchar(160) NOT NULL,
            image_path varchar(255) DEFAULT NULL,
            price_adjustment decimal(12,2) NOT NULL DEFAULT 0.00,
            sort_order int(10) unsigned NOT NULL DEFAULT 0,
            KEY fk_product_option_values_option (option_id),
            CONSTRAINT fk_product_option_values_option FOREIGN KEY (option_id) REFERENCES product_options (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $optionValueCols = $pdo->query('DESCRIBE product_option_values')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('image_path', $optionValueCols, true)) {
            $pdo->exec("ALTER TABLE product_option_values ADD image_path varchar(255) DEFAULT NULL AFTER label");
        }
    }

    private function loadVariants(PDO $pdo, int $productId): array
    {
        $statement = $pdo->prepare('SELECT * FROM product_variants WHERE product_id=? ORDER BY id');
        $statement->execute([$productId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function loadOptions(PDO $pdo, int $productId): array
    {
        $statement = $pdo->prepare('SELECT * FROM product_options WHERE product_id=? ORDER BY sort_order, id');
        $statement->execute([$productId]);
        $options = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$options) return [];
        $optionIds = array_column($options, 'id');
        $inQuery = implode(',', array_fill(0, count($optionIds), '?'));
        $valStmt = $pdo->prepare("SELECT * FROM product_option_values WHERE option_id IN ($inQuery) ORDER BY sort_order, id");
        $valStmt->execute($optionIds);
        $values = $valStmt->fetchAll(PDO::FETCH_ASSOC);
        $valuesByOption = [];
        foreach ($values as $val) $valuesByOption[$val['option_id']][] = $val;
        foreach ($options as &$opt) $opt['values'] = $valuesByOption[$opt['id']] ?? [];
        return $options;
    }

    private function syncOptions(PDO $pdo, int $productId): void
    {
        $names = (array) ($_POST['option_name'] ?? []);
        $types = (array) ($_POST['option_type'] ?? []);
        $requireds = (array) ($_POST['option_required'] ?? []);
        $ids = (array) ($_POST['option_id'] ?? []);
        
        $valLabels = (array) ($_POST['optval_label'] ?? []);
        $valPrices = (array) ($_POST['optval_price'] ?? []);
        $valIds = (array) ($_POST['optval_id'] ?? []);
        $files = $_FILES['optval_image'] ?? null;
        
        $keepOptions = [];
        $keepValues = [];
        
        $saveOpt = $pdo->prepare("INSERT INTO product_options(id,product_id,name,type,is_required,sort_order) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),type=VALUES(type),is_required=VALUES(is_required),sort_order=VALUES(sort_order)");
        $saveVal = $pdo->prepare("INSERT INTO product_option_values(id,option_id,label,image_path,price_adjustment,sort_order) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE label=VALUES(label),image_path=IFNULL(VALUES(image_path), image_path),price_adjustment=VALUES(price_adjustment),sort_order=VALUES(sort_order)");
        
        $optSort = 0;
        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;
            $id = max(0, (int) ($ids[$index] ?? 0));
            $type = in_array($types[$index] ?? 'text', ['text','textarea','select','radio','checkbox','file','image_select'], true) ? $types[$index] : 'text';
            $req = !empty($requireds[$index]) ? 1 : 0;
            
            $saveOpt->execute([$id ?: null, $productId, $name, $type, $req, $optSort++]);
            $optId = $id ?: (int) $pdo->lastInsertId();
            $keepOptions[] = $optId;
            
            $labels = (array) ($valLabels[$index] ?? []);
            $prices = (array) ($valPrices[$index] ?? []);
            $vIds = (array) ($valIds[$index] ?? []);
            
            $valSort = 0;
            foreach ($labels as $vIndex => $label) {
                $label = trim((string) $label);
                if ($label === '') continue;
                $vId = max(0, (int) ($vIds[$vIndex] ?? 0));
                $price = (float) ($prices[$vIndex] ?? 0);
                
                $imgPath = null;
                if ($type === 'image_select' && isset($files['name'][$index][$vIndex]) && $files['error'][$index][$vIndex] !== UPLOAD_ERR_NO_FILE) {
                    try {
                        $imgPath = $this->upload([
                            'name' => $files['name'][$index][$vIndex],
                            'type' => $files['type'][$index][$vIndex] ?? '',
                            'tmp_name' => $files['tmp_name'][$index][$vIndex] ?? '',
                            'error' => $files['error'][$index][$vIndex],
                            'size' => $files['size'][$index][$vIndex] ?? 0
                        ]);
                    } catch (\Exception $e) {
                        // ignore error
                    }
                }
                
                $saveVal->execute([$vId ?: null, $optId, $label, $imgPath, $price, $valSort++]);
                $keepValues[] = $vId ?: (int) $pdo->lastInsertId();
            }
        }
        
        if ($keepValues) {
            $marks = implode(',', array_fill(0, count($keepValues), '?'));
            $pdo->prepare("DELETE FROM product_option_values WHERE option_id IN (SELECT id FROM product_options WHERE product_id=?) AND id NOT IN ($marks)")->execute(array_merge([$productId], $keepValues));
        } else {
            $pdo->prepare("DELETE FROM product_option_values WHERE option_id IN (SELECT id FROM product_options WHERE product_id=?)")->execute([$productId]);
        }
        
        if ($keepOptions) {
            $marks = implode(',', array_fill(0, count($keepOptions), '?'));
            $pdo->prepare("DELETE FROM product_options WHERE product_id=? AND id NOT IN ($marks)")->execute(array_merge([$productId], $keepOptions));
        } else {
            $pdo->prepare("DELETE FROM product_options WHERE product_id=?")->execute([$productId]);
        }
    }

    private function syncVariants(PDO $pdo, int $productId, string $productSku, float $basePrice): void
    {
        $names = (array) ($_POST['variant_name'] ?? []);
        $colors = (array) ($_POST['variant_color'] ?? []);
        $hexes = (array) ($_POST['variant_hex'] ?? []);
        $prices = (array) ($_POST['variant_price'] ?? []);
        $stocks = (array) ($_POST['variant_stock'] ?? []);
        $ids = (array) ($_POST['variant_id'] ?? []);
        $keep = [];
        $save = $pdo->prepare("INSERT INTO product_variants(id,product_id,name,sku,color_name,color_hex,price_adjustment,stock_quantity,status) VALUES(?,?,?,?,?,?,?,?, 'active') ON DUPLICATE KEY UPDATE name=VALUES(name),sku=VALUES(sku),color_name=VALUES(color_name),color_hex=VALUES(color_hex),price_adjustment=VALUES(price_adjustment),stock_quantity=VALUES(stock_quantity),status='active'");
        foreach ($names as $index => $rawName) {
            $name = trim((string) $rawName);
            if ($name === '') continue;
            $id = max(0, (int) ($ids[$index] ?? 0));
            $color = trim((string) ($colors[$index] ?? $name));
            $hex = strtoupper(trim((string) ($hexes[$index] ?? '')));
            if (!preg_match('/^#[0-9A-F]{6}$/', $hex)) $hex = null;
            $price = max(0, (float) ($prices[$index] ?? $basePrice));
            $adjustment = round($price - $basePrice, 2);
            $stock = max(0, (int) ($stocks[$index] ?? 0));
            $variantSku = substr($productSku . '-' . strtoupper(preg_replace('/[^A-Z0-9]+/i', '-', $name)), 0, 80);
            $save->execute([$id ?: null, $productId, $name, $variantSku, $color ?: $name, $hex, $adjustment, $stock]);
            $keep[] = $id ?: (int) $pdo->lastInsertId();
        }
        if ($keep) {
            $marks = implode(',', array_fill(0, count($keep), '?'));
            $pdo->prepare("DELETE FROM product_variants WHERE product_id=? AND id NOT IN ($marks)")->execute(array_merge([$productId], $keep));
        } else {
            $pdo->prepare('DELETE FROM product_variants WHERE product_id=?')->execute([$productId]);
        }
    }

    private function syncProcurement(PDO $pdo, int $productId, string $type, float $costPrice, int $stockQuantity): void
    {
        if ($type === 'handcrafted') {
            $pdo->prepare('DELETE FROM product_procurements WHERE product_id=?')->execute([$productId]);
            return;
        }
        $amount = round(max(0, $costPrice) * max(0, $stockQuantity), 2);
        $pdo->prepare('INSERT INTO product_procurements(product_id,unit_cost,quantity,amount) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE unit_cost=VALUES(unit_cost), quantity=VALUES(quantity), amount=VALUES(amount)')
            ->execute([$productId,$costPrice,$stockQuantity,$amount]);
    }

    /** @return array<int, array<string, mixed>> */
    private function loadSocialLinks(PDO $pdo, int $productId): array
    {
        $hasSocial = (bool) $pdo->query("SHOW TABLES LIKE 'product_social_links'")->fetchColumn();
        if (!$hasSocial) {
            return [];
        }
        $statement = $pdo->prepare('SELECT platform, label, url FROM product_social_links WHERE product_id = ? ORDER BY sort_order, id');
        $statement->execute([$productId]);
        return $statement->fetchAll();
    }

    /** @param array<int, string> $urls */
    private function saveSocialLinks(PDO $pdo, int $productId, array $urls): void
    {
        $hasSocial = (bool) $pdo->query("SHOW TABLES LIKE 'product_social_links'")->fetchColumn();
        if (!$hasSocial) {
            return;
        }
        $pdo->prepare('DELETE FROM product_social_links WHERE product_id=?')->execute([$productId]);
        $insert = $pdo->prepare('INSERT INTO product_social_links (product_id, platform, label, url, sort_order) VALUES (?,?,?,?,?)');
        $order = 0;
        foreach ($urls as $url) {
            $url = trim($url);
            if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
                continue;
            }
            $insert->execute([$productId, $this->detectSocialPlatform($url), null, $url, $order++]);
        }
    }

    private function detectSocialPlatform(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        return match (true) {
            str_contains($host, 'instagram') => 'instagram',
            str_contains($host, 'tiktok') => 'tiktok',
            str_contains($host, 'facebook') => 'facebook',
            str_contains($host, 'youtube') || str_contains($host, 'youtu.be') => 'youtube',
            str_contains($host, 'wa.me') || str_contains($host, 'whatsapp') => 'whatsapp',
            default => 'other',
        };
    }

    private function generateSku(PDO $pdo, string $name): string
    {
        $base = strtoupper(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $base = substr($base !== '' ? $base : 'PRODUCT', 0, 48);
        $prefix = 'GV-' . $base;
        $candidate = $prefix;
        $suffix = 1;
        $statement = $pdo->prepare('SELECT 1 FROM products WHERE sku = ? LIMIT 1');
        while (true) {
            $statement->execute([$candidate]);
            if (!$statement->fetchColumn()) return $candidate;
            $candidate = $prefix . '-' . str_pad((string) $suffix++, 3, '0', STR_PAD_LEFT);
        }
    }

    private function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int)($file['size'] ?? 0)>5*1024*1024) throw new \RuntimeException('Upload an image smaller than 5MB.');
        $info=@getimagesize((string)$file['tmp_name']); $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        if (!isset($types[$info['mime'] ?? ''])) throw new \RuntimeException('Use a JPG, PNG or WebP image.');
        $dir=BASE_PATH.'/public/assets/uploads/products'; if (!is_dir($dir)) mkdir($dir,0775,true);
        $name=bin2hex(random_bytes(16)).'.'.$types[$info['mime']]; if (!move_uploaded_file((string)$file['tmp_name'],$dir.'/'.$name)) throw new \RuntimeException('Image upload failed.');
        return '/assets/uploads/products/'.$name;
    }
    private function redirect(string $message,string $type='success',?int $id=null): never { $_SESSION['product_flash']=compact('message','type'); header('Location: ' . app_url('/admin/products'.($id?'?edit='.$id:'')),true,303); exit; }
    private function redirectToView(int $id, string $tab, string $message, string $type = 'success', bool $editMode = false): never
    {
        $_SESSION['product_flash'] = compact('message', 'type');
        $url = '/admin/products/view?id=' . $id . '&tab=' . rawurlencode($tab);
        if ($editMode) {
            $url .= '&mode=edit';
        }
        header('Location: ' . app_url($url), true, 303);
        exit;
    }
}
