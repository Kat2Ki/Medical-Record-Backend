<?php
session_start();
require_once 'connect.php';
if(!isset($_SESSION['user_id'])) { header('Location: login_patient.php'); exit(); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Complete Profile</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
  <?php include 'sidebar_patient.php'; ?>

  <main class="main">
    <div class="card">
      <h3>Complete Profile</h3>
      <p>Updated profile details here</p>
      <a href="dashboard_patient.php" class="small-btn">Back</a>
    </div>
  </main>
</div>
</body>
</html>
