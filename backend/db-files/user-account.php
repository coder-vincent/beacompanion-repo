<?php
require_once(dirname(__FILE__) . "../auth/dbconnect.php");

session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sign-up'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
}
?>