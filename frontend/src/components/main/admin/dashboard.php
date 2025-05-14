<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}

$stmt = $pdo->prepare('SELECT COUNT(*) as total_users FROM users WHERE role != "admin"');
$stmt->execute();
$totalUsers = $stmt->fetch()['total_users'];

$stmt = $pdo->prepare('SELECT role, COUNT(*) as count FROM users WHERE role != "admin" GROUP BY role');
$stmt->execute();
$usersByRole = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT 
    (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_users_30d,
    (SELECT COUNT(*) FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 24 HOUR)) as active_users_24h');
$stmt->execute();
$systemMetrics = $stmt->fetch();

?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-breakdown">
                <?php foreach ($usersByRole as $role): ?>
                    <div class="role-stat">
                        <span class="role-name"><?php echo ucfirst($role['role']); ?>s:</span>
                        <span class="role-count"><?php echo $role['count']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="stat-card">
            <h3>System Overview</h3>
            <div class="stat-metrics">
                <div class="metric">
                    <span class="metric-label">New Users (30d)</span>
                    <span class="metric-value"><?php echo $systemMetrics['new_users_30d']; ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Active Users (24h)</span>
                    <span class="metric-value"><?php echo $systemMetrics['active_users_24h']; ?></span>
                </div>
            </div>
        </div>
    </div>