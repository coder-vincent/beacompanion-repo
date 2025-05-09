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

function getSystemPerformance()
{
    $performance = [];

    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $performance['cpu'] = round($load[0] * 100 / 4, 2);
    } else {
        $performance['cpu'] = 0;
    }

    if (function_exists('memory_get_usage')) {
        $totalMemory = memory_get_usage(true);
        $freeMemory = memory_get_peak_usage(true);
        $performance['memory'] = round(($totalMemory - $freeMemory) / $totalMemory * 100, 2);
    } else {
        $performance['memory'] = 0;
    }

    $totalSpace = disk_total_space('/');
    $freeSpace = disk_free_space('/');
    $performance['disk'] = round(($totalSpace - $freeSpace) / $totalSpace * 100, 2);

    return $performance;
}

$systemPerformance = getSystemPerformance();

function getSystemHealthStatus($performance)
{
    $status = [
        'level' => 'good',
        'message' => 'System is performing well',
        'icon' => 'check_circle',
        'color' => '#4CAF50'
    ];

    $warningCount = 0;
    $criticalCount = 0;

    if ($performance['cpu'] > 90) {
        $criticalCount++;
    } elseif ($performance['cpu'] > 70) {
        $warningCount++;
    }

    if ($performance['memory'] > 90) {
        $criticalCount++;
    } elseif ($performance['memory'] > 70) {
        $warningCount++;
    }

    if ($performance['disk'] > 90) {
        $criticalCount++;
    } elseif ($performance['disk'] > 70) {
        $warningCount++;
    }

    if ($criticalCount > 0) {
        $status = [
            'level' => 'critical',
            'message' => 'System requires immediate attention',
            'icon' => 'error',
            'color' => '#f44336'
        ];
    } elseif ($warningCount > 0) {
        $status = [
            'level' => 'warning',
            'message' => 'System needs monitoring',
            'icon' => 'warning',
            'color' => '#ff9800'
        ];
    }

    return $status;
}

$systemHealth = getSystemHealthStatus($systemPerformance);
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
        <div class="stat-card">
            <h3>System Performance</h3>
            <div class="performance-metrics">
                <div class="performance-item">
                    <div class="performance-header">
                        <span class="performance-label">CPU Usage</span>
                        <span class="performance-value"><?php echo $systemPerformance['cpu']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $systemPerformance['cpu']; ?>%"></div>
                    </div>
                </div>
                <div class="performance-item">
                    <div class="performance-header">
                        <span class="performance-label">Memory Usage</span>
                        <span class="performance-value"><?php echo $systemPerformance['memory']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $systemPerformance['memory']; ?>%"></div>
                    </div>
                </div>
                <div class="performance-item">
                    <div class="performance-header">
                        <span class="performance-label">Disk Usage</span>
                        <span class="performance-value"><?php echo $systemPerformance['disk']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $systemPerformance['disk']; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="system-health-status" style="--status-color: <?php echo $systemHealth['color']; ?>">
        <span class="material-symbols-rounded status-icon"><?php echo $systemHealth['icon']; ?></span>
        <div class="status-content">
            <h4>System Health Status</h4>
            <p><?php echo $systemHealth['message']; ?></p>
        </div>
    </div>
</div>