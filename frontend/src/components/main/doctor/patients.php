<?php
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'doctor') {
    header('Location: /thesis_project');
    exit();
}

// Pagination settings
$patients_per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1; // Ensure page is at least 1
$offset = ($page - 1) * $patients_per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = $search ? "AND (u.name LIKE :search OR u.email LIKE :search)" : "";
$search_param = $search ? "%$search%" : null;

// Get total count for pagination
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM users u 
    WHERE u.role = 'patient' 
    AND u.doctor_id = :doctor_id 
    $search_condition
");
$count_params = ['doctor_id' => $user['id']];
if ($search_param) {
    $count_params['search'] = $search_param;
}
$count_stmt->execute($count_params);
$total_patients = $count_stmt->fetchColumn();
$total_pages = ceil($total_patients / $patients_per_page);

// Ensure page doesn't exceed total pages
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $offset = ($page - 1) * $patients_per_page;
}
?>

<div class="patients-container" data-doctor-id="<?php echo $user['id']; ?>">
    <div class="patients-header">
        <h2>Patients Management</h2>
        <div class="patients-controls">
            <div class="search-container">
                <div class="search-form">
                    <input type="text" id="patientSearch" name="search" placeholder="Search patients..."
                        value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                    <span class="material-icons search-icon">search</span>
                </div>
            </div>
            <button type="button" class="action-btn assign-btn" data-action="showUnassignedModal"
                id="showUnassignedModalBtn">
                <span class="material-icons">person_add</span>
                Add New Patient
            </button>
        </div>
    </div>

    <div class="patients-table-container">
        <div class="table-responsive">
            <table class="patients-table" id="myPatientsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Last Observation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch doctor's assigned patients with pagination and search
                    $stmt = $pdo->prepare("
                        SELECT u.id, u.name, u.email, 
                               MAX(o.created_at) as last_observation
                        FROM users u
                        LEFT JOIN observations o ON u.id = o.patient_id
                        WHERE u.role = 'patient' 
                        AND u.doctor_id = :doctor_id 
                        $search_condition
                        GROUP BY u.id, u.name, u.email
                        LIMIT :limit OFFSET :offset
                    ");

                    // Bind parameters separately to ensure proper type handling
                    $stmt->bindValue(':doctor_id', $user['id'], PDO::PARAM_INT);
                    $stmt->bindValue(':limit', $patients_per_page, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

                    if ($search_param) {
                        $stmt->bindValue(':search', $search_param, PDO::PARAM_STR);
                    }

                    $stmt->execute();
                    $myPatients = $stmt->fetchAll();

                    if (empty($myPatients)): ?>
                        <tr>
                            <td colspan="4" class="no-records">
                                <?php echo $search ? 'No patients found matching your search.' : 'No patients assigned to you yet.'; ?>
                            </td>
                        </tr>
                    <?php else:
                        foreach ($myPatients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                <td class="email-cell">
                                    <span class="truncate-email" title="<?php echo htmlspecialchars($patient['email']); ?>">
                                        <?php echo htmlspecialchars($patient['email']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    if ($patient['last_observation']) {
                                        echo date('F j, Y', strtotime($patient['last_observation']));
                                    } else {
                                        echo 'No observations yet';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view-btn" data-action="view"
                                            data-patient-id="<?php echo $patient['id']; ?>">
                                            <span class="material-icons">visibility</span>
                                            View Records
                                        </button>
                                        <button class="action-btn remove-btn" data-action="remove"
                                            data-patient-id="<?php echo $patient['id']; ?>">
                                            <span class="material-icons">person_remove</span>
                                            Remove
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                    class="page-btn" data-page="<?php echo $page - 1; ?>">
                    <span class="material-icons">chevron_left</span>
                </a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                    class="page-btn <?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"
                    class="page-btn" data-page="<?php echo $page + 1; ?>">
                    <span class="material-icons">chevron_right</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Unassigned Patients Modal -->
    <div id="unassignedPatientsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Unassigned Patients</h3>
                <button class="close-modal" data-action="closeModal">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="search-container">
                    <input type="text" id="unassignedSearch" placeholder="Search unassigned patients..."
                        class="search-input">
                </div>
                <div class="table-responsive">
                    <table class="patients-table" id="unassignedPatientsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch unassigned patients
                            $stmt = $pdo->prepare('
                                SELECT id, name, email 
                                FROM users 
                                WHERE role = "patient" 
                                AND doctor_id IS NULL
                            ');
                            $stmt->execute();
                            $unassignedPatients = $stmt->fetchAll();

                            if (empty($unassignedPatients)): ?>
                                <tr>
                                    <td colspan="3" class="no-records">No unassigned patients found.</td>
                                </tr>
                            <?php else:
                                foreach ($unassignedPatients as $patient): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td class="email-cell">
                                            <span class="truncate-email"
                                                title="<?php echo htmlspecialchars($patient['email']); ?>">
                                                <?php echo htmlspecialchars($patient['email']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn assign-btn" data-action="assign"
                                                    data-patient-id="<?php echo $patient['id']; ?>">
                                                    <span class="material-icons">person_add</span>
                                                    Assign to Me
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Records Modal -->
    <div id="patientRecordsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Patient Records</h3>
                <button class="close-modal" data-action="closeModal">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="patientRecordsContent">
                    <!-- Records will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>