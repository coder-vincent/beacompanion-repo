<?php
require_once(__DIR__ . '/../auth/dbconnect.php');

header('Content-Type: application/json');
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['create-password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    $created_at = date('Y-m-d H:i:s');

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (!preg_match('/^[a-zA-Z]{2,}$/', $name)) {
        $errors['name'] = 'Name must contain only letters and be at least 2 characters.';
    }


    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $errors['password'] = 'Password must include upper, lower, number, and symbol, and be at least 8 characters.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm-password'] = 'Passwords do not match.';
    }

    // Check if user already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);

    if ($stmt->fetch()) {
        $errors['users_exist'] = 'Email is already registered.';
    }

    // Return errors if any
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Create user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, :created_at)');
    $stmt->execute([
        'email' => $email,
        'password' => $hashedPassword,
        'name' => $name,
        'created_at' => $created_at
    ]);

    echo json_encode(['success' => true, 'redirectTo' => 'loginPage']);
    exit;
}

// Fallback in case request is not valid
echo json_encode(['success' => false, 'errors' => ['request' => 'Invalid request method or missing POST data.']]);
exit;
