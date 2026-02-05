<?php
// hospital_patients.php
// Location: D:\XAMP\htdocs\my_project\login\hospital_patients.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connect.php';

// require hospital login
if (!isset($_SESSION['user_id'])) {
    header('Location: hospital_login.php');
    exit();
}

$hospital_id = (int)$_SESSION['user_id'];
$hospital_name = $_SESSION['fullname'] ?? 'Hospital';
$username = $_SESSION['username'] ?? 'hospital';

// Fetch patients for this hospital
$patients = [];
if ($stmt = $conn->prepare("
    SELECT id, reg_no, gender, age, bsl, bp, o2, created_at
    FROM hospital_patients
    WHERE hospital_id = ?
    ORDER BY created_at DESC
")) {
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $patients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // prepare failed — helpful for debugging
    die("DB prepare failed: " . htmlspecialchars($conn->error));
}

// DEBUG: Uncomment if you want to inspect results quickly
// echo "<pre>Patients count: " . count($patients) . "\nSample row:\n"; print_r($patients[0] ?? 'none'); echo "</pre>";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Patient List — Hospital</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* small table tweaks */
    .patients-table { width:100%; border-collapse:collapse; margin-top:12px; }
    .patients-table th, .patients-table td { padding:10px; border-bottom:1px solid #eef3f8; text-align:left; vertical-align:middle; }
    .patients-table thead th { background:#f6fbff; font-weight:700; }
    .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:linear-gradient(90deg,#5b57d9,#00b894); color:#fff; text-decoration:none; font-weight:700; }
    .btn.secondary { background:#f3f7fb; color:#5b57d9; border:1px solid #e6edf3; }
    .muted { color:#6b7280; font-size:13px; }
    .container-inner { padding:18px; }
    .nowrap { white-space:nowrap; }
  </style>
</head>
<body>
  <header class="header">
    <h1>Hospital — Patient List</h1>
    <p class="muted">Welcome, <?= htmlspecialchars($hospital_name) ?></p>
  </header>

  <div class="container container-inner">
    <div class="card" style="width:100%">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
          <h3 style="margin:0 0 6px 0;">Registered Patients</h3>
          <div class="muted">Patients registered under this hospital account</div>
        </div>
        <div>
          <a class="btn secondary" href="hospital_dashboard.php">← Dashboard</a>
          <a class="btn" href="hospital_register_patient.php" style="margin-left:8px;">Register Patient</a>
        </div>
      </div>

      <?php if (empty($patients)): ?>
        <p class="muted" style="margin-top:16px;">No patients found.</p>
      <?php else: ?>
        <table class="patients-table" role="table" aria-label="Patients">
          <thead>
            <tr>
              <th>Reg No</th>
              <th>Gender</th>
              <th>Age</th>
              <th>BSL</th>
              <th>BP</th>
              <th>O₂</th>
              <th>Added</th>
              <th class="nowrap">History</th> <!-- NEW -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($patients as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['reg_no']) ?></td>
              <td><?= htmlspecialchars($p['gender']) ?></td>
              <td><?= (int)$p['age'] ?></td>
              <td><?= htmlspecialchars($p['bsl']) ?></td>
              <td><?= htmlspecialchars($p['bp']) ?></td>
              <td><?= htmlspecialchars($p['o2']) ?></td>
              <td><?= htmlspecialchars(substr($p['created_at'],0,16)) ?></td>

              <!-- History button column -->
              <td>
                <!-- safe integer casting, opens patient_history.php with patient_id parameter -->
                <a class="btn" href="patient_history.php?patient_id=<?= (int)$p['id'] ?>">History</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
