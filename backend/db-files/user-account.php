<?php
ob_start();
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once(__DIR__ . '/../auth/dbconnect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
    echo json_encode(['success' => false, 'error' => 'PHPMailer is not available.']);
    exit;
}

function respond($success, $data = [])
{
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

function resetSessionIfNeeded($expectedUserId = null)
{
    if (isset($_SESSION['user_reset_pass']) && ($expectedUserId === null || $_SESSION['user_reset_pass']['id'] != $expectedUserId)) {
        session_unset();
        session_destroy();
        session_start();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_user') {
    $userId = $_GET['id'] ?? null;

    if (!$userId) {
        respond(false, ['message' => 'User ID is required']);
    }

    $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = :id AND role != "admin"');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        respond(false, ['message' => 'User not found']);
    }

    respond(true, ['user' => $user]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (!preg_match('/^[a-zA-Z\s]{2,}$/', $name)) {
        $errors['name'] = 'Name must contain only letters and spaces, minimum 2 characters';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters and include upper/lowercase, number, and symbol';
    }

    if (empty($role)) {
        $errors['role'] = 'Role is required';
    } elseif (!in_array($role, ['patient', 'doctor'])) {
        $errors['role'] = 'Invalid role selected';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        respond(false, ['message' => 'Email already registered']);
    }

    try {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, :created_at)');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'created_at' => $createdAt
        ]);

        respond(true, ['message' => 'User added successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error adding user: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    $userId = $_POST['userId'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $errors = [];

    if (!$userId) {
        $errors['userId'] = 'User ID is required';
    }

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (!preg_match('/^[a-zA-Z\s]{2,}$/', $name)) {
        $errors['name'] = 'Name must contain only letters and spaces, minimum 2 characters';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (!empty($password) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters and include upper/lowercase, number, and symbol';
    }

    if (empty($role)) {
        $errors['role'] = 'Role is required';
    } elseif (!in_array($role, ['patient', 'doctor'])) {
        $errors['role'] = 'Invalid role selected';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id');
    $stmt->execute(['email' => $email, 'id' => $userId]);
    if ($stmt->fetch()) {
        respond(false, ['message' => 'Email already registered to another user']);
    }

    try {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, password = :password, role = :role WHERE id = :id AND role != "admin"');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role,
                'id' => $userId
            ]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id AND role != "admin"');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'id' => $userId
            ]);
        }

        if ($stmt->rowCount() === 0) {
            respond(false, ['message' => 'User not found or no changes made']);
        }

        respond(true, ['message' => 'User updated successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error updating user: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $userId = $_POST['id'] ?? null;

    if (!$userId) {
        respond(false, ['message' => 'User ID is required']);
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id AND role != "admin"');
        $stmt->execute(['id' => $userId]);

        if ($stmt->rowCount() === 0) {
            respond(false, ['message' => 'User not found or cannot be deleted']);
        }

        respond(true, ['message' => 'User deleted successfully']);
    } catch (Exception $e) {
        respond(false, ['message' => 'Error deleting user: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset-password'])) {
    $email = filter_input(INPUT_POST, 'forgot-password-email', FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        respond(false, ['errors' => ['forgot-password-email' => 'Email is required.'], 'message' => 'Please fix the errors and try again.']);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(false, ['errors' => ['forgot-password-email' => 'Invalid email format.'], 'message' => 'Please fix the errors and try again.']);
    }

    $stmt = $pdo->prepare('SELECT id, email, name, role FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        respond(false, ['errors' => ['forgot-password-email' => 'No account found with that email.'], 'message' => 'Please fix the errors and try again.']);
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = password_hash($token, PASSWORD_BCRYPT);
    $expiry = date('Y-m-d H:i:s', time() + 3600);

    $stmt = $pdo->prepare('UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email');
    $stmt->execute(['token' => $tokenHash, 'expiry' => $expiry, 'email' => $email]);

    session_regenerate_id(true);
    $_SESSION['user_reset_pass'] = ['id' => $user['id'], 'email' => $user['email'], 'name' => $user['name'], 'role' => $user['role'], 'token' => $token];

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = (strpos($host, 'localhost') !== false) ? '/thesis_project' : '';
    $resetLink = "{$protocol}://{$host}{$basePath}/reset-password?token=" . urlencode($token);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'] ?? '';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'] ?? '';
        $mail->Password = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? '', $_ENV['SMTP_FROM_NAME'] ?? '');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset your password';
        $mail->Body = "<p>Click the link below to reset your password:</p><p><a href=\"" . htmlspecialchars($resetLink) . "\">Reset Password</a></p>";

        $mail->send();

        respond(true, ['message' => 'Password reset instructions have been sent to your email.']);
    } catch (Exception $e) {
        respond(false, ['errors' => ['mail' => 'Email could not be sent.']]);
    }
}

if (isset($_POST['change-password'])) {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new-password'] ?? '';
    $confirmPassword = $_POST['confirm-new-password'] ?? '';

    $errors = [];

    if (empty($newPassword) || empty($confirmPassword)) {
        $errors['password'] = 'Both password fields are required.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $newPassword)) {
        $errors['password'] = 'Password must be at least 8 characters and include upper/lowercase, number, and symbol.';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    $userSession = $_SESSION['user_reset_pass'] ?? null;
    if (!$userSession || empty($token)) {
        respond(false, ['errors' => ['session' => 'Session or token missing.']]);
    }

    $stmt = $pdo->prepare('SELECT id, reset_token, reset_token_expiry FROM users WHERE id = :id');
    $stmt->execute(['id' => $userSession['id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($token, $user['reset_token']) || strtotime($user['reset_token_expiry']) < time()) {
        respond(false, ['errors' => ['token' => 'Invalid or expired token.']]);
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id');
    $stmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);

    unset($_SESSION['user_reset_pass']);
    session_regenerate_id(true);

    respond(true, ['message' => 'Password has been reset successfully. You can now log in.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['create-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $createdAt = date('Y-m-d H:i:s');

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (!preg_match('/^[a-zA-Z\s]{2,}$/', $name)) {
        $errors['name'] = 'Name must contain only letters and spaces, minimum 2 characters.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $errors['create-password'] = 'Password must include upper, lower, number, symbol, and be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm-password'] = 'Passwords do not match.';
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors['email_taken'] = 'Email is already registered.';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    try {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $role = 'patient';
        $authToken = bin2hex(random_bytes(32));
        $authTokenExpiry = date('Y-m-d H:i:s', time() + 3600);
        $hashedAuthToken = password_hash($authToken, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('INSERT INTO users (email, password, name, role, created_at, auth_token, auth_token_expiry) VALUES (:email, :password, :name, :role, :created_at, :token, :expiry)');
        $stmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'role' => $role,
            'created_at' => $createdAt,
            'token' => $hashedAuthToken,
            'expiry' => $authTokenExpiry
        ]);

        if (!isset($_SESSION['user'])) {
            resetSessionIfNeeded($user['id']);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'created_at' => $createdAt,
            'plain_token' => $authToken,
        ];

        respond(true, ['redirectTo' => "patient/auth-token?token=" . urlencode($authToken)]);
    } catch (Exception $e) {
        respond(false, ['errors' => ['database' => 'An error occurred while processing your request.']]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
    $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '';

    $errors = [];

    if ($email !== $adminEmail && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty.';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    if ($email === $adminEmail && $password === $adminPassword) {
        session_regenerate_id(true);
        $authToken = bin2hex(random_bytes(32));
        $authTokenExpiry = date('Y-m-d H:i:s', time() + 3600);
        $hashedAuthToken = password_hash($authToken, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('UPDATE users SET auth_token = :token, auth_token_expiry = :expiry, last_login = :last_login WHERE email = :email');
        $stmt->execute([
            'token' => $hashedAuthToken,
            'expiry' => $authTokenExpiry,
            'last_login' => date('Y-m-d H:i:s'),
            'email' => $adminEmail
        ]);

        $_SESSION['user'] = [
            'id' => 0,
            'email' => $adminEmail,
            'name' => 'Administrator',
            'role' => 'admin',
            'created_at' => null,
            'plain_token' => $authToken,
        ];

        respond(true, ['redirectTo' => "admin/auth-token?token=" . urlencode($authToken)]);
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        respond(false, ['errors' => ['login' => 'Invalid email or password.']]);
    }

    if (!isset($_SESSION['user'])) {
        resetSessionIfNeeded($user['id']);
    }
    session_regenerate_id(true);

    $authToken = bin2hex(random_bytes(32));
    $authTokenExpiry = date('Y-m-d H:i:s', time() + 3600);
    $hashedAuthToken = password_hash($authToken, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('UPDATE users SET auth_token = :token, auth_token_expiry = :expiry, last_login = :last_login WHERE email = :email');
    $stmt->execute([
        'token' => $hashedAuthToken,
        'expiry' => $authTokenExpiry,
        'last_login' => date('Y-m-d H:i:s'),
        'email' => $email
    ]);

    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'created_at' => $user['created_at'],
        'plain_token' => $authToken,
    ];

    respond(true, ['redirectTo' => $user['role'] . "/auth-token?token=" . urlencode($authToken)]);
}

respond(false, ['errors' => ['request' => 'Invalid form submission.']]);
?>