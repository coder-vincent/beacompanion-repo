<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once(__DIR__ . '/../../../../auth/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $providedToken = $_GET['token'];
    $stmt = $pdo->prepare('SELECT id, email, name, role, auth_token, auth_token_expiry FROM users WHERE auth_token IS NOT NULL');
    $stmt->execute();

    $user = null;
    while ($row = $stmt->fetch()) {
        if (password_verify($providedToken, $row['auth_token'])) {
            if (strtotime($row['auth_token_expiry']) >= time()) {
                $user = $row;
                break;
            }
        }
    }

    if (!$user) {
        echo '<script>window.location.href = "/thesis_project";</script>';
        exit();
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'plain_token' => $providedToken,
    ];
}

$fullName = $_SESSION['user']['name'] ?? 'Guest';
$token = $_SESSION['user']['plain_token'] ?? '';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
?>

<div id="doctor-page">
    <div class="dashboard-content">
        <?php
        include __DIR__ . '/../../../components/navbar/navbar.php';
        include __DIR__ . '/../../../components/sidebar/sidebar.php';
        ?>

        <main>
            <?php
            $currentPage = $_GET['page'] ?? 'dashboard';
            if ($currentPage === 'dashboard') {
                include __DIR__ . '/../../../components/main/doctor/dashboard.php';
            } else if ($currentPage === 'appointments') {
                include __DIR__ . '/../../../components/main/doctor/appointments.php';
            } else if ($currentPage === 'about') {
                include __DIR__ . '/../../../components/main/doctor/about.php';
            } else if ($currentPage === 'faq') {
                include __DIR__ . '/../../../components/main/doctor/faq.php';
            }
            ?>
        </main>

        <?php
        include __DIR__ . '/../../../components/footer/footer.php';
        ?>
    </div>
</div>