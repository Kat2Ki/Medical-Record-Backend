<?php
// sidebar_patient.php - small snippet used by multiple pages
$username = $_SESSION['username'] ?? 'user';
$fullname = $_SESSION['fullname'] ?? $username;
?>
<aside class="sidebar">
  <div class="user-box">
    <div class="avatar"><?= strtoupper(substr($username,0,1)) ?></div>
    <div>
      <strong><?= htmlspecialchars($fullname) ?></strong>
      <p class="muted"><?= htmlspecialchars($username) ?></p>
      <a href="complete_profile.php">Complete profile</a>
    </div>
  </div>

  <nav class="menu">
    <a class="menu-item" href="dashboard_patient.php">Dashboard</a>
    <a class="menu-item" href="book_appointment.php">Book Appointment</a>
    <a class="menu-item" href="upload_record.php">Upload Medical Record</a>
    <a class="menu-item logout" href="logout.php">Logout</a>
  </nav>

  <div class="quick-box">
    <h4>Quick Actions</h4>
    <a class="quick-btn" href="complete_profile.php">Complete Profile</a>
    <a class="quick-btn" href="upload_record.php">Upload Record</a>
  </div>
</aside>
