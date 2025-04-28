<div id="login">

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


        <div class="user-login-form">
            <?php

            include($_SERVER['DOCUMENT_ROOT'] . '/thesis_project/frontend/src/components/form/login-form/login-form.php');

            ?>
        </div>
    </div>
    <!-- <button onclick="loadPage('signupPage')">Other Page</button> -->
</div>