<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}
$user_id = (int)$_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'User';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Book Appointment</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <header class="header">
    <h1>Medical Record Management System</h1>
    <p>Digitizing Health Records Security</p>
  </header>

  <div class="container">
    <?php include 'sidebar_patient.php'; /* optional: small include for sidebar markup */ ?>

    <main class="main">
      <div class="card">
        <h2>Book Appointment</h2>
        <form method="post" action="save_appointment.php">
          <input type="hidden" name="user_id" value="<?= $user_id ?>">
          <label>Appointment Date</label>
          <input type="date" name="appointment_date" required>

          <label>Appointment Time</label>
          <input type="time" name="appointment_time" required>

          <label>Reason (short)</label>
          <input type="text" name="reason" placeholder="e.g. General checkup" maxlength="255">

          <button class="btn" type="submit">Book Appointment</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
