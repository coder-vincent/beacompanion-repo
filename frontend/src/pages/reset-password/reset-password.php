<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once(__DIR__ . '/../../../auth/dbconnect.php');

echo '<pre>';
print_r($_SESSION['user_reset_pass'] ?? 'No user session');
echo '</pre>';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $providedToken = $_GET['token'];

    $stmt = $pdo->prepare('SELECT id, email, name, role, reset_token, reset_token_expiry FROM users WHERE reset_token IS NOT NULL');
    $stmt->execute();

    $user = null;
    while ($row = $stmt->fetch()) {
        if (password_verify($providedToken, $row['reset_token'])) {
            if (strtotime($row['reset_token_expiry']) >= time()) {
                $user = $row;
                break;
            }
        }
    }

    if (!$user) {
        echo '<script>window.location.href = "/thesis_project";</script>';
        exit();
    }

    $_SESSION['user_reset_pass'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'plain_token' => $providedToken,
    ];
}

$fullName = $_SESSION['user_reset_pass']['name'] ?? 'Guest';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
$userRole = $_SESSION['user_reset_pass']['role'] ?? 'guest';

echo "Welcome, " . htmlspecialchars($firstName) . "!";
echo "<br>Role: " . htmlspecialchars($userRole);

?>

<div id="reset-password">
    <h1>Welcome, <?php echo htmlspecialchars($firstName); ?>!</h1>
    <p>You are on the reset password page.</p>
</div>