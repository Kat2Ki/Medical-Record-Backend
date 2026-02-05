<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$age = $_POST['age'];
$address = $_POST['address'];
$avatar_path = "";

// Handle avatar upload
if (!empty($_FILES['avatar']['name'])) {
    $filename = "uploads/avatar_" . $user_id . "_" . time() . ".jpg";
    move_uploaded_file($_FILES['avatar']['tmp_name'], $filename);
    $avatar_path = $filename;

    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $avatar_path, $user_id);
    $stmt->execute();
}

// Update all fields
$stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, gender=?, age=?, address=? WHERE id=?");
$stmt->bind_param("sssssi", $fullname, $phone, $gender, $age, $address, $user_id);
$stmt->execute();

header("Location: dashboard_patient.php");
exit();
