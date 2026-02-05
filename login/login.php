<?php
// login_patient.php
session_start();
if(isset($_SESSION['user_id'])) header('Location: dashboard_patient.php');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Patient Login</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
  <div style="flex:1; display:flex; justify-content:center; padding:30px;">
    <div class="form-card">
      <h2>Login</h2>
      <form method="post" action="check_login.php">
        <label>Username or Email</label>
        <input type="text" name="username" required placeholder="Enter username or email">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter password">
        <button class="btn" type="submit" name="login">Login</button>
      </form>
      <p style="margin-top:12px;">Don't have an account? <a href="register.php">Sign Up</a></p>
    </div>
  </div>
</div>
</body>
</html>
