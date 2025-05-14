<?php
date_default_timezone_set('Asia/Manila');
$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'admin') {
    header('Location: /thesis_project');
    exit();
}

$stmt = $pdo->prepare('SELECT id, name, email, role, created_at, last_login FROM users WHERE role != "admin" ORDER BY created_at DESC');
$stmt->execute();
$users = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT role, COUNT(*) as count FROM users WHERE role != "admin" GROUP BY role');
$stmt->execute();
$roleCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="users-container">
    <div class="users-header">
        <h2>User Management</h2>
        <button class="add-user-btn" onclick="showAddUserModal()">
            <span class="material-symbols-rounded">person_add</span>
            Add New User
        </button>
    </div>

    <div class="users-stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo array_sum($roleCounts); ?></div>
            <div class="stat-breakdown">
                <?php foreach ($roleCounts as $role => $count): ?>
                    <div class="role-stat">
                        <span class="role-name"><?php echo ucfirst($role); ?>s:</span>
                        <span class="role-count"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="users-table-container">
        <div class="table-header">
            <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="userSearch" placeholder="Search users...">
            </div>
            <div class="table-filters">
                <select id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td><?php echo $user['last_login'] ? date('D, M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" onclick="editUser(<?php echo $user['id']; ?>)">
                                        <span class="material-symbols-rounded">edit</span>
                                    </button>
                                    <button class="action-btn delete" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <span class="material-symbols-rounded">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New User</h3>
            <button class="close-modal" onclick="closeModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <form id="userForm" class="modal-form">
            <input type="hidden" id="userId" name="userId">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                </select>
            </div>
            <div class="form-group password-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small>Leave blank to keep existing password when editing</small>
            </div>
            <div class="form-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="submit-btn">Save User</button>
            </div>
        </form>
    </div>
</div>