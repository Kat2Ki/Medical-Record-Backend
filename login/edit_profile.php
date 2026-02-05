<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<link rel="stylesheet" href="dashboard.css">
</head>

<body>

<div class="edit-container">
    <h2>Edit Profile</h2>

    <form action="save_profile.php" method="POST" enctype="multipart/form-data">
        
        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= $user['fullname'] ?>">

        <label>Phone</label>
        <input type="text" name="phone" value="<?= $user['phone'] ?>">

        <label>Gender</label>
        <select name="gender">
            <option <?= $user['gender']=="Male"?"selected":"" ?>>Male</option>
            <option <?= $user['gender']=="Female"?"selected":"" ?>>Female</option>
            <option <?= $user['gender']=="Other"?"selected":"" ?>>Other</option>
        </select>

        <label>Age</label>
        <input type="number" name="age" value="<?= $user['age'] ?>">

        <label>Address</label>
        <input type="text" name="address" value="<?= $user['address'] ?>">

        <label>Profile Picture</label>
        <input type="file" name="avatar">

        <button type="submit" class="btn">Save Changes</button>
    </form>

    <a href="dashboard_patient.php" class="back-btn">← Back</a>
</div>

</body>
</html>
