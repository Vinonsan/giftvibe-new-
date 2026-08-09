<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SmsService;
use PDO;

class AuthController extends Controller
{
    public function form(): void
    {
        if (isset($_SESSION['user']['id'])) {
            header('Location: ' . app_url($this->safeRedirect((string) ($_GET['redirect'] ?? '/shop'))), true, 303);
            exit;
        }
        $notice = (string) ($_SESSION['auth_notice'] ?? '');
        unset($_SESSION['auth_notice']);
        $redirect = $this->safeRedirect((string) ($_GET['redirect'] ?? '/shop'));
        $content = $this->render('public/auth/login', compact('notice', 'redirect'));
        $this->view('layouts/public-layout', ['title' => 'Customer Sign In', 'robots' => 'noindex, nofollow', 'content' => $content]);
    }

    public function login(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            return;
        }

        $db = Database::connection();
        $stmt = $db->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
            return;
        }

        if ($user['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Your account is inactive or blocked.']);
            return;
        }

        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'role' => $user['role_name'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'phone_2' => $user['phone_2'] ?? '',
            'address_line_1' => $user['address_line_1'] ?? ($user['address'] ?? ''),
            'city' => $user['city'] ?? '',
            'district' => $user['district'] ?? '',
            'avatar' => $user['avatar'] ?? 'avatar_1',
        ];
        $this->persistSessionCookie();

        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
    }

    public function register(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $phone2 = trim((string) ($input['phone_2'] ?? ''));
        $addressLine1 = trim((string) ($input['address_line_1'] ?? ''));
        $city = trim((string) ($input['city'] ?? ''));
        $district = trim((string) ($input['district'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $avatar = $this->validAvatarSeed((string) ($input['avatar'] ?? 'giftvibe-1'));

        if ($name === '' || $email === '' || $phone === '' || $addressLine1 === '' || $city === '' || $district === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Name, email, primary phone number, address, city, district, and password are required.']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Enter a valid email address.']);
            return;
        }
        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must contain at least 8 characters.']);
            return;
        }

        $db = Database::connection();
        $columns = $db->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('address', $columns, true)) {
            $db->exec('ALTER TABLE users ADD address TEXT NULL AFTER phone');
        }
        foreach (['phone_2' => 'VARCHAR(40) NULL AFTER phone', 'address_line_1' => 'VARCHAR(255) NULL AFTER address', 'city' => 'VARCHAR(120) NULL AFTER address_line_1', 'district' => 'VARCHAR(120) NULL AFTER city'] as $column => $definition) {
            if (!in_array($column, $columns, true)) $db->exec("ALTER TABLE users ADD {$column} {$definition}");
        }
        if (!in_array('avatar', $columns, true)) $db->exec("ALTER TABLE users ADD avatar VARCHAR(30) NOT NULL DEFAULT 'giftvibe-1' AFTER district");
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1');
        $stmt->execute([$email, $phone]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'This email or phone number is already registered.']);
            return;
        }

        $nameParts = preg_split('/\s+/', $name, 2) ?: [$name];
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // Resolve the customer role by its stable slug; never assume a database ID.
        $roleStmt = $db->prepare('SELECT id FROM roles WHERE slug = ? LIMIT 1');
        $roleStmt->execute(['customer']);
        $roleId = $roleStmt->fetchColumn();
        if (!$roleId) {
            $createRole = $db->prepare('INSERT INTO roles (name, slug) VALUES (?, ?)');
            $createRole->execute(['Customer', 'customer']);
            $roleId = $db->lastInsertId();
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $insert = $db->prepare('INSERT INTO users (role_id, first_name, last_name, email, phone, phone_2, address, address_line_1, city, district, avatar, password_hash, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([$roleId, $firstName, $lastName, $email, $phone, $phone2, $addressLine1, $addressLine1, $city, $district, $avatar, $passwordHash, 'active']);
        $newUserId = $db->lastInsertId();

        $_SESSION['user'] = [
            'id' => $newUserId,
            'role' => 'customer',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'phone_2' => $phone2,
            'address_line_1' => $addressLine1,
            'city' => $city,
            'district' => $district,
            'avatar' => $avatar,
        ];
        $this->persistSessionCookie();

        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        if (isset($_GET['redirect'])) {
            header('Location: ' . app_url($this->safeRedirect((string) $_GET['redirect'])));
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function status(): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'logged_in' => isset($_SESSION['user']),
            'user' => $_SESSION['user'] ?? null
        ]);
    }

    public function updateAvatar(): void
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user']['id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Sign in required.']); return; }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $avatar = (string) ($input['avatar'] ?? '');
        $avatar = $this->validAvatarSeed($avatar);
        $db = Database::connection();
        $columns = $db->query('DESCRIBE users')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('avatar', $columns, true)) $db->exec("ALTER TABLE users ADD avatar VARCHAR(30) NOT NULL DEFAULT 'giftvibe-1' AFTER district");
        $db->prepare('UPDATE users SET avatar=? WHERE id=?')->execute([$avatar,(int)$_SESSION['user']['id']]);
        $_SESSION['user']['avatar']=$avatar;
        echo json_encode(['success'=>true,'avatar'=>$avatar]);
    }

    public function requestPasswordReset(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $identifier = trim((string) ($input['identifier'] ?? ''));
        $channel = ($input['channel'] ?? 'email') === 'sms' ? 'sms' : 'email';
        if ($identifier === '') { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Enter your email or phone number.']); return; }
        if ((int) ($_SESSION['password_reset_last_request'] ?? 0) > time() - 60) { http_response_code(429); echo json_encode(['success'=>false,'message'=>'Please wait one minute before requesting another code.']); return; }

        $db = Database::connection();
        $statement = $db->prepare('SELECT id,email,phone FROM users WHERE email=? OR phone=? LIMIT 1');
        $statement->execute([$identifier, $identifier]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        $generic = 'If the account exists, a verification code has been sent.';
        if (!$user) { echo json_encode(['success'=>true,'message'=>$generic]); return; }

        $otp = (string) random_int(100000, 999999);
        $_SESSION['password_reset'] = ['user_id'=>(int)$user['id'],'hash'=>password_hash($otp, PASSWORD_DEFAULT),'expires'=>time()+600,'attempts'=>0,'verified'=>false];
        $_SESSION['password_reset_last_request'] = time();
        $message = "Your GiftVibe password reset code is {$otp}. It expires in 10 minutes.";
        $sent = $channel === 'sms'
            ? SmsService::send((string) $user['phone'], $message)
            : @mail((string) $user['email'], 'GiftVibe password reset code', $message, "Content-Type: text/plain; charset=UTF-8\r\n");
        $isLocal = in_array((string) ($_SERVER['SERVER_NAME'] ?? ''), ['localhost', '127.0.0.1'], true);
        echo json_encode([
            'success'=>true,
            'message'=>$sent ? $generic : ($isLocal ? "Local verification code: {$otp}" : 'Code created, but delivery is not configured. Please contact support.'),
        ]);
    }

    public function verifyPasswordReset(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $otp = preg_replace('/\D+/', '', (string) ($input['otp'] ?? ''));
        $reset = $_SESSION['password_reset'] ?? null;
        if (!$reset || (int)$reset['expires'] < time() || (int)$reset['attempts'] >= 5) { unset($_SESSION['password_reset']); http_response_code(422); echo json_encode(['success'=>false,'message'=>'The verification code has expired. Request a new code.']); return; }
        $_SESSION['password_reset']['attempts']++;
        if (!password_verify($otp, (string)$reset['hash'])) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Incorrect verification code.']); return; }
        $_SESSION['password_reset']['verified'] = true;
        echo json_encode(['success'=>true]);
    }

    public function resetPassword(): void
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $password = (string) ($input['password'] ?? '');
        $confirmation = (string) ($input['password_confirmation'] ?? '');
        $reset = $_SESSION['password_reset'] ?? null;
        if (!$reset || empty($reset['verified']) || (int)$reset['expires'] < time()) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Verify a new code before resetting your password.']); return; }
        if (strlen($password) < 8 || $password !== $confirmation) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Passwords must match and contain at least 8 characters.']); return; }
        Database::connection()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($password, PASSWORD_BCRYPT),(int)$reset['user_id']]);
        unset($_SESSION['password_reset'], $_SESSION['password_reset_last_request']);
        echo json_encode(['success'=>true,'message'=>'Password reset successfully. You can now sign in.']);
    }

    private function validAvatarSeed(string $seed): string
    {
        return preg_match('/^[a-zA-Z0-9_-]{1,30}$/', $seed) === 1 ? $seed : 'giftvibe-1';
    }

    private function persistSessionCookie(): void
    {
        setcookie(session_name(), session_id(), [
            'expires' => time() + (60 * 60 * 24 * 365),
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function safeRedirect(string $redirect): string
    {
        return str_starts_with($redirect, '/') && !str_starts_with($redirect, '//') ? $redirect : '/shop';
    }
}
