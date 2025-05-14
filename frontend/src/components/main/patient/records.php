<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'patient') {
    header('Location: /thesis_project');
    exit();
}

// Fetch all observations
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "/thesis_project/backend/db-files/observations.php?action=get_all&patient_id=" . $user['id']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$observations = [];
if ($response) {
    $data = json_decode($response, true);
    if ($data['success']) {
        $observations = $data['observations'];
    }
}
?>

<div class="records-container">
    <h1>Observation Records</h1>

    <div class="records-table-container">
        <div class="table-header">
            <div class="search-box">
                <span class="material-icons">search</span>
                <input type="text" id="recordSearch" placeholder="Search records...">
            </div>
            <div class="table-filters">
                <select id="dateFilter">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
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
                <tbody>
                    <?php if (!empty($observations)): ?>
                        <?php foreach ($observations as $observation): ?>
                            <tr>
                                <td><?php echo date('F j, Y', strtotime($observation['created_at'])); ?></td>
                                <td>Dr. <?php echo htmlspecialchars($observation['doctor_name']); ?></td>
                                <td>
                                    <?php
                                    $behavioralPatterns = json_decode($observation['behavioral_patterns'], true);
                                    foreach ($behavioralPatterns as $pattern => $score):
                                        ?>
                                        <div class="pattern-item">
                                            <span class="pattern-name"><?php echo htmlspecialchars($pattern); ?></span>
                                            <span class="pattern-score"><?php echo $score; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <?php
                                    $speechPatterns = json_decode($observation['speech_patterns'], true);
                                    foreach ($speechPatterns as $pattern => $score):
                                        ?>
                                        <div class="pattern-item">
                                            <span class="pattern-name"><?php echo htmlspecialchars($pattern); ?></span>
                                            <span class="pattern-score"><?php echo $score; ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo htmlspecialchars($observation['remarks']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-records">No observation records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .records-container {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .records-table-container {
        background: var(--background-secondary);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px var(--shadow-color);
        margin-top: 1.5rem;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--background-color);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        width: 300px;
    }

    .search-box input {
        border: none;
        background: none;
        outline: none;
        width: 100%;
        font-size: 0.95rem;
    }

    .table-filters select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--background-color);
        color: var(--text-color);
        min-width: 150px;
        cursor: pointer;
    }

    .records-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .records-table th,
    .records-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .records-table th {
        font-weight: 500;
        color: var(--text-color-light);
        background: var(--background-secondary);
    }

    .pattern-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .pattern-name {
        color: var(--text-color);
    }

    .pattern-score {
        font-weight: 600;
        color: var(--primary-color);
        margin-left: 1rem;
    }

    .no-records {
        text-align: center;
        color: var(--text-color-light);
        padding: 2rem !important;
    }

    @media (max-width: 768px) {
        .records-container {
            padding: 1rem;
        }

        .records-table-container {
            padding: 1rem;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            width: 100%;
        }

        .table-filters select {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('recordSearch');
        const dateFilter = document.getElementById('dateFilter');
        const table = document.querySelector('.records-table');
        const rows = table.getElementsByTagName('tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const dateValue = dateFilter.value;
            const today = new Date();

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                const dateCell = cells[0];
                const date = new Date(dateCell.textContent);

                let showRow = true;

                // Search filter
                if (searchTerm) {
                    showRow = false;
                    for (let cell of cells) {
                        if (cell.textContent.toLowerCase().includes(searchTerm)) {
                            showRow = true;
                            break;
                        }
                    }
                }

                // Date filter
                if (showRow && dateValue) {
                    const diffTime = Math.abs(today - date);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    switch (dateValue) {
                        case 'today':
                            showRow = diffDays === 0;
                            break;
                        case 'week':
                            showRow = diffDays <= 7;
                            break;
                        case 'month':
                            showRow = diffDays <= 30;
                            break;
                    }
                }

                row.style.display = showRow ? '' : 'none';
            }
        }

        searchInput.addEventListener('input', filterTable);
        dateFilter.addEventListener('change', filterTable);
    });
</script>