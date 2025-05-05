<form method="POST" id="login-form" action="userAccount" data-action-key="userAccount">

    <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="text" name="email" id="login-email" placeholder="Email">
        <?php
        if (isset($errors['email'])) {
            echo '<div class="error-main"><p>' . $errors['email'] . '</p></div>';
        }
        ?>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" id="login-password" placeholder="Password">
        <i class="fa-solid fa-eye" id="eye"></i>
        <?php
        if (isset($errors['password'])) {
            echo '<div class="error-main"><p>' . $errors['password'] . '</p></div>';
        }
        ?>
    </div>

    <a href="javascript:void(0)" onclick="loadPage('forgotPasswordPage')">Forgot Password?</a>

    <input type="submit" class="user-button" name="login" value="Login">

</form>