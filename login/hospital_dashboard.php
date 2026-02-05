<?php
// hospital_dashboard.php
// Replace your current hospital dashboard with this file.
// Path suggestion: D:\XAMP\htdocs\my_project\login\hospital_dashboard.php

session_start();
require_once 'connect.php';

// ensure logged in (hospital user)
if (!isset($_SESSION['user_id'])) {
    header("Location: hospital_login.php");
    exit();
}

$hospital_id = (int)$_SESSION['user_id'];
$hospital_name = $_SESSION['fullname'] ?? 'Hospital';
$username = $_SESSION['username'] ?? 'hospital';

$errors = [];
$success = "";

// --- Handle saving a diagnosis note ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    // validate
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $diagnosis  = trim($_POST['diagnosis'] ?? '');
    $advice     = trim($_POST['advice'] ?? '');
    $prescription = trim($_POST['prescription'] ?? '');

    if ($patient_id <= 0) $errors[] = "Please choose a patient.";
    if ($diagnosis === '' && $advice === '' && $prescription === '') $errors[] = "Please fill at least one of Diagnosis, Advice or Prescription.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
          INSERT INTO hospital_patient_notes (hospital_id, patient_id, diagnosis, advice, prescription, created_by)
          VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            $errors[] = "DB prepare failed: " . $conn->error;
        } else {
            $created_by = $hospital_id; // could be staff user id
            $stmt->bind_param("iisssi", $hospital_id, $patient_id, $diagnosis, $advice, $prescription, $created_by);
            if ($stmt->execute()) {
                $success = "Note saved successfully.";
            } else {
                $errors[] = "Failed to save note: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// --- Fetch patients for the hospital (to populate select) ---
$patients = [];
if ($pstmt = $conn->prepare("SELECT id, reg_no, gender, age FROM hospital_patients WHERE hospital_id = ? ORDER BY created_at DESC")) {
    $pstmt->bind_param("i", $hospital_id);
    $pstmt->execute();
    $patients = $pstmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $pstmt->close();
}

// --- Fetch recent notes (latest 12) ---
$notes = [];
if ($np = $conn->prepare("SELECT n.id, n.patient_id, n.diagnosis, n.advice, n.prescription, n.created_at, p.reg_no FROM hospital_patient_notes n LEFT JOIN hospital_patients p ON p.id = n.patient_id WHERE n.hospital_id = ? ORDER BY n.created_at DESC LIMIT 12")) {
    $np->bind_param("i", $hospital_id);
    $np->execute();
    $notes = $np->get_result()->fetch_all(MYSQLI_ASSOC);
    $np->close();
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Hospital Dashboard</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    .form-grid { display:grid; grid-template-columns: 1fr; gap:10px; }
    label { font-weight:600; margin-top:8px; display:block; }
    textarea { min-height:90px; resize:vertical; }
    .note-list { margin-top:12px; max-height:420px; overflow:auto; }
    .note-item { border-bottom:1px solid #eef3f8; padding:10px 0; }
    .muted { color:#6b7280; font-size:13px; }
  </style>
</head>
<body>
<header class="header">
  <h1>Hospital Dashboard</h1>
  <p>Welcome, <?= htmlspecialchars($hospital_name) ?></p>
</header>

<div class="container">
  <aside class="sidebar">
    <div class="user-box">
      <div class="avatar">H</div>
      <div>
        <strong><?= htmlspecialchars($hospital_name) ?></strong>
        <p class="muted"><?= htmlspecialchars($username) ?></p>
      </div>
    </div>

    <nav class="menu">
      <a class="active" href="hospital_dashboard.php">Dashboard</a>
      <a href="hospital_register_patient.php">Register Patient</a>
      <a href="hospital_patients.php">Patient List</a>
      <a href="logout.php" style="background:#ff9aa2;color:#fff">Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="card">
      <h2>Patient Notes — Diagnosis · Advice · Prescription</h2>
      <p class="muted">Select patient, add diagnosis, advice and prescription. All three fields are editable and saved as a single note record.</p>

      <?php if (!empty($errors)): ?>
        <div class="errors" style="color:#b00020;margin-bottom:8px;">
          <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success" style="color:#0b8a41;margin-bottom:8px;"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" action="hospital_dashboard.php">
        <div class="form-grid">
          <div>
            <label for="patient_id">Patient</label>
            <select name="patient_id" id="patient_id" required>
              <option value="">-- choose patient --</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['reg_no']) ?> — <?= htmlspecialchars($p['gender']) ?> <?= (int)$p['age'] ?>y</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label for="diagnosis">Diagnosis <span class="muted">(writable)</span></label>
            <textarea id="diagnosis" name="diagnosis" placeholder="Enter diagnosis..."></textarea>
          </div>

          <div>
            <label for="advice">Advice <span class="muted">(writable)</span></label>
            <textarea id="advice" name="advice" placeholder="Enter advice / counseling notes..."></textarea>
          </div>

          <div>
            <label for="prescription">Prescription <span class="muted">(writable)</span></label>
            <textarea id="prescription" name="prescription" placeholder="Medications, dose, duration..."></textarea>
          </div>

          <div>
            <button type="submit" name="save_note" class="btn">Save Note</button>
          </div>
        </div>
      </form>
    </div>

    <div class="card">
      <h3>Recent Notes</h3>
      <div class="note-list">
        <?php if (empty($notes)): ?>
          <p class="muted">No notes yet.</p>
        <?php else: ?>
          <?php foreach ($notes as $n): ?>
            <div class="note-item">
              <div style="display:flex;justify-content:space-between;align-items:center;">
                <div><strong>Patient: <?= htmlspecialchars($n['reg_no'] ?: $n['patient_id']) ?></strong></div>
                <div class="muted"><?= htmlspecialchars($n['created_at']) ?></div>
              </div>
              <?php if (trim($n['diagnosis'])!==""): ?>
                <div><strong>Diagnosis:</strong> <?= nl2br(htmlspecialchars($n['diagnosis'])) ?></div>
              <?php endif; ?>
              <?php if (trim($n['advice'])!==""): ?>
                <div><strong>Advice:</strong> <?= nl2br(htmlspecialchars($n['advice'])) ?></div>
              <?php endif; ?>
              <?php if (trim($n['prescription'])!==""): ?>
                <div><strong>Prescription:</strong> <?= nl2br(htmlspecialchars($n['prescription'])) ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>
