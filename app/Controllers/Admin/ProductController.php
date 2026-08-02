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
        $editProduct = null; $editCategoryIds = [];
        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($editId) {
            $statement = $pdo->prepare("SELECT p.*,pi.image_path FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 WHERE p.id=?"); $statement->execute([$editId]);
            $editProduct = $statement->fetch() ?: null;
            $statement = $pdo->prepare('SELECT category_id FROM product_categories WHERE product_id=?'); $statement->execute([$editId]); $editCategoryIds = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
        }
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['product_flash'] ?? null; unset($_SESSION['product_flash']);
        $this->view('layouts/admin-layout', ['title'=>'Products','pageTitle'=>'Products','showPageTitle'=>false,'content'=>$this->render('admin/products/index', compact('products','categories','editProduct','editCategoryIds','flash') + ['csrfToken'=>$_SESSION['csrf_token']])]);
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
        if ($id) $slug .= '-' . $id;
        $basePrice = max(0, (float) ($_POST['base_price'] ?? 0));
        $saleInput = trim((string) ($_POST['sale_price'] ?? '')); $salePrice = $saleInput === '' ? null : max(0, (float) $saleInput);
        $image = '';
        if ($id) { $s=$pdo->prepare('SELECT image_path FROM product_images WHERE product_id=? AND is_primary=1'); $s->execute([$id]); $image=(string)($s->fetchColumn() ?: ''); }
        try { $image = $this->upload($_FILES['product_image'] ?? null) ?? $image; } catch (\RuntimeException $e) { $this->redirect($e->getMessage(),'error',$id); }
        if ($image === '') $this->redirect('Product image is required.','error',$id);
        $data = [$sku,$name,$slug,trim((string)($_POST['short_description'] ?? '')),$basePrice,$salePrice,max(0,(int)($_POST['stock_quantity'] ?? 0)),isset($_POST['is_featured'])?1:0,($_POST['status'] ?? '')==='active'?'active':'draft'];
        $pdo->beginTransaction();
        try {
            if ($id) { $pdo->prepare('UPDATE products SET sku=?,name=?,slug=?,short_description=?,base_price=?,sale_price=?,stock_quantity=?,is_featured=?,status=? WHERE id=?')->execute([...$data,$id]); }
            else { $pdo->prepare('INSERT INTO products (sku,name,slug,short_description,base_price,sale_price,stock_quantity,is_featured,status) VALUES (?,?,?,?,?,?,?,?,?)')->execute($data); $id=(int)$pdo->lastInsertId(); }
            $pdo->prepare('DELETE FROM product_categories WHERE product_id=?')->execute([$id]);
            $relation=$pdo->prepare('INSERT INTO product_categories(product_id,category_id) VALUES(?,?)'); foreach ($categoryIds as $categoryId) $relation->execute([$id,$categoryId]);
            $pdo->prepare('DELETE FROM product_images WHERE product_id=? AND is_primary=1')->execute([$id]);
            $pdo->prepare('INSERT INTO product_images(product_id,image_path,alt_text,sort_order,is_primary) VALUES(?,?,?,0,1)')->execute([$id,$image,$name]);
            $pdo->commit();
        } catch (\Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $this->redirect('SKU or product details already exist.','error',$id); }
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
