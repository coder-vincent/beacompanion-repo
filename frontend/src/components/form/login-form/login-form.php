<form method="POST" id="login-form" data-action-key="userAccount">
    <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" id="email" placeholder="Email" required>
    </div>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <i class="fa-solid fa-eye" id="eye"></i>
    </div>

    <a href="javascript:void(0)" id="forgot-password" onclick="loadPage('forgotPasswordPage')">Forgot
        Password?</a>

    <input type="submit" class="user-button" name="login" value="Login">

</form>