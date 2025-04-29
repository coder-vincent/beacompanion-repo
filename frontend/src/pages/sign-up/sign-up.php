<div id="sign-up">
    <!-- Left Container -->
    <div class="leftDiv">
        <div class="image-container">
            <img src="frontend/src/assets/images/weak-hero.jpg" alt="image">
            <div class="overlay"></div>
            <div class="text-overlay">
                <h1>Welcome to<br>BEACompanion!👋</h1>
                <p>Understand behavioral and speech patterns <b>like never before!</b> Gain deep insights into ADHD
                    assessment
                    through intelligent monitoring and analysis—helping children thrive with confidence.</p>
            </div>
        </div>
    </div>

    <!-- Right Container -->
    <div class="rightDiv">

        <!-- Header -->
        <div class="topend-header">
            <p class="paragraph-fade">
                Already have an account?
                <span class="user-creation">
                    <a href="javascript:void(0)" onclick="loadPage('loginPage')">Sign In</a>
                </span>
            </p>
        </div>

        <div class="company-name">
            <h2>BEACompanion</h2>
        </div>

        <div class="user-login">
            <h1>Create Account</h1>
        </div>

        <?php
        if (isset($errors['user_exist'])) {
            echo '<div class="error"><p>' . $errors['user_exist'] . '</p></div>';
        }
        ?>


        <div class="user-login-form">
            <?php

            include($_SERVER['DOCUMENT_ROOT'] . '/thesis_project/frontend/src/components/form/signup-form/signup-form.php');

            ?>
        </div>
    </div>
</div>