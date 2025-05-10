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
                        <h3>Information</h3>
                        <li class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('adminDashboard?page=about')">
                                <span class="material-symbols-rounded">info</span>
                                <span>About</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'faq' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('adminDashboard?page=faq')">
                                <span class="material-symbols-rounded">help</span>
                                <span>FAQ</span>
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
                        <li class="<?php echo $currentPage === 'patients' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard?page=patients')">
                                <span class="material-symbols-rounded">patient_list</span>
                                <span>Patients</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard?page=about')">
                                <span class="material-symbols-rounded">info</span>
                                <span>About</span>
                            </a>
                        </li>
                        <li class="<?php echo $currentPage === 'faq' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('doctorDashboard?page=faq')">
                                <span class="material-symbols-rounded">help</span>
                                <span>FAQ</span>
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
                        <li class="<?php echo $currentPage === 'faq' ? 'active' : ''; ?>">
                            <a href="javascript:void(0)" onclick="loadPage('patientDashboard?page=faq')">
                                <span class="material-symbols-rounded">help</span>
                                <span>FAQ</span>
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