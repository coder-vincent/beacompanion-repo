<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="faq-container">
    <div class="faq-header">
        <h1>Frequently Asked Questions</h1>
        <p>Welcome to the BEA Help Center! If you have any questions or need assistance, you're in the right place.
            Below, you'll find answers to common inquiries and guides to help you use BEA efficiently.</p>
    </div>
    <div class="faq-content" id="faqContent">
        <!-- Content will be loaded dynamically -->
    </div>
</div>