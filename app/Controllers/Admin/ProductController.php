<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
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
        $editProduct = null; $editCategoryIds = []; $secondaryImages = []; $productVideos = []; $productSocialLinks = [];
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
        }
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['product_flash'] ?? null; unset($_SESSION['product_flash']);
        $this->view('layouts/admin-layout', ['title'=>'Products','pageTitle'=>'Products','showPageTitle'=>false,'content'=>$this->render('admin/products/index', compact('products','categories','editProduct','editCategoryIds','flash','secondaryImages','productVideos','productSocialLinks') + ['csrfToken'=>$_SESSION['csrf_token']])]);
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
        $editProduct = $product;

        $tabs = ['basic', 'keywords', 'images', 'social', 'pricing'];
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
        $name = trim((string) ($_POST['name'] ?? '')); $sku = strtoupper(trim((string) ($_POST['sku'] ?? '')));
        $categoryIds = array_values(array_filter(array_map('intval', (array) ($_POST['category_ids'] ?? []))));
        if ($name === '' || $sku === '' || !$categoryIds) $this->redirect('Name, SKU and at least one category are required.', 'error', $id);
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        if ($id) {
            $slugStatement = $pdo->prepare('SELECT slug FROM products WHERE id=?');
            $slugStatement->execute([$id]);
            $existingSlug = trim((string) $slugStatement->fetchColumn());
            if ($existingSlug !== '') $slug = $existingSlug;
        }
        $basePrice = max(0, (float) ($_POST['selling_price'] ?? 0));
        $costPrice = max(0, (float) ($_POST['buying_price'] ?? 0));
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
        
        $data = [$sku,$name,$slug,trim((string)($_POST['short_description'] ?? '')),trim((string)($_POST['description'] ?? '')),$basePrice,$costPrice,max(0,(int)($_POST['stock_quantity'] ?? 0)),isset($_POST['is_featured'])?1:0,($_POST['status'] ?? '')==='active'?'active':'draft',$videoUrl,$metaTitle,$metaDescription,$searchKeywords];
        $pdo->beginTransaction();
        try {
            if ($id) { 
                $pdo->prepare('UPDATE products SET sku=?,name=?,slug=?,short_description=?,description=?,base_price=?,cost_price=?,sale_price=NULL,stock_quantity=?,is_featured=?,status=?,video_url=?,meta_title=?,meta_description=?,search_keywords=? WHERE id=?')->execute([...$data,$id]); 
            }
            else { 
                $pdo->prepare('INSERT INTO products (sku,name,slug,short_description,description,base_price,cost_price,sale_price,stock_quantity,is_featured,status,video_url,meta_title,meta_description,search_keywords) VALUES (?,?,?,?,?,?,?,NULL,?,?,?,?,?,?,?)')->execute($data); 
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

            $pdo->commit();
        } catch (\Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $this->redirect('SKU or product details already exist. ' . $e->getMessage(),'error',$id); }
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
                $sku = strtoupper(trim((string) ($_POST['sku'] ?? '')));
                $categoryIds = array_values(array_filter(array_map('intval', (array) ($_POST['category_ids'] ?? []))));
                if ($name === '' || $sku === '' || !$categoryIds) {
                    $this->redirectToView($id, $tab, 'Name, SKU and at least one category are required.', 'error', true);
                }
                $metaTitle = trim((string) ($_POST['meta_title'] ?? '')) ?: $name;
                $metaDescription = trim((string) ($_POST['meta_description'] ?? ''));
                $pdo->prepare('UPDATE products SET sku=?, name=?, short_description=?, description=?, meta_title=?, meta_description=? WHERE id=?')->execute([
                    $sku, $name,
                    trim((string) ($_POST['short_description'] ?? '')),
                    trim((string) ($_POST['description'] ?? '')),
                    $metaTitle, $metaDescription, $id,
                ]);
                $pdo->prepare('DELETE FROM product_categories WHERE product_id=?')->execute([$id]);
                $relation = $pdo->prepare('INSERT INTO product_categories(product_id,category_id) VALUES(?,?)');
                foreach ($categoryIds as $categoryId) {
                    $relation->execute([$id, $categoryId]);
                }
            } elseif ($section === 'keywords') {
                $pdo->prepare('UPDATE products SET search_keywords=? WHERE id=?')->execute([
                    trim((string) ($_POST['search_keywords'] ?? '')), $id,
                ]);
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
                if ($basePrice <= 0 || $costPrice <= 0) {
                    $this->redirectToView($id, $tab, 'Selling price and cost price are required.', 'error', true);
                }
                $pdo->prepare('UPDATE products SET base_price=?, cost_price=?, stock_quantity=?, is_featured=?, status=? WHERE id=?')->execute([
                    $basePrice,
                    $costPrice,
                    max(0, (int) ($_POST['stock_quantity'] ?? 0)),
                    isset($_POST['is_featured']) ? 1 : 0,
                    ($_POST['status'] ?? '') === 'active' ? 'active' : 'draft',
                    $id,
                ]);
            } else {
                $this->redirectToView($id, $tab, 'Unknown section.', 'error', true);
            }
        } catch (\Throwable $e) {
            $this->redirectToView($id, $tab, $e->getMessage(), 'error', true);
        }

        $this->redirectToView($id, $tab, 'Changes saved.');
    }

    private function ensureProductSchema(PDO $pdo): void
    {
        $columns = $pdo->query('DESCRIBE `products`')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('search_keywords', $columns, true)) {
            $pdo->exec('ALTER TABLE `products` ADD `search_keywords` TEXT NULL DEFAULT NULL');
        }
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
    private function redirect(string $message,string $type='success',?int $id=null): never { $_SESSION['product_flash']=compact('message','type'); header('Location: /admin/products'.($id?'?edit='.$id:''),true,303); exit; }
    private function redirectToView(int $id, string $tab, string $message, string $type = 'success', bool $editMode = false): never
    {
        $_SESSION['product_flash'] = compact('message', 'type');
        $url = '/admin/products/view?id=' . $id . '&tab=' . rawurlencode($tab);
        if ($editMode) {
            $url .= '&mode=edit';
        }
        header('Location: ' . $url, true, 303);
        exit;
    }
}
