<?php
// patient_history.php
// Location example: D:\XAMP\htdocs\my_project\login\patient_history.php
// Production-ready rewrite: shows patient details, lists notes, and provides the add-note form.
// Expects save_patient_note.php to handle POST and redirect back to this page with flash messages.

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connect.php';

// --- Authentication ---
if (!isset($_SESSION['user_id'])) {
    // not logged in as hospital user
    header('Location: hospital_login.php');
    exit();
}
$hospital_id = (int) $_SESSION['user_id'];

// --- Read patient_id from query string ---
$patient_id = isset($_GET['patient_id']) ? (int) $_GET['patient_id'] : 0;
if ($patient_id <= 0) {
    // Bad request
    http_response_code(400);
    echo "Invalid patient id. Please open this page from the patient list.";
    exit();
}

// --- Fetch patient and ensure it belongs to the logged-in hospital ---
$stmt = $conn->prepare("
    SELECT id, hospital_id, reg_no, gender, age, bsl, bp, o2, created_at
    FROM hospital_patients
    WHERE id = ? LIMIT 1
");
if (!$stmt) {
    http_response_code(500);
    echo "DB error (patient prepare): " . htmlspecialchars($conn->error);
    exit();
}
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$patient) {
    http_response_code(404);
    echo "Patient not found.";
    exit();
}

if ((int)$patient['hospital_id'] !== $hospital_id) {
    http_response_code(403);
    echo "Permission denied: you don't have access to view this patient's history.";
    exit();
}

// --- Flash messages from save_patient_note.php (if any) ---
$note_success = $_SESSION['note_success'] ?? '';
$note_error   = $_SESSION['note_error'] ?? '';
unset($_SESSION['note_success'], $_SESSION['note_error']);

// --- Fetch notes for this patient (most recent first) ---
$notes = [];
$notes_stmt = $conn->prepare("
    SELECT id, diagnosis, advice, prescription, created_by, created_at
    FROM hospital_patient_notes
    WHERE patient_id = ? AND hospital_id = ?
    ORDER BY created_at DESC
    LIMIT 100
");
if ($notes_stmt) {
    $notes_stmt->bind_param("ii", $patient_id, $hospital_id);
    $notes_stmt->execute();
    $notes = $notes_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $notes_stmt->close();
} else {
    // non-fatal: continue but show DB error later
    $notes_error = "DB error fetching notes: " . htmlspecialchars($conn->error);
}

// --- HTML output ---
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Patient History — <?= htmlspecialchars($patient['reg_no']) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* small inline styles that won't conflict with main CSS */
    .muted { color:#6b7280; font-size:14px; }
    .card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.04); margin-bottom:18px; }
    .note-box { background:#fff; padding:14px; border-radius:10px; margin-bottom:12px; box-shadow:0 6px 18px rgba(0,0,0,0.03); }
    .note-meta { color:#6b7280; font-size:13px; margin-bottom:8px; }
    label { display:block; margin-top:12px; font-weight:600; }
    textarea { width:100%; min-height:110px; padding:10px; border-radius:8px; border:1px solid #e6edf3; resize:vertical; }
    .btn { display:inline-block; padding:10px 14px; border-radius:8px; background:linear-gradient(90deg,#5b57d9,#00b894); color:#fff; border:none; cursor:pointer; text-decoration:none; font-weight:700; }
    .btn.secondary { background:#f3f7fb; color:#5b57d9; border:1px solid #e6edf3; }
    .page-grid { display:grid; grid-template-columns: 260px 1fr; gap:20px; padding:20px; }
    .profile-row { display:flex; gap:12px; align-items:center; }
    .profile-avatar { width:56px;height:56px;border-radius:12px; display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#5b57d9,#00b894); color:#fff; font-weight:700; font-size:20px; }
    .error { color:#b00020; margin-bottom:10px; }
    .success { color:#0b8a41; margin-bottom:10px; }
  </style>
</head>
<body>

<header class="header">
  <h1>Patient History</h1>
  <p class="muted">Hospital: <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Hospital') ?></p>
</header>

<div class="page-grid">

  <!-- Sidebar (small) -->
  <aside>
    <div class="card">
      <div class="profile-row">
        <div class="profile-avatar"><?= htmlspecialchars(strtoupper(substr((string)($_SESSION['username'] ?? 'H'), 0, 1))) ?></div>
        <div>
          <strong><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Hospital') ?></strong><br>
          <span class="muted"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
        </div>
      </div>

      <nav style="margin-top:16px;">
        <a class="btn secondary" href="hospital_dashboard.php">← Dashboard</a>
        <a class="btn secondary" href="hospital_patients.php" style="margin-left:8px;">Patient List</a>
      </nav>
    </div>
  </aside>

  <!-- Main -->
  <main>
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:start;gap:16px;">
        <div>
          <h2 style="margin:0 0 6px 0;">Patient: <?= htmlspecialchars($patient['reg_no']) ?></h2>
          <div class="muted">
            Gender: <?= htmlspecialchars($patient['gender'] ?: '-') ?> •
            Age: <?= (int)$patient['age'] ?: '-' ?> •
            BSL: <?= htmlspecialchars($patient['bsl'] ?: '-') ?> •
            BP: <?= htmlspecialchars($patient['bp'] ?: '-') ?> •
            O₂: <?= htmlspecialchars($patient['o2'] ?: '-') ?>
          </div>
          <div class="muted" style="margin-top:6px;">Added: <?= htmlspecialchars(substr($patient['created_at'],0,16)) ?></div>
        </div>

        <div>
          <a class="btn" href="hospital_patients.php">Back to Patients</a>
        </div>
      </div>

      <?php if ($note_success): ?>
        <div class="success"><?= htmlspecialchars($note_success) ?></div>
      <?php endif; ?>
      <?php if ($note_error): ?>
        <div class="error"><?= htmlspecialchars($note_error) ?></div>
      <?php endif; ?>

      <?php if (!empty($notes_error)): ?>
        <div class="error"><?= htmlspecialchars($notes_error) ?></div>
      <?php endif; ?>

      <hr style="margin:18px 0; border:none; border-top:1px solid #f0f5f9;">

      <!-- Add new note form -->
      <form method="post" action="save_patient_note.php">
        <input type="hidden" name="patient_id" value="<?= (int)$patient_id ?>">
        <label for="diagnosis">Diagnosis <small class="muted">(writable)</small></label>
        <textarea id="diagnosis" name="diagnosis" placeholder="Enter diagnosis..."></textarea>

        <label for="advice">Advice <small class="muted">(writable)</small></label>
        <textarea id="advice" name="advice" placeholder="Enter advice / counseling notes..."></textarea>

        <label for="prescription">Prescription <small class="muted">(writable)</small></label>
        <textarea id="prescription" name="prescription" placeholder="Medications, dose, duration..."></textarea>

        <div style="margin-top:12px;">
          <button type="submit" class="btn">Save Note</button>
        </div>
      </form>
    </div>

    <!-- Recent notes -->
    <div class="card">
      <h3 style="margin-top:0;">Recent Notes</h3>

      <?php if (empty($notes)): ?>
        <p class="muted">No notes yet.</p>
      <?php else: ?>
        <?php foreach ($notes as $n): ?>
          <div class="note-box">
            <div class="note-meta">
              Added: <?= htmlspecialchars($n['created_at']) ?>
              <?php if (!empty($n['created_by'])): ?>
                &nbsp; • &nbsp; by user #<?= (int)$n['created_by'] ?>
              <?php endif; ?>
            </div>

            <?php if (trim($n['diagnosis']) !== ''): ?>
              <div><strong>Diagnosis</strong>
                <div style="white-space:pre-wrap;margin-top:6px;"><?= nl2br(htmlspecialchars($n['diagnosis'])) ?></div>
              </div>
            <?php endif; ?>

            <?php if (trim($n['advice']) !== ''): ?>
              <div style="margin-top:8px;"><strong>Advice</strong>
                <div style="white-space:pre-wrap;margin-top:6px;"><?= nl2br(htmlspecialchars($n['advice'])) ?></div>
              </div>
            <?php endif; ?>

            <?php if (trim($n['prescription']) !== ''): ?>
              <div style="margin-top:8px;"><strong>Prescription</strong>
                <div style="white-space:pre-wrap;margin-top:6px;"><?= nl2br(htmlspecialchars($n['prescription'])) ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </main>
</div>

</body>
</html>
