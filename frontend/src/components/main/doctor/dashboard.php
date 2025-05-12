<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="dashboard-container">
    <h2>Welcome, Dr. <?php echo htmlspecialchars($user['name']); ?>!</h2>

</div>