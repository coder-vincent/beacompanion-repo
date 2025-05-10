<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="about-container">
    <div class="about-image">
        <img src="/thesis_project/frontend/src/assets/images/logo-beacompanion.png" alt="BEACompanion Logo"
            class="logo-image">
    </div>
    <h2>About BEACompanion</h2>
    <div class="about-content" id="aboutContent">
        <!-- Content will be loaded dynamically -->
    </div>
</div>