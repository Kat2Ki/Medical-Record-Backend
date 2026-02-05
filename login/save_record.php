<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}

if (isset($_POST['upload'])) {

    $user_id = $_SESSION['user_id'];

    // Folder for uploads
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['record_file'];
    $fileName = time() . "_" . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    // Move file to uploads folder
    if (move_uploaded_file($file['tmp_name'], $filePath)) {

        $stmt = $conn->prepare("
            INSERT INTO medical_records (user_id, file_name, file_path)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $user_id, $fileName, $filePath);
        $stmt->execute();

        header("Location: dashboard_patient.php");
        exit();

    } else {
        echo "File upload failed.";
    }
}
?>
