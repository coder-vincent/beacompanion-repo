<?php
session_start();

// Debug output (for development only — remove or restrict in production)
echo '<pre>';
print_r($_SESSION['reset_process']);
echo '</pre>';

// Safely retrieve user full name
$fullName = $_SESSION['user']['name'] ?? 'Guest';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';

// Retrieve reset token if available
$authToken = $_SESSION['reset_process']['generated_token'] ?? 'Unavailable';
$tokenFromUrl = $_GET['token'] ?? '';

// Check if the token from the URL matches the token in the session
if (empty($tokenFromUrl) || $tokenFromUrl !== $authToken) {
    // Redirect to login page if token is invalid
    header("Location: /thesis_project/login.php");  // Update with the correct base path
    exit();

}

// If the token is valid, you can proceed with further logic for password reset
?>


<div id="reset-password">
    <h1>Welcome, <?php echo htmlspecialchars($firstName); ?>!</h1>
    <p>You are on the reset password page.</p>
    <p>Your reset token (for debugging): <code><?php echo htmlspecialchars($authToken); ?></code></p>
</div>