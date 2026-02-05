<?php
// register.php - simple register form that matches dashboard theme
// place in same folder as register.css and ensure connect.php exists if you want to actually save

// If you already have server-side registration logic, adapt action to your save_user.php
// This file demonstrates the frontend and simple POST submission.

session_start();
$flash_error = $_SESSION['register_error'] ?? '';
$flash_success = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_error'], $_SESSION['register_success']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register — Health Record Management System</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="register.css">
</head>
<body>

<header class="site-header">
  <h1>Medical Record Management System</h1>
  <p>Digitizing Health Records Security</p>
</header>

<main class="main-wrap">
  <section class="register-card">
    <?php if($flash_error): ?>
      <div class="alert error"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>
    <?php if($flash_success): ?>
      <div class="alert success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>

    <div class="register-grid">
      <!-- left: form -->
      <div>
        <h2 style="margin:0 0 12px 0;">Register</h2>

        <!-- Change "save_user.php" to the path you use to handle registration -->
        <form class="register-form" method="post" action="save_user.php" enctype="multipart/form-data">
          <label for="fullname">Full Name</label>
          <input id="fullname" name="fullname" type="text" required placeholder="e.g. Pratyusha Survase">

          <label for="username">Username</label>
          <input id="username" name="username" type="text" required placeholder="Choose username">

          <div class="row-inline" style="margin-top:8px;">
            <div class="col">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" placeholder="you@example.com">
            </div>
            <div class="col">
              <label for="phone">Phone</label>
              <input id="phone" name="phone" type="tel" placeholder="+91...">
            </div>
          </div>

          <label for="password" style="margin-top:12px;">Password</label>
          <input id="password" name="password" type="password" required placeholder="Choose a password">

          <div class="form-actions">
            <button class="btn-primary" type="submit">Register</button>
            <a class="btn-secondary" href="cep.html">Back</a>
          </div>

          <div class="form-footer">
            Already have an account? <a href="login.php">Sign in</a>
          </div>
        </form>
      </div>

      <!-- right column (optional info, keep empty or add image/notes) -->
      <aside style="padding-left:12px;">
        <div style="background:#f7fbff;border-radius:8px;padding:16px;box-shadow:inset 0 1px 0 rgba(255,255,255,0.6);">
          <h3 style="margin-top:0;">Why register?</h3>
          <p class="helper">Register to book appointments, upload medical records and access your personal dashboard.</p>
        </div>
      </aside>
    </div>
  </section>
</main>

</body>
</html>
