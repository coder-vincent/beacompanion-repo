<form method="POST" id="reset-password-form" action="userAccount" data-action-key="userAccount">

    <?php if (isset($_SESSION['user_reset_pass']['token'])): ?>
        <!-- Always sanitize token for security -->
        <input type="text" name="token" id="reset-token"
            value="<?php echo htmlspecialchars($_SESSION['user_reset_pass']['token']); ?>">
    <?php endif; ?>

    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="new-password" id="new-password" placeholder="New Password">
        <i class="fa-solid fa-eye-slash password-toggle" data-target="new-password"></i>
        <?php
        if (isset($errors['password'])) {
            echo '<div class="error-main"><p>' . htmlspecialchars($errors['password']) . '</p></div>';
        }
        ?>
    </div>

    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="confirm-new-password" id="confirm-new-password" placeholder="Confirm New Password">
        <i class="fa-solid fa-eye-slash password-toggle" data-target="confirm-new-password"></i>
        <?php
        if (isset($errors['confirm_password'])) {
            echo '<div class="error-main"><p>' . htmlspecialchars($errors['confirm_password']) . '</p></div>';
        }
        ?>
    </div>

    <input type="submit" class="user-button" name="change-password" value="Change Password">

</form>