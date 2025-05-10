<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="patients-container">
    <h2>Patients Management</h2>
</div>