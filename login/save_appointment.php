<?php
session_start();
require_once 'connect.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book'])) {
    header('Location: book_appointment.php');
    exit;
}

$user_id = (int)($_POST['user_id'] ?? 0);
$date = $_POST['appointment_date'] ?? '';
$time = $_POST['appointment_time'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if(!$user_id || !$date || !$time) {
    die('Missing required fields. <a href="book_appointment.php">Back</a>');
}

// use prepared statements
$stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, appointment_time, reason, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("isss", $user_id, $date, $time, $reason);

if($stmt->execute()) {
    header("Location: dashboard_patient.php?msg=appointment_ok");
    exit;
} else {
    echo "Error saving appointment: " . htmlspecialchars($conn->error);
}
$stmt->close();
$conn->close();
