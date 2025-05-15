<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once(__DIR__ . '/../../../auth/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $providedToken = filter_var($_GET['token'], FILTER_SANITIZE_STRING);

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
        $_SESSION['error_message'] = 'The reset token is invalid or has expired. Please request a new one.';
        $basePath = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ? '/thesis_project' : '';
        header('Location: ' . $basePath . '/');
        exit();
    }

    $_SESSION['user_reset_pass'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'token' => $providedToken,
    ];
}

$fullName = $_SESSION['user_reset_pass']['name'] ?? 'Guest';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
$userRole = $_SESSION['user_reset_pass']['role'] ?? 'guest';
$email = $_SESSION['user_reset_pass']['email'] ?? '';
?>

<div id="reset-password">
    <!-- Left Container -->
    <?php include '../../components/hero/hero-section.php'; ?>

    <!-- Right Container -->
    <div class="rightDiv">
        <div class="rightDiv-contents">
            <div class="rightDiv-container">
                <div class="company-name">
                    <h2>BEACompanion</h2>
                </div>

                <div class="user-reset-password">
                    <h1>Reset account password</h1>
                    <p class="paragraph-fade">Enter a new password for <?php echo htmlspecialchars($email); ?></p>
                </div>

                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-main">
                <p>' . htmlspecialchars($_SESSION['error_message']) . '</p>
              </div>';
                    unset($_SESSION['error_message']);
                }

                if (isset($successMessage)) {
                    echo '<div class="success-main">
                <p>' . htmlspecialchars($successMessage) . '</p>
              </div>';
                }
                ?>

                <div class="user-reset-password-form">
                    <?php
                    include('../../components/form/reset-password-form/reset-password-form.php');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>