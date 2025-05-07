<div id="forgot-password-page">

    <!-- Left Container -->
    <?php include '../../components/hero/hero-section.php'; ?>

    <!-- Right Container -->
    <div class="rightDiv">

        <div class="rightDiv-contents">
            <span class="user-back" id="backBtnWrapper">
                <a href="javascript:void(0)" onclick="loadPage('loginPage')" id="backBtn">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span class="back-text">Back</span>
            </span>

            <div class="rightDiv-container">
                <div class="company-name">
                    <h2>BEACompanion</h2>
                </div>

                <div class="user-forgot-password">
                    <h1>Forgot Password</h1>
                    <p class="paragraph-fade">Please enter the email address you'd like your password reset information
                        sent
                        to
                    </p>
                </div>

                <div class="user-forgot-password-form">
                    <?php

                    include($_SERVER['DOCUMENT_ROOT'] . '/thesis_project/frontend/src/components/form/forgot-password-form/forgot-password-form.php');

                    ?>
                </div>
            </div>
        </div>

    </div>

</div>