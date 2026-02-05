<?php
// save_transaction.php
session_start();
require_once 'connect.php';

// require login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['tx_error'] = "Please login first.";
    header("Location: cep.html");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard_patient.php");
    exit();
}

$amount_raw = trim($_POST['amount'] ?? '');
$description = trim($_POST['description'] ?? '');

// normalize decimal separators and validate numeric
$amount_raw = str_replace(',', '.', $amount_raw);
if ($amount_raw === '' || !is_numeric($amount_raw)) {
    $_SESSION['tx_error'] = 'Please enter a valid numeric amount.';
    header('Location: dashboard_patient.php');
    exit();
}
$amount = round((float)$amount_raw, 2);

// insert
$stmt = $conn->prepare("INSERT INTO transactions (user_id, amount, description) VALUES (?, ?, ?)");
if (!$stmt) {
    $_SESSION['tx_error'] = "DB prepare error: " . $conn->error;
    header("Location: dashboard_patient.php");
    exit();
}
$stmt->bind_param("ids", $user_id, $amount, $description);

if ($stmt->execute()) {
    $_SESSION['tx_success'] = "Transaction added.";
} else {
    $_SESSION['tx_error'] = "Failed to save transaction: " . $stmt->error;
}
$stmt->close();

header("Location: dashboard_patient.php");
exit();
