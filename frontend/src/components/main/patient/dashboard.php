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
                    <?php if ($latestObservation): ?>
                        <span
                            class="observation-date"><?php echo date('F j, Y', strtotime($latestObservation['created_at'])); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($latestObservation): ?>
                    <div class="observation-cards">
                        <div class="observation-card">
                            <h4>Behavioral Patterns</h4>
                            <div class="observation-list">
                                <?php
                                $behavioralPatterns = json_decode($latestObservation['behavioral_patterns'], true);
                                foreach ($behavioralPatterns as $pattern => $score):
                                    ?>
                                    <div class="observation-item">
                                        <span class="observation-label">
                                            <span class="material-icons observation-icon">touch_app</span>
                                            <?php echo htmlspecialchars($pattern); ?>
                                        </span>
                                        <span class="observation-score"><?php echo $score; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="observation-card">
                            <h4>Speech Patterns</h4>
                            <div class="observation-list">
                                <?php
                                $speechPatterns = json_decode($latestObservation['speech_patterns'], true);
                                foreach ($speechPatterns as $pattern => $score):
                                    ?>
                                    <div class="observation-item">
                                        <span class="observation-label">
                                            <span class="material-icons observation-icon">record_voice_over</span>
                                            <?php echo htmlspecialchars($pattern); ?>
                                        </span>
                                        <span class="observation-score"><?php echo $score; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="observation-remarks">
                        <b>Remarks:</b>
                        <div class="remarks-box"><?php echo htmlspecialchars($latestObservation['remarks']); ?></div>
                    </div>
                    <div class="observation-doctor">
                        <b>Observed by:</b> Dr. <?php echo htmlspecialchars($latestObservation['doctor_name']); ?>
                    </div>
                <?php else: ?>
                    <div class="no-observations">
                        <p>No observations available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>