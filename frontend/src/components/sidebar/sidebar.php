<?php
// echo '<pre>';
// print_r($_SESSION['user']);
// echo '</pre>';

$role = $_SESSION['user']['role'] ?? '';
$currentPage = $_GET['page'] ?? 'dashboard';

if (in_array($role, ['admin', 'doctor', 'patient'])):
    ?>
    <div class="sidebar-overlay"></div>
    <aside class="sidebar">
        <div class="sidebar-menu">
            <div>
                <?php if ($role === 'admin'): ?>
                    <h3>Main</h3>
                    <ul>
                        <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('adminDashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('users')">
                                <span class="material-symbols-rounded">manage_accounts</span>
                                <span>Manage Users</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                <?php elseif ($role === 'doctor'): ?>
                    <h3>Main</h3>
                    <ul>
                        <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'appointments' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('appointments')">
                                <span class="material-symbols-rounded">event</span>
                                <span>Appointments</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                <?php elseif ($role === 'patient'): ?>
                    <h3>Main</h3>
                    <ul>
                        <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('patientDashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'appointments' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('appointments')">
                                <span class="material-symbols-rounded">event</span>
                                <span>Appointments</span>
                            </a>
                        </li>
                    </ul>
                </div>


            <?php endif; ?>

            <div>
                <h3>Account</h3>
                <ul>
                    <li>
                        <a href="javascript:void(0)" onclick="handleLogout()">
                            <span class="material-symbols-rounded">logout</span>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
<?php endif; ?>