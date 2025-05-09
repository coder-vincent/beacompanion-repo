<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="faq-container">
    <h2>Frequently Asked Questions</h2>


</div>