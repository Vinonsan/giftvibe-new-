<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class ReviewController extends Controller
{
    private const HOMEPAGE_LIMIT = 6;

    public function index(): void
    {
        $pdo = Database::connection();
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->post($pdo);
        }

        // Every review stays visible here. "approved" now simply means that
        // the admin selected it for the homepage (maximum six).
        $reviews = $pdo->query(
            "SELECT * FROM testimonials ORDER BY (status = 'approved') DESC, sort_order, id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $edit = null;
        $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
        if ($id) {
            $statement = $pdo->prepare('SELECT * FROM testimonials WHERE id = ?');
            $statement->execute([$id]);
            $edit = $statement->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        $flash = $_SESSION['review_admin_flash'] ?? null;
        unset($_SESSION['review_admin_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Reviews',
            'showPageTitle' => false,
            'content' => $this->render('admin/reviews/index', [
                'reviews' => $reviews,
                'edit' => $edit,
                'flash' => $flash,
                'csrfToken' => $_SESSION['csrf_token'],
                'homepageLimit' => self::HOMEPAGE_LIMIT,
            ]),
        ]);
    }

    private function post(PDO $pdo): never
    {
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(419);
            exit('Invalid request token.');
        }

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $action = (string) ($_POST['action'] ?? 'save');

        if ($action === 'delete' && $id) {
            $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
            $this->go('Review deleted.');
        }

        if ($action === 'toggle_homepage' && $id) {
            $this->toggleHomepage($pdo, $id);
        }

        $name = trim((string) ($_POST['reviewer_name'] ?? ''));
        $text = trim((string) ($_POST['review_text'] ?? ''));
        if ($name === '' || $text === '') {
            $this->go('Name and review are required.', 'error', $id);
        }

        $avatar = '';
        $currentlyFeatured = false;
        if ($id) {
            $statement = $pdo->prepare('SELECT avatar_path, status FROM testimonials WHERE id = ?');
            $statement->execute([$id]);
            $existing = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
            $avatar = (string) ($existing['avatar_path'] ?? '');
            $currentlyFeatured = ($existing['status'] ?? '') === 'approved';
        }

        try {
            $avatar = $this->upload($_FILES['avatar'] ?? null) ?? $avatar;
        } catch (\RuntimeException $exception) {
            $this->go($exception->getMessage(), 'error', $id);
        }

        $featured = isset($_POST['show_on_homepage']);
        if ($featured && !$currentlyFeatured && $this->featuredCount($pdo) >= self::HOMEPAGE_LIMIT) {
            $this->go('Homepage already has 6 reviews. Remove one before selecting another.', 'error', $id);
        }

        $data = [
            $name,
            trim((string) ($_POST['reviewer_email'] ?? '')),
            trim((string) ($_POST['reviewer_role'] ?? '')),
            $avatar ?: null,
            trim((string) ($_POST['title'] ?? '')),
            $text,
            max(1, min(5, (int) ($_POST['rating'] ?? 5))),
            $featured ? 'approved' : 'pending',
            max(0, (int) ($_POST['sort_order'] ?? 0)),
        ];

        if ($id) {
            $pdo->prepare('UPDATE testimonials SET reviewer_name=?, reviewer_email=?, reviewer_role=?, avatar_path=?, title=?, review_text=?, rating=?, status=?, sort_order=? WHERE id=?')
                ->execute([...$data, $id]);
        } else {
            $pdo->prepare("INSERT INTO testimonials (reviewer_name, reviewer_email, reviewer_role, avatar_path, title, review_text, rating, status, sort_order, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin')")
                ->execute($data);
        }

        $this->go('Review saved.');
    }

    private function toggleHomepage(PDO $pdo, int $id): never
    {
        $statement = $pdo->prepare('SELECT status FROM testimonials WHERE id = ?');
        $statement->execute([$id]);
        $status = $statement->fetchColumn();
        if ($status === false) {
            $this->go('Review not found.', 'error');
        }

        if ($status !== 'approved' && $this->featuredCount($pdo) >= self::HOMEPAGE_LIMIT) {
            $this->go('You can show only 6 reviews on the homepage. Remove one first.', 'error');
        }

        $nextStatus = $status === 'approved' ? 'pending' : 'approved';
        $pdo->prepare('UPDATE testimonials SET status = ? WHERE id = ?')->execute([$nextStatus, $id]);
        $this->go($nextStatus === 'approved' ? 'Review added to the homepage.' : 'Review removed from the homepage.');
    }

    private function featuredCount(PDO $pdo): int
    {
        return (int) $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'approved'")->fetchColumn();
    }

    private function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new \RuntimeException('Upload a profile image smaller than 3MB.');
        }
        $info = @getimagesize((string) $file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($types[$info['mime'] ?? ''])) {
            throw new \RuntimeException('Use a JPG, PNG or WebP profile image.');
        }
        $directory = BASE_PATH . '/public/assets/uploads/testimonials';
        if (!is_dir($directory)) mkdir($directory, 0775, true);
        $name = bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
        if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $name)) {
            throw new \RuntimeException('Profile image upload failed.');
        }
        return '/assets/uploads/testimonials/' . $name;
    }

    private function go(string $message, string $type = 'success', ?int $id = null): never
    {
        $_SESSION['review_admin_flash'] = compact('message', 'type');
        header('Location: ' . app_url('/admin/reviews' . ($id ? '?edit=' . $id : '')), true, 303);
        exit;
    }
}
