<?php
// require_once __DIR__ . '/../../../../backend/config/config.php';
// require_once __DIR__ . '/../../../../backend/auth/dbconnect.php';

// Get patient ID from session
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}

$patientId = $user['id'];
?>

<div class="records-container">
    <div class="section-container">
        <div class="section-header">
            <h2>My Records</h2>
            <div class="records-controls">
                <div class="search-container">
                    <input type="text" id="recordSearch" placeholder="Search records..." class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="date-filter-container">
                    <select id="dateFilter" class="filter-select">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">Last 7 Days</option>
                        <option value="month">Last 30 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="records-content" data-patient-id="<?php echo htmlspecialchars($patientId); ?>">
            <div class="table-container">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Behavioral Patterns</th>
                            <th>Speech Patterns</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody">
                        <tr>
                            <td colspan="5" class="loading">Loading records...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>