<?php
session_start();
require_once "connect.php";

// require login
if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$username = $_SESSION['username'];

// Handle appointment submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $reason = trim($_POST['reason']);

    if (!empty($date) && !empty($time) && !empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $date, $time, $reason);

        if ($stmt->execute()) {
            $message = "Appointment booked successfully!";
        } else {
            $message = "Error saving appointment.";
        }
        $stmt->close();
    } else {
        $message = "Please fill all fields.";
    }
}
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

<div class="header">Health Record Management System.</div>

<div class="wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile-block">
        <div class="avatar"><?php echo strtoupper(substr($username,0,1)); ?></div>
        <div>
          <h3><?php echo htmlspecialchars($fullname); ?></h3>
          <p><?php echo htmlspecialchars($username); ?></p>
        </div>
      </div>

      <nav>
        <a class="menu-link" href="dashboard_patient.php">Dashboard</a>
        <a class="menu-link" href="create_appointment.php">Book Appointment</a>
        <a class="menu-link" href="upload_record.php">Upload Medical Record</a>
        <a class="menu-link logout" href="logout.php">Logout</a>
      </nav>
    </aside>

    <!-- Main -->
    <main class="main">

      <div class="form-card">
        <h2>Book Appointment</h2>

        <?php if ($message): ?>
            <p style="color:green;font-weight:600;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Date</label>
            <input type="date" name="appointment_date" required>

            <label>Time</label>
            <input type="time" name="appointment_time" required>

            <label>Reason</label>
            <textarea name="reason" placeholder="Reason for appointment" required></textarea>

            <button class="btn" type="submit">Book</button>
        </form>
      </div>

    </main>

</div>

</body>
</html>
