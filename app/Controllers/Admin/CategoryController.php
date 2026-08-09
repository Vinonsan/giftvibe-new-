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
        $this->ensureImageAltColumn($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
        }

        $categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order, id')->fetchAll();
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
            'title' => 'Categories',
            'pageTitle' => 'Categories',
            'showPageTitle' => false,
            'content' => $this->render('admin/categories/index', compact('categories', 'editCategory', 'flash') + ['csrfToken' => $_SESSION['csrf_token']]),
        ]);
    }

    public function show(): void
    {
        $pdo = Database::connection();
        $this->ensureImageAltColumn($pdo);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->redirect('Category not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $statement->execute([$id]);
        $category = $statement->fetch();
        if (!$category) {
            $this->redirect('Category not found.', 'error');
        }

        $tabs = ['basic', 'image', 'settings'];
        $tab = strtolower((string) ($_GET['tab'] ?? 'basic'));
        if (!in_array($tab, $tabs, true)) {
            $tab = 'basic';
        }
        $editMode = (($_GET['mode'] ?? '') === 'edit');
        $editCategory = $category;

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['category_flash'] ?? null;
        unset($_SESSION['category_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'View Category',
            'pageTitle' => 'View Category',
            'showPageTitle' => false,
            'content' => $this->render('admin/categories/show', compact('category', 'editCategory', 'tab', 'editMode', 'flash') + ['csrfToken' => $_SESSION['csrf_token']]),
        ]);
    }

    private function handlePost(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid or expired request token.');
        }

        if (($_POST['action'] ?? 'save') === 'save_section') {
            $this->handleSectionSave($pdo);
        }

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        if (($_POST['action'] ?? 'save') === 'delete' && $id) {
            $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            $this->redirect('Category deleted.');
        }

        $payload = $this->readCategoryPayload($pdo, $id);
        if ($payload['error'] !== '') {
            $this->redirect($payload['error'], 'error', $id);
        }

        if ($id) {
            $pdo->prepare('UPDATE categories SET name=?, slug=?, description=?, link_url=?, image_path=?, image_alt_text=?, sort_order=?, status=?, meta_title=?, meta_description=? WHERE id=?')->execute([
                $payload['name'], $payload['slug'], $payload['description'], $payload['link_url'], $payload['image'], $payload['image_alt_text'],
                $payload['sort_order'], $payload['status'], $payload['meta_title'], $payload['meta_description'], $id,
            ]);
            $this->redirect('Category updated.');
        }

        $pdo->prepare('INSERT INTO categories (name, slug, description, link_url, image_path, image_alt_text, sort_order, status, meta_title, meta_description) VALUES (?,?,?,?,?,?,?,?,?,?)')->execute([
            $payload['name'], $payload['slug'], $payload['description'], $payload['link_url'], $payload['image'], $payload['image_alt_text'],
            $payload['sort_order'], $payload['status'], $payload['meta_title'], $payload['meta_description'],
        ]);
        $this->redirect('Category added.');
    }

    private function handleSectionSave(PDO $pdo): never
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $section = (string) ($_POST['section'] ?? '');
        $tab = (string) ($_POST['tab'] ?? $section);
        if (!$id) {
            $this->redirect('Category not found.', 'error');
        }

        $statement = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $statement->execute([$id]);
        $existing = $statement->fetch();
        if (!$existing) {
            $this->redirect('Category not found.', 'error');
        }

        try {
            if ($section === 'basic') {
                $name = trim((string) ($_POST['name'] ?? ''));
                if ($name === '') {
                    $this->redirectToView($id, $tab, 'Category name is required.', 'error', true);
                }
                $slug = $this->uniqueSlug($pdo, $name, $id);
                $linkUrl = '/shop?category=' . rawurlencode($slug);
                $pdo->prepare('UPDATE categories SET name=?, slug=?, description=?, link_url=? WHERE id=?')->execute([
                    $name, $slug, (string) ($existing['description'] ?? ''), $linkUrl, $id,
                ]);
            } elseif ($section === 'image') {
                $image = (string) ($existing['image_path'] ?? '');
                try {
                    $image = $this->upload($_FILES['category_image'] ?? null) ?? $image;
                } catch (\RuntimeException $e) {
                    $this->redirectToView($id, $tab, $e->getMessage(), 'error', true);
                }
                if ($image === '') {
                    $this->redirectToView($id, $tab, 'Category image is required.', 'error', true);
                }
                $pdo->prepare('UPDATE categories SET image_path=? WHERE id=?')->execute([$image, $id]);
            } elseif ($section === 'settings') {
                $pdo->prepare('UPDATE categories SET sort_order=?, status=?, meta_title=?, meta_description=? WHERE id=?')->execute([
                    max(0, (int) ($_POST['sort_order'] ?? 0)),
                    ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive',
                    trim((string) ($_POST['meta_title'] ?? '')),
                    trim((string) ($_POST['meta_description'] ?? '')),
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

    /** @return array{name:string,slug:string,description:string,link_url:string,image:string,sort_order:int,status:string,meta_title:string,meta_description:string,error:string} */
    private function readCategoryPayload(PDO $pdo, ?int $id): array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            return ['error' => 'Category name is required.'] + $this->emptyPayload();
        }
        $slug = $this->uniqueSlug($pdo, $name, $id);
        $linkUrl = '/shop?category=' . rawurlencode($slug);

        $existing = [];
        $image = '';
        if ($id) {
            $statement = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
            $statement->execute([$id]);
            $existing = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
            $image = (string) ($existing['image_path'] ?? '');
        }
        try {
            $image = $this->upload($_FILES['category_image'] ?? null) ?? $image;
        } catch (\RuntimeException $e) {
            return ['error' => $e->getMessage()] + $this->emptyPayload();
        }
        if ($image === '') {
            return ['error' => 'Category image is required.'] + $this->emptyPayload();
        }
        $imageAltText = trim((string) ($_POST['image_alt_text'] ?? ''));
        if ($imageAltText === '') {
            return ['error' => 'Image alt words are required.'] + $this->emptyPayload();
        }

        return [
            'error' => '',
            'name' => $name,
            'slug' => $slug,
            'description' => (string) ($existing['description'] ?? ''),
            'link_url' => $linkUrl,
            'image' => $image,
            'image_alt_text' => $imageAltText,
            'sort_order' => $id ? (int) ($existing['sort_order'] ?? 0) : (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM categories')->fetchColumn(),
            'status' => (string) ($existing['status'] ?? 'active'),
            'meta_title' => (string) ($existing['meta_title'] ?? ''),
            'meta_description' => (string) ($existing['meta_description'] ?? ''),
        ];
    }

    private function uniqueSlug(PDO $pdo, string $name, ?int $excludeId = null): string
    {
        $base = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $base = $base !== '' ? $base : 'category';
        $slug = $base;
        $suffix = 2;

        $sql = 'SELECT id FROM categories WHERE slug = ?' . ($excludeId ? ' AND id <> ?' : '') . ' LIMIT 1';
        $statement = $pdo->prepare($sql);
        while (true) {
            $statement->execute($excludeId ? [$slug, $excludeId] : [$slug]);
            if (!$statement->fetchColumn()) {
                return $slug;
            }
            $slug = $base . '-' . $suffix++;
        }
    }

    /** @return array{name:string,slug:string,description:string,link_url:string,image:string,sort_order:int,status:string,meta_title:string,meta_description:string,error:string} */
    private function emptyPayload(): array
    {
        return [
            'name' => '', 'slug' => '', 'description' => '', 'link_url' => '', 'image' => '', 'image_alt_text' => '',
            'sort_order' => 0, 'status' => 'active', 'meta_title' => '', 'meta_description' => '', 'error' => '',
        ];
    }

    private function ensureImageAltColumn(PDO $pdo): void
    {
        $statement = $pdo->query("SHOW COLUMNS FROM categories LIKE 'image_alt_text'");
        if (!$statement->fetch()) {
            $pdo->exec("ALTER TABLE categories ADD COLUMN image_alt_text VARCHAR(255) NOT NULL DEFAULT '' AFTER image_path");
        }
    }

    private function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('Upload a category image smaller than 5MB.');
        }
        $info = @getimagesize((string) $file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($types[$info['mime'] ?? ''])) {
            throw new \RuntimeException('Use a JPG, PNG or WebP image.');
        }
        $directory = BASE_PATH . '/public/assets/uploads/categories';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        $name = bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $name)) {
            throw new \RuntimeException('The image could not be saved.');
        }
        return '/assets/uploads/categories/' . $name;
    }

    private function redirect(string $message, string $type = 'success', ?int $id = null): never
    {
        $_SESSION['category_flash'] = compact('message', 'type');
        header('Location: ' . app_url('/admin/categories' . ($id ? '?edit=' . $id : '')), true, 303);
        exit;
    }

    private function redirectToView(int $id, string $tab, string $message, string $type = 'success', bool $editMode = false): never
    {
        $_SESSION['category_flash'] = compact('message', 'type');
        $url = '/admin/categories/view?id=' . $id . '&tab=' . rawurlencode($tab);
        if ($editMode) {
            $url .= '&mode=edit';
        }
        header('Location: ' . app_url($url), true, 303);
        exit;
    }
}
