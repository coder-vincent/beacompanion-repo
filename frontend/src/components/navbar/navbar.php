<?php
$user = $_SESSION['user'] ?? [];
$role = $user['role'] ?? '';
$name = $user['name'] ?? 'User';
?>

<nav class="navbar">
    <div class="navbar-content">
        <button class="toggle-sidebar" aria-label="Toggle Sidebar">
            <span class="material-symbols-rounded">menu</span>
        </button>
        <div class="page-title">
            <?php
            $currentPage = $_GET['page'] ?? 'dashboard';
            echo ucfirst($currentPage);
            ?>
        </div>
    </div>
</nav>