<?php
session_start();
require_once "connect.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['upload'])) {
    die("Open the upload form and submit it.");
}

$user_id = (int) $_SESSION['user_id'];

if (!isset($_FILES['medical_file']) || $_FILES['medical_file']['error'] !== UPLOAD_ERR_OK) {
    die("File upload error.");
}

// basic validations
$allowed = ['pdf','jpg','jpeg','png','doc','docx'];
$maxSize = 5 * 1024 * 1024; // 5 MB

$origName = $_FILES['medical_file']['name'];
$size = $_FILES['medical_file']['size'];
$tmp = $_FILES['medical_file']['tmp_name'];
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("File type not allowed.");
}
if ($size > $maxSize) {
    die("File too large (max 5MB).");
}

// ensure uploads directory exists
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// generate unique filename
$base = bin2hex(random_bytes(8));
$filename = $base . '.' . $ext;
$destPath = $uploadDir . '/' . $filename;

// move uploaded file
if (!move_uploaded_file($tmp, $destPath)) {
    die("Failed to save file.");
}

// store record in DB (store relative path)
$relativePath = 'uploads/' . $filename;
$description = trim($_POST['description'] ?? '');

$stmt = $conn->prepare("INSERT INTO medical_records (user_id, file_name, file_path, description, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("isss", $user_id, $origName, $relativePath, $description);

if ($stmt->execute()) {
    header("Location: dashboard_patient.php");
    exit();
} else {
    echo "DB error: " . htmlspecialchars($stmt->error);
}
$stmt->close();
$conn->close();
?>
