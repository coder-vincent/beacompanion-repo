<form method="POST" id="forgot-password-form" action="userAccount" data-action-key="userAccount">
    <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="text" name="forgot-password-email" id="forgot-password-email" placeholder="Email"
            value="<?= htmlspecialchars($_POST['forgot-password-email'] ?? '') ?>">

        <?php
        if (isset($errors['forgot-password-email'])) {
            echo '<div class="error-main">
            <p>' . htmlspecialchars($errors['forgot-password-email']) . '</p>
          </div>';
        } elseif (isset($successMessage)) {
            echo '<div class="success-main">
            <p>' . htmlspecialchars($successMessage) . '</p>
          </div>';
        }
        ?>

    </div>

    <input type="submit" class="user-button" name="reset-password" value="Reset Password">
</form>