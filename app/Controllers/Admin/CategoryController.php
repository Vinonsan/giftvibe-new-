<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class CategoryController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $columns = $pdo->query('DESCRIBE categories')->fetchAll(PDO::FETCH_COLUMN);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo, $columns);
        }

        $order = in_array('sort_order', $columns, true) ? 'sort_order' : (in_array('display_order', $columns, true) ? 'display_order' : 'id');
        $categories = $pdo->query("SELECT * FROM categories ORDER BY {$order}, id")->fetchAll();
        $editCategory = null;
        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($editId) {
            $statement = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
            $statement->execute([$editId]);
            $editCategory = $statement->fetch() ?: null;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['category_flash'] ?? null;
        unset($_SESSION['category_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Categories', 'pageTitle' => 'Categories', 'showPageTitle' => false,
            'content' => $this->render('admin/categories/index', compact('categories', 'editCategory', 'flash') + ['csrfToken' => $_SESSION['csrf_token']]),
        ]);
    }

    private function handlePost(PDO $pdo, array $columns): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419); exit('Invalid or expired request token.');
        }
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        if (($_POST['action'] ?? 'save') === 'delete' && $id) {
            $statement = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $statement->execute([$id]);
            $this->redirect('Category deleted.');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') $this->redirect('Category name is required.', 'error', $id);
        $linkUrl = trim((string) ($_POST['link_url'] ?? ''));
        if ($linkUrl === '') $this->redirect('Category URL is required.', 'error', $id);
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $image = '';
        if ($id) {
            $statement = $pdo->prepare('SELECT * FROM categories WHERE id = ?'); $statement->execute([$id]);
            $row = $statement->fetch() ?: [];
            $image = (string) ($row['image_path'] ?? $row['image'] ?? '');
        }
        try { $image = $this->upload($_FILES['category_image'] ?? null) ?? $image; }
        catch (\RuntimeException $e) { $this->redirect($e->getMessage(), 'error', $id); }
        if ($image === '') $this->redirect('Category image is required.', 'error', $id);

        $data = ['name' => $name];
        if (in_array('slug', $columns, true)) $data['slug'] = $slug;
        if (in_array('link_url', $columns, true)) $data['link_url'] = $linkUrl;
        $imageColumn = in_array('image_path', $columns, true) ? 'image_path' : (in_array('image', $columns, true) ? 'image' : null);
        if ($imageColumn) $data[$imageColumn] = $image;
        if (!$id && in_array('status', $columns, true)) $data['status'] = 'active';
        if (!$id && in_array('is_active', $columns, true)) $data['is_active'] = 1;

        if ($id) {
            $sets = implode(', ', array_map(fn ($key) => "{$key} = ?", array_keys($data)));
            $statement = $pdo->prepare("UPDATE categories SET {$sets} WHERE id = ?");
            $statement->execute([...array_values($data), $id]);
            $this->redirect('Category updated.');
        }
        $keys = array_keys($data);
        $statement = $pdo->prepare('INSERT INTO categories (' . implode(', ', $keys) . ') VALUES (' . implode(', ', array_fill(0, count($keys), '?')) . ')');
        $statement->execute(array_values($data));
        $this->redirect('Category added.');
    }

    private function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 5 * 1024 * 1024) throw new \RuntimeException('Upload a category image smaller than 5MB.');
        $info = @getimagesize((string) $file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($types[$info['mime'] ?? ''])) throw new \RuntimeException('Use a JPG, PNG or WebP image.');
        $directory = BASE_PATH . '/public/assets/uploads/categories';
        if (!is_dir($directory)) mkdir($directory, 0775, true);
        $name = bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $name)) throw new \RuntimeException('The image could not be saved.');
        return '/assets/uploads/categories/' . $name;
    }

    private function redirect(string $message, string $type = 'success', ?int $id = null): never
    {
        $_SESSION['category_flash'] = compact('message', 'type');
        header('Location: /admin/categories' . ($id ? '?edit=' . $id : ''), true, 303); exit;
    }
}
