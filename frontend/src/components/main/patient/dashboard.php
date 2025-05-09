<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Upcoming Appointments</h3>
            <p>You have no upcoming appointments</p>
        </div>
        <div class="stat-card">
            <h3>Recent Activity</h3>
            <p>No recent activity</p>
        </div>
    </div>
</div>