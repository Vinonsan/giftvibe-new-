<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class SettingController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo);
            return;
        }

        // Get all settings
        $settingsRaw = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['settings_flash'] ?? null;
        unset($_SESSION['settings_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Settings',
            'pageTitle' => 'Settings',
            'showPageTitle' => false,
            'content' => $this->render('admin/settings/index', [
                'settings' => $settingsRaw,
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

        // 1. Save standard text settings
        $keysToSave = [
            'site.name' => 'general',
            'site.description' => 'general',
            'site.contact_email' => 'general',
            'site.contact_phone' => 'general',
            'site.contact_address' => 'general',
            'social.facebook' => 'social',
            'social.instagram' => 'social',
            'social.youtube' => 'social',
            'social.twitter' => 'social',
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");

        foreach ($keysToSave as $key => $group) {
            $val = trim((string) ($_POST[str_replace('.', '_', $key)] ?? ''));
            $stmt->execute([$key, $val, $group, $val]);
        }

        // 2. Handle Footer Link lists (combine labels & URLs)
        // Quick Links
        $quickLabels = $_POST['quick_links_label'] ?? [];
        $quickHrefs = $_POST['quick_links_href'] ?? [];
        $quickLinks = [];
        for ($i = 0; $i < count($quickLabels); $i++) {
            $lbl = trim((string)($quickLabels[$i] ?? ''));
            $url = trim((string)($quickHrefs[$i] ?? ''));
            if ($lbl !== '' && $url !== '') {
                $quickLinks[] = ['label' => $lbl, 'href' => $url];
            }
        }
        $quickLinksJson = json_encode($quickLinks);
        $stmt->execute(['footer.quick_links', $quickLinksJson, 'general', $quickLinksJson]);

        // Products Links
        $prodLabels = $_POST['products_links_label'] ?? [];
        $prodHrefs = $_POST['products_links_href'] ?? [];
        $prodLinks = [];
        for ($i = 0; $i < count($prodLabels); $i++) {
            $lbl = trim((string)($prodLabels[$i] ?? ''));
            $url = trim((string)($prodHrefs[$i] ?? ''));
            if ($lbl !== '' && $url !== '') {
                $prodLinks[] = ['label' => $lbl, 'href' => $url];
            }
        }
        $prodLinksJson = json_encode($prodLinks);
        $stmt->execute(['footer.products_links', $prodLinksJson, 'general', $prodLinksJson]);

        // 3. Handle Logo Upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $logoPath = $this->storeUploadedLogo($_FILES['site_logo']);
                if ($logoPath !== null) {
                    $stmt->execute(['site.logo', $logoPath, 'general', $logoPath]);
                }
            } catch (\RuntimeException $exception) {
                $this->redirectWithMessage($exception->getMessage(), 'error');
            }
        }

        $this->redirectWithMessage('Site settings updated successfully.');
    }

    private function storeUploadedLogo(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Logo upload failed.');
        }

        if ((int) ($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new \RuntimeException('The logo must be 3MB or smaller.');
        }

        $temporaryPath = (string) ($file['tmp_name'] ?? '');
        $imageInfo = @getimagesize($temporaryPath);
        $mime = is_array($imageInfo) ? (string) ($imageInfo['mime'] ?? '') : '';
        
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];

        if (!isset($extensions[$mime]) && !str_ends_with(strtolower((string)$file['name']), '.svg')) {
            throw new \RuntimeException('Upload a valid JPG, PNG, WebP or SVG logo.');
        }

        $ext = $extensions[$mime] ?? 'svg';

        $uploadDirectory = BASE_PATH . '/public/assets/uploads/settings';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0775, true);
        }

        $fileName = 'logo_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($temporaryPath, $uploadDirectory . '/' . $fileName)) {
            throw new \RuntimeException('The uploaded logo could not be saved.');
        }

        return '/assets/uploads/settings/' . $fileName;
    }

    private function redirectWithMessage(string $message, string $type = 'success'): never
    {
        $_SESSION['settings_flash'] = ['message' => $message, 'type' => $type];
        header('Location: /admin/settings', true, 303);
        exit;
    }
}
