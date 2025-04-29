<form method="POST" id="signup-form" data-action-key="userAccount">

    <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="name" name="name" id="name" placeholder="e.g. Juan Dela Cruz" required>
        <span class="check-icon" id="name-check">
            <i class="fa-solid fa-check"></i>
        </span>
    </div>
    <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" id="email" placeholder="e.g. juandelacruz@gmail.com" required>
        <span class="check-icon" id="email-check">
            <i class="fa-solid fa-check"></i>
        </span>
    </div>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="create-password" id="create-password" placeholder="Create Password" required>
        <span class="check-icon" id="createpassword-check">
            <i class="fa-solid fa-check"></i>
        </span>
    </div>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
        <span class="check-icon" id="confirmpassword-check">
            <i class="fa-solid fa-check"></i>
        </span>
    </div>

    <div class="show-password-container">
        <input type="checkbox" id="show-password" aria-label="Show Password">
        <label for="show-password">Show Password</label>
    </div>


    <input type="submit" class="user-button" value="Sign Up">

</form>