<?php
// debug save_user.php — overwrite the file while debugging
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<h3>save_user.php</h3>";
    echo "<p>This page expects a POST from register.php. Open the registration form and submit it (do not open this file directly).</p>";
    echo "<p><a href='register.php'>Open registration form</a></p>";
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if ($fullname === '' || $username === '' || $email === '' || $password === '') {
    echo "<p style='color:red'>Error: please fill required fields.</p>";
    echo "<p><a href='register.php'>Back to register</a></p>";
    exit;
}

// hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// prepared statement
$stmt = $conn->prepare("INSERT INTO users (fullname, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo "<p style='color:red'>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
    exit;
}
$stmt->bind_param("sssss", $fullname, $username, $email, $phone, $passwordHash);

if ($stmt->execute()) {
    echo "<p style='color:green'>Registration successful. Redirecting to choose_profile.php...</p>";
    // debug: show inserted id
    echo "<p>Inserted user id: " . (int)$stmt->insert_id . "</p>";
    // redirect after short delay so you can read messages
    header("Refresh:2; url=choose_profile.php");
    exit;
} else {
    echo "<p style='color:red'>Execute failed: " . htmlspecialchars($stmt->error) . "</p>";
    // duplicate key handling (1062)
    if ($conn->errno === 1062) {
        echo "<p style='color:orange'>Duplicate entry — username or email already exists.</p>";
    }
}
$stmt->close();
$conn->close();
?>
