<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p>Manage all system users</p>
        </div>
        <div class="stat-card">
            <h3>System Overview</h3>
            <p>Monitor system performance</p>
        </div>
    </div>
</div>