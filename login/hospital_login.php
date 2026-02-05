<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Hospital Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- use your dashboard.css (must exist in same folder or change path) -->
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <main style="display:flex;justify-content:center;padding:40px;">
    <div style="background:#fff;padding:28px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.08);width:360px;">
      <h2>Hospital Sign In</h2>
      <form method="post" action="check_login.php">
        <label>Username or Email</label>
        <input type="text" name="username" placeholder="Enter username or email" required style="width:100%;padding:8px;margin:8px 0;border-radius:6px;border:1px solid #ddd;">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required style="width:100%;padding:8px;margin:8px 0;border-radius:6px;border:1px solid #ddd;">

        <!-- IMPORTANT: tells the login handler this is a hospital login -->
        <input type="hidden" name="login_type" value="hospital">

        <button type="submit" name="login" style="width:100%;padding:10px;background:#00b894;color:#fff;border:none;border-radius:6px;margin-top:10px;cursor:pointer;">Login</button>
      </form>
    </div>
  </main>
</body>
</html>
