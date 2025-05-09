<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="users-container">
    <h2>User Management</h2>
    <div class="users-content">
        <div class="feature-card">
            <h3>Manage Users</h3>
            <p>Add, edit, or remove users from the system.</p>
        </div>
        <div class="feature-card">
            <h3>User Roles</h3>
            <p>Assign and manage user roles and permissions.</p>
        </div>
    </div>
</div>