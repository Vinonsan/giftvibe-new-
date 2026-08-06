<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class HeroController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
            return;
        }

        $slides = $pdo->query("SELECT * FROM banners WHERE placement = 'hero' ORDER BY sort_order, id")->fetchAll();
        $editSlide = null;
        $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

        if ($editId) {
            $statement = $pdo->prepare("SELECT * FROM banners WHERE id = ? AND placement = 'hero'");
            $statement->execute([$editId]);
            $editSlide = $statement->fetch() ?: null;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['hero_flash'] ?? null;
        unset($_SESSION['hero_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Hero banners',
            'pageTitle' => 'Hero banners',
            'showPageTitle' => false,
            'content' => $this->render('admin/hero/index', [
                'slides' => $slides,
                'editSlide' => $editSlide,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    private function handlePost(PDO $pdo): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(419);
            echo 'Invalid or expired request token.';
            return;
        }

        $action = (string) ($_POST['action'] ?? 'save');
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;

        if ($action === 'delete' && $id) {
            $statement = $pdo->prepare("DELETE FROM banners WHERE id = ? AND placement = 'hero'");
            $statement->execute([$id]);
            $this->redirectWithMessage('Hero slide deleted.');
        }

        $title = trim((string) ($_POST['title'] ?? ''));
        $imagePath = '';

        if ($id) {
            $existingStatement = $pdo->prepare("SELECT image_path FROM banners WHERE id = ? AND placement = 'hero'");
            $existingStatement->execute([$id]);
            $imagePath = (string) ($existingStatement->fetchColumn() ?: '');
        }

        try {
            $uploadedPath = $this->storeUploadedImage($_FILES['hero_image'] ?? null);
            if ($uploadedPath !== null) {
                $imagePath = $uploadedPath;
            }
        } catch (\RuntimeException $exception) {
            $this->redirectWithMessage($exception->getMessage(), 'error', $id);
        }

        if ($title === '' || $imagePath === '') {
            $this->redirectWithMessage('Title and image path are required.', 'error', $id);
        }

        $eyebrow = $this->cleanPart($_POST['eyebrow'] ?? '');
        $description = $this->cleanPart($_POST['description'] ?? '');
        $price = $this->cleanPart($_POST['price'] ?? '');
        $buttonLabel = $this->cleanPart($_POST['button_label'] ?? 'Shop now');
        $background = strtoupper(trim((string) ($_POST['background'] ?? '#0B1528')));
        $background = preg_match('/^#[0-9A-F]{6}$/', $background) ? $background : '#0B1528';
        $subtitle = implode('|', [$eyebrow, $description, $price, $buttonLabel]);
        $linkUrl = trim((string) ($_POST['link_url'] ?? '/shop')) ?: '/shop';
        $sortOrder = max(0, (int) ($_POST['sort_order'] ?? 0));
        $status = ($_POST['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive';

        if ($id) {
            $statement = $pdo->prepare('UPDATE banners SET title = ?, subtitle = ?, image_path = ?, link_url = ?, background_color = ?, sort_order = ?, status = ? WHERE id = ? AND placement = \'hero\'');
            $statement->execute([$title, $subtitle, $imagePath, $linkUrl, $background, $sortOrder, $status, $id]);
            $message = 'Hero slide updated.';
        } else {
            $statement = $pdo->prepare("INSERT INTO banners (title, subtitle, image_path, link_url, background_color, placement, sort_order, status) VALUES (?, ?, ?, ?, ?, 'hero', ?, ?)");
            $statement->execute([$title, $subtitle, $imagePath, $linkUrl, $background, $sortOrder, $status]);
            $message = 'Hero slide created.';
        }

        $this->redirectWithMessage($message);
    }

    private function cleanPart(mixed $value): string
    {
        return str_replace('|', '', trim((string) $value));
    }

    private function storeUploadedImage(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('The image upload failed. Please try again.');
        }

        if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException('The image must be 5MB or smaller.');
        }

        $temporaryPath = (string) ($file['tmp_name'] ?? '');
        if ($temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
            throw new \RuntimeException('The uploaded image is invalid.');
        }

        $imageInfo = @getimagesize($temporaryPath);
        $mime = is_array($imageInfo) ? (string) ($imageInfo['mime'] ?? '') : '';
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($extensions[$mime])) {
            throw new \RuntimeException('Upload a valid JPG, PNG or WebP image.');
        }

        $uploadDirectory = BASE_PATH . '/public/assets/uploads/hero';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
            throw new \RuntimeException('The image upload directory is unavailable.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($temporaryPath, $uploadDirectory . '/' . $fileName)) {
            throw new \RuntimeException('The uploaded image could not be saved.');
        }

        return '/assets/uploads/hero/' . $fileName;
    }

    private function redirectWithMessage(string $message, string $type = 'success', ?int $editId = null): never
    {
        $_SESSION['hero_flash'] = ['message' => $message, 'type' => $type];
        $location = '/admin/hero' . ($editId ? '?edit=' . $editId : '');
        header('Location: ' . $location, true, 303);
        exit;
    }
}
