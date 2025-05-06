<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once(__DIR__ . '/../../../auth/dbconnect.php');

// Debugging output: Check if user reset session is set
echo '<pre>';
print_r($_SESSION['user_reset_pass'] ?? 'No user session');
echo '</pre>';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $providedToken = filter_var($_GET['token'], FILTER_SANITIZE_STRING); // Sanitize token input

    // Prepare the query to fetch user data and check the reset token
    $stmt = $pdo->prepare('SELECT id, email, name, role, reset_token, reset_token_expiry FROM users WHERE reset_token IS NOT NULL');
    $stmt->execute();

    $user = null;
    while ($row = $stmt->fetch()) {
        // Check if the provided token matches the stored token
        if (password_verify($providedToken, $row['reset_token'])) {
            // Check if the reset token has expired
            if (strtotime($row['reset_token_expiry']) >= time()) {
                $user = $row;
                break;
            }
        }
    }

    if (!$user) {
        // Token is invalid or expired, redirect with an error message
        $_SESSION['error_message'] = 'The reset token is invalid or has expired. Please request a new one.';
        header('Location: /thesis_project'); // Redirect to a page with the error message
        exit();
    }

    // Token is valid, store user details in session
    $_SESSION['user_reset_pass'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'token' => $providedToken,
    ];
}

// Retrieve session values for display
$fullName = $_SESSION['user_reset_pass']['name'] ?? 'Guest';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
$userRole = $_SESSION['user_reset_pass']['role'] ?? 'guest';
$email = $_SESSION['user_reset_pass']['email'] ?? '';

// Display greeting message
echo "Welcome, " . htmlspecialchars($firstName) . "!";
echo "<br>Role: " . htmlspecialchars($userRole);
?>

<div id="reset-password">
    <!-- Left Container -->
    <?php include '../../components/hero/hero-section.php'; ?>

    <!-- Right Container -->
    <div class="rightDiv">
        <div class="company-name">
            <h2>BEACompanion</h2>
        </div>

        <div class="user-reset-password">
            <h1>Reset account password</h1>
            <p class="paragraph-fade">Enter a new password for <?php echo htmlspecialchars($email); ?></p>
        </div>

        <?php
        // Check if there are errors or a success message
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-main">
                <p>' . htmlspecialchars($_SESSION['error_message']) . '</p>
              </div>';
            unset($_SESSION['error_message']); // Clear the error message after displaying
        }

        if (isset($successMessage)) {
            echo '<div class="success-main">
                <p>' . htmlspecialchars($successMessage) . '</p>
              </div>';
        }
        ?>

        <div class="user-reset-password-form">
            <?php
            // Include the form for resetting the password
            include($_SERVER['DOCUMENT_ROOT'] . '/thesis_project/frontend/src/components/form/reset-password-form/reset-password-form.php');
            ?>
        </div>
    </div>
</div>