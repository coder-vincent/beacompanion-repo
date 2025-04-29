<?php
session_start();

if (isset($_SESSION['errors'])) {
  $errors = $_SESSION['errors'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Title Here</title>
  <link rel="stylesheet" type="text/css" href="frontend/index.css">
  <link rel="stylesheet" type="text/css" href="app.php">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>
  <div id="app">
    <!-- Load contents here -->
  </div>

  <script src="./app.js"></script>
</body>

</html>

<?php
?>