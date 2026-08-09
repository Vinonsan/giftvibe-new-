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
        $this->ensureTablesExist($pdo);
        $this->ensureBankAccountsTable($pdo);

        // Active submenu tab
        $tab = (string) ($_GET['tab'] ?? 'general');
        if (!in_array($tab, ['general', 'contact', 'payment', 'footer'], true)) {
            $tab = 'general';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($pdo, $tab);
            return;
        }

        // Fetch corresponding settings data based on active tab
        $settingsData = [];
        if ($tab === 'general') {
            $settingsData = $pdo->query("SELECT * FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        } elseif ($tab === 'contact') {
            $settingsData = $pdo->query("SELECT * FROM contact_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        } elseif ($tab === 'payment') {
            $settingsData = ['bank_accounts' => $pdo->query("SELECT * FROM bank_accounts WHERE status='active' ORDER BY sort_order,id")->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($tab === 'footer') {
            $settingsData = $pdo->query("SELECT * FROM footer_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
        }
        
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $flash = $_SESSION['settings_flash'] ?? null;
        unset($_SESSION['settings_flash']);

        $this->view('layouts/admin-layout', [
            'title' => 'Settings',
            'pageTitle' => 'Settings',
            'showPageTitle' => false,
            'content' => $this->render('admin/settings/index', [
                'tab' => $tab,
                'settings' => $settingsData,
                'csrfToken' => $_SESSION['csrf_token'],
                'flash' => $flash,
            ]),
        ]);
    }

    private function handlePost(PDO $pdo, string $tab): void
    {
        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token)) {
            http_response_code(419);
            echo 'Invalid or expired request token.';
            return;
        }

        if ($tab === 'general') {
            // Logo Upload Handler
            $logoPath = $_POST['current_logo'] ?? '';
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $logoPath = $this->storeUploadedLogo($_FILES['site_logo']) ?? $logoPath;
                } catch (\RuntimeException $exception) {
                    $this->redirectWithMessage($exception->getMessage(), 'error', $tab);
                }
            }

            $siteName = trim((string) ($_POST['site_name'] ?? 'GiftVibe'));
            $siteDescription = trim((string) ($_POST['site_description'] ?? ''));
            $googleReviewUrl = trim((string) ($_POST['google_review_url'] ?? ''));

            $stmt = $pdo->prepare("UPDATE general_settings SET site_name = ?, site_description = ?, site_logo = ?, google_review_url = ? WHERE id = 1");
            $stmt->execute([$siteName, $siteDescription, $logoPath, $googleReviewUrl]);

        } elseif ($tab === 'contact') {
            $email = trim((string) ($_POST['site_contact_email'] ?? ''));
            $phone = trim((string) ($_POST['site_contact_phone'] ?? ''));
            $address = trim((string) ($_POST['site_contact_address'] ?? ''));
            $fb = trim((string) ($_POST['social_facebook'] ?? ''));
            $ig = trim((string) ($_POST['social_instagram'] ?? ''));
            $yt = trim((string) ($_POST['social_youtube'] ?? ''));
            $tw = trim((string) ($_POST['social_twitter'] ?? ''));

            $stmt = $pdo->prepare("UPDATE contact_settings SET contact_email = ?, contact_phone = ?, contact_address = ?, social_facebook = ?, social_instagram = ?, social_youtube = ?, social_twitter = ? WHERE id = 1");
            $stmt->execute([$email, $phone, $address, $fb, $ig, $yt, $tw]);

        } elseif ($tab === 'payment') {
            $names = (array) ($_POST['bank_name'] ?? []);
            $holders = (array) ($_POST['account_name'] ?? []);
            $numbers = (array) ($_POST['account_number'] ?? []);
            $branches = (array) ($_POST['branch'] ?? []);
            $accounts = [];
            foreach ($names as $index => $name) {
                $account = [
                    trim((string) $name),
                    trim((string) ($holders[$index] ?? '')),
                    trim((string) ($numbers[$index] ?? '')),
                    trim((string) ($branches[$index] ?? '')),
                ];
                if ($account[0] !== '' && $account[1] !== '' && $account[2] !== '') {
                    $accounts[] = $account;
                }
            }
            if ($accounts === []) {
                $this->redirectWithMessage('Add at least one complete bank account.', 'error', $tab);
            }
            try {
                $pdo->beginTransaction();
                $pdo->exec('DELETE FROM bank_accounts');
                $insert = $pdo->prepare("INSERT INTO bank_accounts(bank_name,account_name,account_number,branch,sort_order,status) VALUES(?,?,?,?,?,'active')");
                foreach ($accounts as $index => $account) {
                    $insert->execute([$account[0], $account[1], $account[2], $account[3], $index]);
                }
                $pdo->commit();
            } catch (\Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $this->redirectWithMessage('Bank accounts could not be saved. Please try again.', 'error', $tab);
            }

        } elseif ($tab === 'footer') {
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

            $stmt = $pdo->prepare("UPDATE footer_settings SET quick_links = ?, products_links = ? WHERE id = 1");
            $stmt->execute([$quickLinksJson, $prodLinksJson]);
        }

        $this->redirectWithMessage('Settings updated successfully.', 'success', $tab);
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

    private function ensureTablesExist(PDO $pdo): void
    {
        $pdo->exec("
             CREATE TABLE IF NOT EXISTS `general_settings` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `site_name` varchar(190) NOT NULL DEFAULT 'GiftVibe',
              `site_description` text DEFAULT NULL,
              `site_logo` varchar(255) DEFAULT NULL,
              `google_review_url` varchar(255) DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            /* Add google_review_url column if it does not exist in active databases */
            SELECT 1;
        ");
        try {
            $pdo->exec("ALTER TABLE general_settings ADD COLUMN google_review_url VARCHAR(255) DEFAULT NULL AFTER site_logo");
        } catch (\PDOException $e) {}

        $pdo->exec("

            CREATE TABLE IF NOT EXISTS `contact_settings` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `contact_email` varchar(190) DEFAULT NULL,
              `contact_phone` varchar(40) DEFAULT NULL,
              `contact_address` text DEFAULT NULL,
              `social_facebook` varchar(255) DEFAULT NULL,
              `social_instagram` varchar(255) DEFAULT NULL,
              `social_youtube` varchar(255) DEFAULT NULL,
              `social_twitter` varchar(255) DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `footer_settings` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `quick_links` text DEFAULT NULL,
              `products_links` text DEFAULT NULL,
              `created_at` timestamp NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $countGeneral = $pdo->query("SELECT COUNT(*) FROM general_settings")->fetchColumn();
        if ($countGeneral == 0) {
            $pdo->exec("INSERT INTO general_settings (id, site_name, site_description, site_logo) VALUES (1, 'GiftVibe', 'Thoughtful gifts for every person, moment and celebration—all in one place.', '/assets/images/logo.svg')");
        }

        $countContact = $pdo->query("SELECT COUNT(*) FROM contact_settings")->fetchColumn();
        if ($countContact == 0) {
            $pdo->exec("INSERT INTO contact_settings (id, contact_email, contact_phone, contact_address) VALUES (1, 'info@giftvibe.com', '+1 234 567 890', '123 Galle Road, Colombo, Sri Lanka')");
        }

        $countFooter = $pdo->query("SELECT COUNT(*) FROM footer_settings")->fetchColumn();
        if ($countFooter == 0) {
            $pdo->exec("INSERT INTO footer_settings (id, quick_links, products_links) VALUES (1, '[]', '[]')");
        }
    }

    private function ensureBankAccountsTable(PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS bank_accounts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,bank_name VARCHAR(120) NOT NULL,account_name VARCHAR(190) NOT NULL,account_number VARCHAR(100) NOT NULL,branch VARCHAR(120) NULL,sort_order INT UNSIGNED NOT NULL DEFAULT 0,status ENUM('active','inactive') NOT NULL DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        if ((int) $pdo->query('SELECT COUNT(*) FROM bank_accounts')->fetchColumn() === 0) {
            $legacy = $pdo->query("SELECT setting_key,setting_value FROM settings WHERE setting_group='payment'")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            if (!empty($legacy['bank_account_number']) && $legacy['bank_account_number'] !== 'Configure in Admin Settings') {
                $stmt=$pdo->prepare("INSERT INTO bank_accounts(bank_name,account_name,account_number,branch) VALUES(?,?,?,?)");
                $stmt->execute([$legacy['bank_name']??'Bank',$legacy['bank_account_name']??'GiftVibe',$legacy['bank_account_number'],$legacy['bank_branch']??'']);
            }
        }
    }

    private function redirectWithMessage(string $message, string $type = 'success', string $tab = 'general'): never
    {
        $_SESSION['settings_flash'] = ['message' => $message, 'type' => $type];
        header('Location: ' . app_url('/admin/settings?tab=' . $tab), true, 303);
        exit;
    }
}
