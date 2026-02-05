<?php
// Include after session_start() on pages where used
?>
<aside class="sidebar">
  <div class="user-card">
    <div class="avatar"><?php echo isset($_SESSION['fullname'])? strtoupper(substr($_SESSION['fullname'],0,1)) : 'U'; ?></div>
    <div class="user-info">
      <strong><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Guest'); ?></strong>
      <div class="muted"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
    </div>
  </div>

  <nav class="menu">
    <a href="dashboard_patient.php" class="menu-item">Dashboard</a>
    <a href="book_appointment.php" class="menu-item">Book Appointment</a>
    <a href="upload_record.php" class="menu-item">Upload Medical Record</a>
    <a href="complete_profile.php" class="menu-item action">Complete Profile</a>
    <a href="logout.php" class="menu-item logout">Logout</a>
  </nav>
</aside>
