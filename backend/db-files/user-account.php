<?php
require_once(__DIR__ . '/../auth/dbconnect.php');

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

    // Validate inputs
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

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors['users_exist'] = 'Email is already registered.';
    }

    if (!empty($errors)) {
        respond(false, ['errors' => $errors]);
    }

    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, :created_at)');
    $stmt->execute([
        'email' => $email,
        'password' => $hashedPassword,
        'name' => $name,
        'created_at' => $createdAt
    ]);

    respond(true, ['redirectTo' => 'loginPage']);
}

if (isset($_POST['login'])) {
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        respond(true, ['redirectTo' => 'loginPage']);
    }

    // Authenticate user
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'created_at' => $user['created_at']
        ];
        respond(true, ['redirectTo' => 'patientPage']);
    }

    $errors['login'] = 'Invalid email or password.';
    $_SESSION['errors'] = $errors;
    respond(true, ['redirectTo' => 'loginPage']);
}

respond(false, ['errors' => ['request' => 'Invalid form submission.']]);
