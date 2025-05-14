<?php
session_start();

require_once(__DIR__ . '/auth/dbconnect.php');


?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BEACompanion</title>
  <link rel="icon" type="image/svg+xml" href="frontend/public/images/beacompanion-logo.svg">

  <link rel="stylesheet" type="text/css" href="frontend/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>

<body>
  <div id="app">
    <!-- Load contents here -->
  </div>

  <script src="./app.js"></script>
</body>

</html>