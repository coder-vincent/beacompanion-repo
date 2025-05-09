<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}
?>

<div class="appointments-container">
    <h2>Appointment Management</h2>
    <div class="appointments-content">
        <div class="feature-card">
            <h3>Schedule</h3>
            <p>View and manage your appointment schedule.</p>
        </div>
        <div class="feature-card">
            <h3>Patient Appointments</h3>
            <p>Handle patient appointment requests and confirmations.</p>
        </div>
    </div>
</div>