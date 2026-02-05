<?php
// connect.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'cep_project'; // your DB name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // helpful for debugging — remove in production
    die("DB connect error: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
