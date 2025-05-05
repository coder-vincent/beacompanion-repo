<?php
session_start();

require_once(__DIR__ . '/auth/dbconnect.php');

// $isLoggedIn = isset($_SESSION['user']);
// $user = $isLoggedIn ? $_SESSION['user'] : null;

// if (isset($_SESSION['user'])) {
//   $role = $_SESSION['user']['role'] ?? 'patient';
//   $token = urlencode($_SESSION['user']['plain_token']);

//   $host = $_SERVER['HTTP_HOST'];
//   $isLocalhost = strpos($host, 'localhost') !== false;

//   $basePath = $isLocalhost ? '/thesis_project' : ''; // Adjust this if your local folder name is different

//   header("Location: {$basePath}/{$role}/auth-token?token={$token}");
//   exit;
// }

?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Title Here</title>
  <link rel="stylesheet" type="text/css" href="frontend/index.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

</head>

<body>
  <div id="app">
    <!-- Load contents here -->
  </div>

  <script src="./app.js"></script>
</body>

</html>