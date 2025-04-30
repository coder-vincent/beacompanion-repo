<form method="POST" id="signup-form" action="userAccount" data-action-key="userAccount">

    <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="name" id="name" placeholder="e.g. Juan Dela Cruz">
        <span class="check-icon" id="name-check">
            <i class="fa-solid fa-check"></i>
        </span>

        <?php
        if (isset($errors['name'])) {
            echo '<div class="error-main"><p>' . $errors['name'] . '</p></div>';
        }
        ?>

    </div>
    <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" id="email" placeholder="e.g. juandelacruz@gmail.com">
        <span class="check-icon" id="email-check">
            <i class="fa-solid fa-check"></i>
        </span>

        <?php
        if (isset($errors['email'])) {
            echo '<div class="error-main"><p>' . $errors['email'] . '</p></div>';
        }
        ?>

    </div>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="create-password" id="create-password" placeholder="Create Password">
        <span class="check-icon" id="createpassword-check">
            <i class="fa-solid fa-check"></i>
        </span>

        <?php
        if (isset($errors['create-password'])) {
            echo '<div class="error-main"><p>' . $errors['create-password'] . '</p></div>';
        }
        ?>

    </div>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password">
        <span class="check-icon" id="confirmpassword-check">
            <i class="fa-solid fa-check"></i>
        </span>

        <?php
        if (isset($errors['confirm-password'])) {
            echo '<div class="error-main"><p>' . $errors['confirm-password'] . '</p></div>';
        }
        ?>

    </div>

    <div class="show-password-container">
        <input type="checkbox" id="show-password" aria-label="Show Password">
        <label for="show-password">Show Password</label>
    </div>


    <input type="submit" class="user-button" name="signup" value="Sign Up">


</form>