<?php
ob_start();
date_default_timezone_set('Asia/Manila');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once(__DIR__ . '/../auth/dbconnect.php');
require_once(__DIR__ . '/../../config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
    echo json_encode(['success' => false, 'error' => 'PHPMailer is not available. Check autoload or installation.']);
    exit;
}

$errors = [];

// Function to respond with JSON output
function respond($success, $data = [])
{
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

// === Ensure No Session Conflict ===
function resetSessionIfNeeded($expectedUserId = null)
{
    // Check if there is an active session with a different user and reset it
    if (isset($_SESSION['user']) && ($expectedUserId === null || $_SESSION['user']['id'] != $expectedUserId)) {
        session_unset();
        session_destroy();
        session_start(); // Start a new session
    }
}

// === Handle Forgot Password ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset-password'])) {

    $email = filter_input(INPUT_POST, 'forgot-password-email', FILTER_SANITIZE_EMAIL);

    $_SESSION['reset_process'] = [
        'submitted_email' => $email,
        'is_email_valid' => null,
        'user_found' => null,
        'generated_token' => null,
        'token_expiry' => null,
        'reset_link' => null,
        'mail_error' => null
    ];

    if (empty($email)) {
        respond(false, [
            'errors' => ['forgot-password-email' => 'Email is required.'],
            'message' => 'Please fix the errors and try again.'
        ]);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_process']['is_email_valid'] = false;
        respond(false, [
            'errors' => ['forgot-password-email' => 'Invalid email format.'],
            'message' => 'Please fix the errors and try again.'
        ]);
    } else {
        $_SESSION['reset_process']['is_email_valid'] = true;
    }

    $stmt = $pdo->prepare('SELECT id, email, name, role, created_at, auth_token, auth_token_expiry FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    $_SESSION['reset_process']['user_found'] = (bool) $user;

    if (!$user) {
        respond(false, [
            'errors' => ['forgot-password-email' => 'No account found with that email.'],
            'message' => 'Please fix the errors and try again.'
        ]);
    }

    // Check for session conflict and reset if needed
    resetSessionIfNeeded($user['id']);

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'created_at' => $user['created_at'],
        'token' => $user['auth_token'],
        'expiry' => $user['auth_token_expiry'],
    ];

    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 3600);

    $_SESSION['reset_process']['generated_token'] = $token;
    $_SESSION['reset_process']['token_expiry'] = $expiry;

    $stmt = $pdo->prepare('UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email');
    $stmt->execute([
        'token' => $token,
        'expiry' => $expiry,
        'email' => $email
    ]);

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = (strpos($host, 'localhost') !== false) ? '/thesis_project' : '';
    $resetLink = "{$protocol}://{$host}{$basePath}/reset-password?token=" . urlencode($token);

    $_SESSION['reset_process']['reset_link'] = $resetLink;

    $mail = new PHPMailer(true);
    $smtpHost = $_ENV['SMTP_HOST'] ?? '';
    $smtpUser = $_ENV['SMTP_USER'] ?? '';
    $smtpPass = $_ENV['SMTP_PASS'] ?? '';
    $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? '';
    $smtpFromName = $_ENV['SMTP_FROM_NAME'] ?? '';

    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($smtpFromEmail, $smtpFromName);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset your password';
        $mail->Body = "<p>Click the link below to reset your password:</p><p><a href=\"" . htmlspecialchars($resetLink) . "\">Reset Password</a></p>";

        $mail->send();

        respond(true, ['message' => 'Password reset instructions have been sent to your email.']);

    } catch (Exception $e) {
        $_SESSION['reset_process']['mail_error'] = $mail->ErrorInfo;

        respond(false, [
            'errors' => ['mail' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"],
            'message' => 'An error occurred while sending the reset link.'
        ]);
    }
}


// === Handle Signup ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['create-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $createdAt = date('Y-m-d H:i:s');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (!preg_match('/^[a-zA-Z]{2,}$/', $name)) {
        $errors['name'] = 'Name must contain only letters and be at least 2 characters.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $errors['create-password'] = 'Password must include upper, lower, number, and symbol, and be at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm-password'] = 'Passwords do not match.';
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors['users_exist'] = 'Email is already registered.';
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
            'expiry' => $authTokenExpiry,
        ]);

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'email' => $email,
            'name' => $name,
            'role' => $role,
            'created_at' => $createdAt,
            'token' => $hashedAuthToken,
            'expiry' => $authTokenExpiry,
        ];

        $redirectTo = "patient/auth-token?token=" . urlencode($authToken);
        respond(true, ['redirectTo' => $redirectTo]);

    } catch (Exception $e) {
        respond(false, ['errors' => ['database' => 'An error occurred while processing your request.']]);
    }
}


// === Handle Login ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
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
        $_SESSION['user'] = [
            'id' => 0,
            'email' => $adminEmail,
            'name' => 'Administrator',
            'role' => 'admin',
            'created_at' => null
        ];

        $authToken = bin2hex(random_bytes(32));
        $authTokenExpiry = date('Y-m-d H:i:s', time() + 3600);
        $hashedAuthToken = password_hash($authToken, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare('UPDATE users SET auth_token = :token, auth_token_expiry = :expiry WHERE email = :email');
            $stmt->execute([
                'token' => $hashedAuthToken,
                'expiry' => $authTokenExpiry,
                'email' => $adminEmail
            ]);

            $redirectTo = "admin/auth-token?token=" . urlencode($authToken);
            respond(true, ['redirectTo' => $redirectTo]);

        } catch (Exception $e) {
            respond(false, ['errors' => ['database' => 'An error occurred while processing your request.']]);
        }

    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            respond(false, ['errors' => ['login' => 'Invalid email or password.']]);
        }

        // Check for session conflict and reset if needed
        resetSessionIfNeeded($user['id']);

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'created_at' => $user['created_at'],
            'token' => $user['auth_token'],
            'expiry' => $user['auth_token_expiry'],
        ];

        $authToken = bin2hex(random_bytes(32));
        $authTokenExpiry = date('Y-m-d H:i:s', time() + 3600);
        $hashedAuthToken = password_hash($authToken, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare('UPDATE users SET auth_token = :token, auth_token_expiry = :expiry WHERE email = :email');
            $stmt->execute([
                'token' => $hashedAuthToken,
                'expiry' => $authTokenExpiry,
                'email' => $email
            ]);

            $redirectTo = $_SESSION['user']['role'] . "/auth-token?token=" . urlencode($authToken);
            respond(true, ['redirectTo' => $redirectTo]);

        } catch (Exception $e) {
            respond(false, ['errors' => ['database' => 'An error occurred while processing your request.']]);
        }
    }
}

respond(false, ['errors' => ['request' => 'Invalid form submission.']]);
