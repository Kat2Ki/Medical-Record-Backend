<?php
// hospital_save_patient.php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: hospital_login.php");
    exit();
}
$hospital_id = (int)$_SESSION['user_id'];

$reg_no = trim($_POST['reg_no'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$age    = intval($_POST['age'] ?? 0);
$bsl    = trim($_POST['bsl'] ?? '');
$bp     = trim($_POST['bp'] ?? '');
$o2     = trim($_POST['o2'] ?? '');

if ($reg_no === '') {
    header("Location: hospital_register_patient.php?error=" . urlencode("Registration number required"));
    exit();
}

$stmt = $conn->prepare("INSERT INTO hospital_patients (hospital_id, reg_no, gender, age, bsl, bp, o2) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    header("Location: hospital_register_patient.php?error=" . urlencode("DB prepare failed: " . $conn->error));
    exit();
}
$stmt->bind_param("ississs", $hospital_id, $reg_no, $gender, $age, $bsl, $bp, $o2);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: hospital_dashboard.php?success=" . urlencode("Patient saved"));
    exit();
} else {
    $err = $conn->errno === 1062 ? "Registration number already exists." : "DB error: " . $conn->error;
    $stmt->close();
    header("Location: hospital_register_patient.php?error=" . urlencode($err));
    exit();
}
