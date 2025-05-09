<?php
date_default_timezone_set('Asia/Manila');
$currentYear = date('Y');
$currentTime = date('h:i A');
?>
<footer>
    <div class="footer-content">
        <div class="footer-time">
            <span class="material-symbols-rounded">schedule</span>
            <span>Philippine Time: <?php echo $currentTime; ?></span>
        </div>
        <div class="footer-copyright">
            <span>&copy; <?php echo $currentYear; ?> BEACompanion. All rights reserved.</span>
        </div>
    </div>
</footer>