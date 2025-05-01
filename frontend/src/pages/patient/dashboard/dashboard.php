<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: loginPage');
    exit;
}

$fullName = $_SESSION['user']['name'];
$firstName = explode(' ', trim($fullName))[0];
?>

<div id="patient-page">
    <div class="welcome-reveal">
        <div class="welcome-container">
            <div class="welcome-loader">
                <h1>Hi, <?php echo htmlspecialchars($firstName); ?>!👋</h1>
            </div>
            <div class="welcome-overlay">
                <?php
                for ($i = 1; $i <= 20; $i++) {
                    echo "<div class='block block-$i'></div>";
                }
                ?>
            </div>
        </div>
    </div>
    <h2>You are now logged in</h2>

    <div id="patient-dashboard">
        <h3>Hello, <?php echo htmlspecialchars($firstName); ?>!</h3>
    </div>
</div>