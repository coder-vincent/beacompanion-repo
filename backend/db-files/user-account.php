<?php
require_once(__DIR__ . '/../auth/dbconnect.php');
require_once(__DIR__ . '/../../config.php');
header('Content-Type: application/json');
session_start();

$errors = [];

function respond($success, $data = [])
{
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, ['errors' => ['request' => 'Invalid request method.']]);
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

if (isset($_POST['signup'])) {
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

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = 'patient';

    $stmt = $pdo->prepare('INSERT INTO users (email, password, name, role, created_at) VALUES (:email, :password, :name, :role, :created_at)');
    $stmt->execute([
        'email' => $email,
        'password' => $hashedPassword,
        'name' => $name,
        'role' => $role,
        'created_at' => $createdAt
    ]);

    respond(true, ['redirectTo' => 'loginPage']);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
    $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '';

    if ($email !== $adminEmail && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty.';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? '';
    $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '';

    if ($email === $adminEmail && $password === $adminPassword) {
        $_SESSION['user'] = [
            'id' => 0,
            'email' => $adminEmail,
            'name' => 'Administrator',
            'role' => 'admin',
            'created_at' => null
        ];
        respond(true, ['redirectTo' => 'adminDashboard']);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            respond(false, ['errors' => ['login' => 'Invalid email or password.']]);
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'created_at' => $user['created_at']
        ];

        $redirectTo = match ($user['role']) {
            'admin' => 'adminDashboard',
            'doctor' => 'doctorDashboard',
            default => 'patientDashboard'
        };

        respond(true, ['redirectTo' => $redirectTo]);
    }
}

respond(false, ['errors' => ['request' => 'Invalid form submission.']]);
