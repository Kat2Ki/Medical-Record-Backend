<?php
// first_time_setup.php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: cep.html');
    exit();
}

$uid = (int)$_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // example fields: fullname (you can expand)
    $fullname = trim($_POST['fullname'] ?? '');
    if ($fullname === '') {
        $message = 'Please enter your full name.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, is_first_login = 0 WHERE id = ?");
        $stmt->bind_param("si", $fullname, $uid);
        if ($stmt->execute()) {
            $_SESSION['fullname'] = $fullname;
            $stmt->close();
            header('Location: dashboard_patient.php');
            exit();
        } else {
            $message = 'Error saving data.';
        }
    }
}

// fetch current info
$stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>First Time Setup</title>
  <link rel="stylesheet" href="cep.css">
  <style>
    .setup{ max-width:600px; margin:40px auto; background:#fff; padding:24px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.08); }
    .setup input{ width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ddd; }
    .setup button{ padding:10px 16px; background:#00b894; color:#fff; border:none; border-radius:8px; cursor:pointer; }
  </style>
</head>
<body>
  <div class="setup">
    <h2>Welcome — complete your profile</h2>
    <?php if ($message) echo '<p style="color:red">'.htmlspecialchars($message).'</p>'; ?>
    <form method="post">
      <label>Full name</label>
      <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
      <!-- add more fields if needed -->
      <button type="submit" name="save">Save & Continue</button>
    </form>
  </div>
</body>
</html>
