<?php

// if (isset($_SESSION['errors'])) {
//     $errors = $_SESSION['errors'];
// }

?>

<div id="login">

    <!-- Left Container -->
    <?php include '../../components/hero/hero-section.php'; ?>

    <!-- Right Container -->
    <div class="rightDiv">

        <!-- Header -->
        <div class="topend-header">
            <p class="paragraph-fade">
                New User?
                <span class="user-creation">
                    <a href="javascript:void(0)" onclick="loadPage('signupPage')">Sign Up</a>
                </span>
            </p>
        </div>

        <div class="company-name">
            <h2>BEACompanion</h2>
        </div>

        <div class="user-login">
            <h1>Welcome Back!</h1>
            <p class="paragraph-fade">Login to continue</p>
        </div>

        <?php
        if (isset($errors['login'])) {
            echo '<div class="error-main">
            <p>' . $errors['login'] . '</p>
          </div>';
            unset($errors['login']);
        }
        ?>


        <div class="user-login-form">
            <?php

            include($_SERVER['DOCUMENT_ROOT'] . '/thesis_project/frontend/src/components/form/login-form/login-form.php');

            ?>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>