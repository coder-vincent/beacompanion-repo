<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}

?>

<div class="dashboard-container">
    <h1 class="dashboard-greeting">Hello, <span
            class="dashboard-username"><?php echo htmlspecialchars($user['name']); ?></span>!</h1>
    <div class="dashboard-main-card">
        <div class="observation-section">
            <div class="observation-image">
                <video id="camera-feed" autoplay playsinline muted></video>
                <div class="camera-overlay">
                    <div class="camera-status">Camera is off</div>
                </div>
                <div class="camera-controls">
                    <button id="start-camera" class="camera-btn">
                        <span class="material-icons">videocam</span>
                        Start Camera
                    </button>
                    <button id="stop-camera" class="camera-btn" style="display: none;">
                        <span class="material-icons">videocam_off</span>
                        Stop Camera
                    </button>
                </div>
            </div>
            <div class="observation-analysis">
                <div class="observation-title-bar">
                    Latest Observation and Analysis
                </div>
                <div class="observation-cards">
                    <div class="observation-card">
                        <h4>Behavioral Patterns</h4>
                        <div class="observation-list">
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">touch_app</span>
                                    Fidgeting
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">touch_app</span>
                                    Leaving Seat Inappropriately
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">touch_app</span>
                                    Difficulty Waiting for Turns
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">touch_app</span>
                                    Eye Gaze Shifting
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="observation-card">
                        <h4>Speech Patterns</h4>
                        <div class="observation-list">
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">record_voice_over</span>
                                    Excessive Interruptions During Conversations
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                            <div class="observation-item">
                                <span class="observation-label">
                                    <span class="material-icons observation-icon">record_voice_over</span>
                                    Rapid or Excessive Talking
                                </span>
                                <span class="observation-score">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="observation-remarks">
                    <b>Remarks:</b>
                    <div class="remarks-box">No remarks available.</div>
                </div>
            </div>
        </div>
    </div>
</div>