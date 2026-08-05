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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->handlePost($pdo);
        $products = $pdo->query("SELECT p.*,pi.image_path,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') category_names FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id GROUP BY p.id ORDER BY p.id DESC")->fetchAll();
        $categories = $pdo->query("SELECT id,name FROM categories WHERE status='active' ORDER BY sort_order,id")->fetchAll();
        $editProduct = null; $editCategoryIds = []; $secondaryImages = []; $productVideos = [];
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
        }
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['product_flash'] ?? null; unset($_SESSION['product_flash']);
        $this->view('layouts/admin-layout', ['title'=>'Products','pageTitle'=>'Products','showPageTitle'=>false,'content'=>$this->render('admin/products/index', compact('products','categories','editProduct','editCategoryIds','flash','secondaryImages','productVideos') + ['csrfToken'=>$_SESSION['csrf_token']])]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) { http_response_code(419); exit('Invalid request token.'); }
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
        $data = [$sku,$name,$slug,trim((string)($_POST['short_description'] ?? '')),trim((string)($_POST['description'] ?? '')),$basePrice,$costPrice,max(0,(int)($_POST['stock_quantity'] ?? 0)),isset($_POST['is_featured'])?1:0,($_POST['status'] ?? '')==='active'?'active':'draft',$videoUrl,$metaTitle,$metaDescription];
        $pdo->beginTransaction();
        try {
            if ($id) { 
                $pdo->prepare('UPDATE products SET sku=?,name=?,slug=?,short_description=?,description=?,base_price=?,cost_price=?,sale_price=NULL,stock_quantity=?,is_featured=?,status=?,video_url=?,meta_title=?,meta_description=? WHERE id=?')->execute([...$data,$id]); 
            }
            else { 
                $pdo->prepare('INSERT INTO products (sku,name,slug,short_description,description,base_price,cost_price,sale_price,stock_quantity,is_featured,status,video_url,meta_title,meta_description) VALUES (?,?,?,?,?,?,?,NULL,?,?,?,?,?,?)')->execute($data); 
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

            $pdo->commit();
        } catch (\Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $this->redirect('SKU or product details already exist. ' . $e->getMessage(),'error',$id); }
        $this->redirect('Product saved.');
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
}
