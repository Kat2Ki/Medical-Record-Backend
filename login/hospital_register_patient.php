<?php
// hospital_register_patient.php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: hospital_login.php"); exit(); }
$hospital_name = $_SESSION['fullname'] ?? 'Hospital';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register Patient</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <header class="header"><h1>Register New Patient</h1></header>
  <div class="container" style="padding:24px;">
    <main style="flex:1;max-width:760px;margin:0 auto;">
      <div class="card">
        <form method="post" action="hospital_save_patient.php">
          <label>Registration Number</label>
          <input type="text" name="reg_no" required>

          <label>Gender</label>
          <select name="gender"><option value="">--</option><option>Female</option><option>Male</option><option>Other</option></select>

          <label>Age</label>
          <input type="number" name="age" min="0">

          <label>Blood Sugar Level (BSL)</label>
          <input type="text" name="bsl">

          <label>Blood Pressure (BP)</label>
          <input type="text" name="bp">

          <label>Oxygen Level (O₂)</label>
          <input type="text" name="o2">

          <button class="btn" type="submit" name="save_patient">Save patient</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
