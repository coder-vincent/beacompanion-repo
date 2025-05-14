<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="records-container">
    <h1>Records</h1>
</div>