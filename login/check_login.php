<?php
session_start();
require_once 'connect.php';

// show errors while debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';
    $loginType       = $_POST['login_type'] ?? ''; // "hospital" when coming from hospital form

    if ($usernameOrEmail === '' || $password === '') {
        echo "<p style='color:red'>Please enter username/email and password. <a href='cep.html'>Back</a></p>";
        exit;
    }

    // lookup user by username OR email
    $stmt = $conn->prepare("SELECT id, fullname, username, email, password FROM users WHERE username = ? OR email = ? LIMIT 1");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // set session
            $_SESSION['user_id']  = (int)$row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['fullname'] = $row['fullname'];

            // If the form explicitly said hospital, go to hospital dashboard
            if ($loginType === 'hospital') {
                header("Location: hospital_dashboard.php");
                exit();
            }

            // Default: patient dashboard
            header("Location: dashboard_patient.php");
            exit();
        } else {
            echo "<h2 style='color:red;'>Wrong username/email or password!</h2>";
            echo "<p><a href='cep.html'>Try again</a></p>";
        }
    } else {
        echo "<h2 style='color:red;'>Wrong username/email or password!</h2>";
        echo "<p><a href='cep.html'>Try again</a></p>";
    }

    $stmt->close();
}
$conn->close();
?>
