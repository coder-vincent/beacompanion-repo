<?php
session_start();
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="about-container">
    <h2>About Our Service</h2>
    <div class="about-content">
        <p>Welcome to our healthcare platform. We are dedicated to providing the best medical services to our patients.
        </p>
        <div class="features">
            <div class="feature-card">
                <h3>Easy Appointment Booking</h3>
                <p>Schedule your appointments with our doctors easily through our platform.</p>
            </div>
            <div class="feature-card">
                <h3>24/7 Support</h3>
                <p>Our support team is available round the clock to assist you.</p>
            </div>
        </div>
    </div>
</div>