<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class ReviewController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $general = $pdo->query("SELECT google_review_url FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        $googleReviewUrl = $general['google_review_url'] ?? '';

        if (!empty($googleReviewUrl)) {
            header('Location: ' . $googleReviewUrl, true, 302);
            exit;
        }

        header('Location: /', true, 302);
        exit;
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('Invalid request.', 'error');
        }

        // Spam prevention honeypot
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            $this->redirect('Thank you.');
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            $this->redirect('Your session expired. Please try again.', 'error');
        }

        $name = trim((string) ($_POST['reviewer_name'] ?? ''));
        $email = filter_var(trim((string) ($_POST['reviewer_email'] ?? '')), FILTER_VALIDATE_EMAIL);
        $role = trim((string) ($_POST['reviewer_role'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        $text = trim((string) ($_POST['review_text'] ?? ''));
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));

        if ($name === '' || !$email || mb_strlen($text) < 20) {
            $this->redirect('Name, valid email and a review of at least 20 characters are required.', 'error');
        }

        // Handle optional profile image upload
        $avatarPath = null;
        try {
            $avatarPath = $this->upload($_FILES['avatar'] ?? null);
        } catch (\RuntimeException $e) {
            $this->redirect($e->getMessage(), 'error');
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare("INSERT INTO testimonials(reviewer_name, reviewer_email, reviewer_role, avatar_path, title, review_text, rating, source, status, sort_order) VALUES(?, ?, ?, ?, ?, ?, ?, 'public', 'pending', 0)");
        $stmt->execute([$name, $email, $role ?: null, $avatarPath, $title, $text, $rating]);

        $this->redirect('Thank you! Your review has been submitted for approval.');
    }

    private function upload(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? 1) !== UPLOAD_ERR_OK || (int)($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new \RuntimeException('Upload a profile image smaller than 3MB.');
        }
        $info = @getimagesize((string)$file['tmp_name']);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($types[$info['mime'] ?? ''])) {
            throw new \RuntimeException('Use a JPG, PNG or WebP profile image.');
        }
        $dir = BASE_PATH . '/public/assets/uploads/testimonials';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $name = bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
        if (!move_uploaded_file((string)$file['tmp_name'], $dir . '/' . $name)) {
            throw new \RuntimeException('Profile image upload failed.');
        }
        return '/assets/uploads/testimonials/' . $name;
    }

    private function redirect(string $message, string $type = 'success'): never
    {
        $_SESSION['review_flash'] = compact('message', 'type');
        header('Location: /reviews', true, 303);
        exit;
    }
}
