<div id="sign-up">

    <!-- Left Container -->
    <?php include '../../components/hero/hero-section.php'; ?>

    <!-- Right Container -->
    <div class="rightDiv">

        <div class="rightDiv-contents">
            <!-- Header -->
            <div class="topend-header">
                <p class="paragraph-fade">
                    Already have an account?
                    <span class="user-creation">
                        <a href="javascript:void(0)" onclick="loadPage('loginPage')">Sign In</a>
                    </span>
                </p>
            </div>
            <div class="rightDiv-container">

                <div class="user-create">
                    <h1>Create Account</h1>
                </div>

                <?php
                if (isset($errors['user_exist'])) {
                    echo '<div class="error-main"><p>' . $errors['user_exist'] . '</p></div>';
                }
                ?>


                <div class="user-create-form">
                    <?php
                    include('../../components/form/signup-form/signup-form.php');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>