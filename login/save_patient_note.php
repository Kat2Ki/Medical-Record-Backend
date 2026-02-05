<?php
// save_patient_note.php
session_start();
require_once 'connect.php';
ini_set('display_errors',1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: hospital_login.php');
    exit();
}
$hospital_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hospital_patients.php');
    exit();
}

$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$diagnosis  = trim($_POST['diagnosis'] ?? '');
$advice     = trim($_POST['advice'] ?? '');
$prescription = trim($_POST['prescription'] ?? '');

if ($patient_id <= 0) {
    $_SESSION['note_error'] = "Invalid patient.";
    header("Location: hospital_patients.php");
    exit();
}

// Verify patient belongs to this hospital
$stmt = $conn->prepare("SELECT id FROM hospital_patients WHERE id = ? AND hospital_id = ? LIMIT 1");
$stmt->bind_param("ii", $patient_id, $hospital_id);
$stmt->execute();
$exists = (bool) $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$exists) {
    $_SESSION['note_error'] = "Unauthorized or patient not found.";
    header("Location: hospital_patients.php");
    exit();
}

// If all fields blank, don't save
if ($diagnosis === '' && $advice === '' && $prescription === '') {
    $_SESSION['note_error'] = "Please enter at least one of diagnosis, advice or prescription.";
    header("Location: patient_history.php?patient_id=" . $patient_id);
    exit();
}

// Insert note
$stmt = $conn->prepare("INSERT INTO hospital_patient_notes (hospital_id, patient_id, diagnosis, advice, prescription, created_by) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    $_SESSION['note_error'] = "DB error: " . $conn->error;
    header("Location: patient_history.php?patient_id=" . $patient_id);
    exit();
}
$created_by = (int)($_SESSION['user_id'] ?? 0);
$stmt->bind_param("iisssi", $hospital_id, $patient_id, $diagnosis, $advice, $prescription, $created_by);

if ($stmt->execute()) {
    $_SESSION['note_success'] = "Note saved.";
} else {
    $_SESSION['note_error'] = "Failed to save note: " . $conn->error;
}
$stmt->close();

header("Location: patient_history.php?patient_id=" . $patient_id);
exit();
