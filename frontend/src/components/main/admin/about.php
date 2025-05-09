<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="about-container">
    <h2>About the System</h2>

</div>