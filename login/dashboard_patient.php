<?php
// dashboard_patient.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// fetch user
$stmt = $conn->prepare("SELECT id, username, fullname, avatar FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$fullname = $user['fullname'] ?: ($user['username'] ?: 'User');
$username = $user['username'] ?? 'user';
$avatar_path = $user['avatar'] ?? '';

// fetch appointments
$appointments = [];
if ($q = $conn->prepare("SELECT id, appointment_date, appointment_time, reason FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC LIMIT 8")) {
    $q->bind_param("i", $user_id);
    $q->execute();
    $appointments = $q->get_result()->fetch_all(MYSQLI_ASSOC);
    $q->close();
}

// fetch records
$records = [];
if ($q = $conn->prepare("SELECT id, file_name, file_path, uploaded_at FROM medical_records WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 8")) {
    $q->bind_param("i", $user_id);
    $q->execute();
    $records = $q->get_result()->fetch_all(MYSQLI_ASSOC);
    $q->close();
}

// fetch transactions
$transactions = [];
if ($q = $conn->prepare("SELECT id, amount, description, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10")) {
    $q->bind_param("i", $user_id);
    $q->execute();
    $transactions = $q->get_result()->fetch_all(MYSQLI_ASSOC);
    $q->close();
}

// flash messages
$tx_error = $_SESSION['tx_error'] ?? '';
$tx_success = $_SESSION['tx_success'] ?? '';
unset($_SESSION['tx_error'], $_SESSION['tx_success']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Patient Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<header class="header">
  <h1>Medical Record Management System</h1>
  <p>Digitizing Health Records Security</p>
</header>

<div class="container">
  <aside class="sidebar">
    <div class="user-box">
      <?php if (!empty($avatar_path) && file_exists(__DIR__ . '/' . $avatar_path)): ?>
        <img src="<?= htmlspecialchars($avatar_path) ?>" alt="avatar" class="profile-img" style="width:56px;height:56px;border-radius:12px;object-fit:cover">
      <?php else: ?>
        <div class="avatar"><?= strtoupper(htmlspecialchars(substr($username,0,1))) ?></div>
      <?php endif; ?>

      <div>
        <strong><?= htmlspecialchars($fullname) ?></strong>
        <div class="muted"><?= htmlspecialchars($username) ?></div>
      </div>
    </div>

    <nav class="menu">
      <a class="menu-item active" href="dashboard_patient.php">Dashboard</a>
      <a class="menu-item" href="book_appointment.php">Book Appointment</a>
      <a class="menu-item" href="upload_record.php">Upload Medical Record</a>
      <a class="menu-item" href="edit_profile.php">Edit Profile</a>
      <a class="menu-item logout" href="logout.php">Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="row">
      <div class="card" style="flex:1;min-width:260px;">
        <h3>My Records</h3>
        <?php if (empty($records)): ?>
          <p class="muted">No records uploaded yet.</p>
        <?php else: ?>
          <ul>
            <?php foreach ($records as $r): ?>
              <li><a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank"><?= htmlspecialchars($r['file_name']) ?></a>
                <small class="muted"> — <?= htmlspecialchars(substr($r['uploaded_at'],0,10)) ?></small></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="card" style="flex:1;min-width:260px;">
        <h3>Appointments</h3>
        <?php if (empty($appointments)): ?>
          <p class="muted">No appointments found.</p>
        <?php else: ?>
          <ul>
            <?php foreach ($appointments as $a): ?>
              <li><?= htmlspecialchars($a['appointment_date']) ?> <?= htmlspecialchars($a['appointment_time']) ?>
                <small class="muted"> — <?= htmlspecialchars($a['reason']) ?></small></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <div class="row" style="margin-top:18px;">
      <div class="card" style="flex:2;min-width:360px;">
        <h3>Recent Appointments</h3>
        <?php if (empty($appointments)): ?>
          <p class="muted">No appointments to show.</p>
        <?php else: ?>
          <table class="table-compact">
            <thead><tr><th>Date</th><th>Time</th><th>Reason</th></tr></thead>
            <tbody>
              <?php foreach ($appointments as $a): ?>
                <tr>
                  <td><?= htmlspecialchars($a['appointment_date']) ?></td>
                  <td><?= htmlspecialchars($a['appointment_time']) ?></td>
                  <td><?= htmlspecialchars($a['reason']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <div style="flex:1;display:flex;flex-direction:column;gap:18px;min-width:300px;">
        <div class="card profile-card">
          <h3>Profile</h3>
          <div style="display:flex;gap:12px;align-items:center;">
            <?php if (!empty($avatar_path) && file_exists(__DIR__ . '/' . $avatar_path)): ?>
              <img src="<?= htmlspecialchars($avatar_path) ?>" alt="avatar" class="profile-img">
            <?php else: ?>
              <div class="profile-avatar"><?= strtoupper(substr($username,0,1)) ?></div>
            <?php endif; ?>

            <div class="profile-meta">
              <strong><?= htmlspecialchars($fullname) ?></strong>
              <div class="muted"><?= htmlspecialchars($username) ?></div>
              <div class="profile-actions" style="margin-top:10px;">
                <a class="btn" href="edit_profile.php">Edit Profile</a>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <h3>Transactions</h3>
          <?php if ($tx_error): ?><div class="errors"><?= htmlspecialchars($tx_error) ?></div><?php endif; ?>
          <?php if ($tx_success): ?><div class="success"><?= htmlspecialchars($tx_success) ?></div><?php endif; ?>

          <form method="post" action="save_transaction.php">
            <label>Amount</label>
            <input type="text" name="amount" placeholder="0.00" required>

            <label>Description</label>
            <input type="text" name="description" placeholder="Optional note">

            <button class="btn" type="submit" style="margin-top:10px;">Add Transaction</button>
          </form>

          <div style="margin-top:12px;">
            <h4 style="margin:0 0 8px 0;">Recent</h4>
            <?php if (empty($transactions)): ?>
              <p class="muted">No transactions yet.</p>
            <?php else: ?>
              <table class="table-compact">
                <thead><tr><th>When</th><th style="text-align:right;">Amount</th><th>Note</th></tr></thead>
                <tbody>
                  <?php foreach ($transactions as $t): ?>
                    <tr>
                      <td><?= htmlspecialchars(substr($t['created_at'],0,16)) ?></td>
                      <td style="text-align:right;"><?= number_format($t['amount'],2) ?></td>
                      <td><?= htmlspecialchars($t['description']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>
</body>
</html>
