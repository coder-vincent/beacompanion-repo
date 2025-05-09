<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="dashboard-container">
    <h2>Welcome, Dr. <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Today's Appointments</h3>
            <p>View and manage your appointments</p>
        </div>
        <div class="stat-card">
            <h3>Patient Records</h3>
            <p>Access patient medical records</p>
        </div>
    </div>
</div>