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
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="/thesis_project/frontend/src/assets/images/logo-beacompanion.png" alt="BEACompanion Logo">
            </div>
            <div class="sidebar-company">
                <div class="sidebar-company-name">BEACompanion</div>
                <div class="sidebar-company-tagline">Your Health Partner</div>
            </div>
        </div>
        <div class="sidebar-menu">
            <div>
                <?php if ($role === 'admin'): ?>
                    <h3>Main</h3>
                    <ul>
                        <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('adminDashboard?page=dashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('adminDashboard?page=users')">
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
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard?page=dashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'appointments' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard?page=appointments')">
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
                            <a href="javascript:void(0)" onclick="loadPage('patientDashboard?page=dashboard')">
                                <span class="material-symbols-rounded">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('patientDashboard?page=about')">
                                <span class="material-symbols-rounded">info</span>
                                <span>About</span>
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