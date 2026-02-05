<?php
// profile.php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: cep.html'); // or login page
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// reload fresh user data from DB
$stmt = $conn->prepare("SELECT username, email, fullname, phone, gender, age, address, blood_group, allergies, emergency_contact, avatar FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$flash_success = $_SESSION['profile_success'] ?? '';
$flash_error   = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Profile</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* Small overrides for profile layout */
    .profile-page { display:flex; gap:24px; padding:20px; }
    .profile-card { width:360px; background:#fff; padding:18px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); }
    .profile-edit { flex:1; background:#fff; padding:18px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.06); }
    .avatar-preview { width:96px; height:96px; border-radius:12px; overflow:hidden; display:inline-block; vertical-align:middle; margin-right:12px; }
    .avatar-preview img { width:100%; height:100%; object-fit:cover; display:block; }
    label{ display:block; margin-top:10px; font-weight:600; }
    input, select, textarea{ width:100%; padding:10px; border-radius:8px; border:1px solid #e6edf3; margin-top:6px; }
    .muted{ color:#6b7280; }
    .btn{ margin-top:12px; padding:10px 16px; background:linear-gradient(90deg,#5b57d9,#00b894); color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:700; }
    .small { padding:8px 12px; font-size:14px; border-radius:8px; }
    .message { padding:10px; border-radius:8px; margin-bottom:10px; }
    .success { background:#f0fff5; color:#0b8a41; }
    .error { background:#fff0f0; color:#b00020; }
  </style>
</head>
<body>

<header class="header">
  <h1>My Profile</h1>
  <p class="muted">View and update your profile details</p>
</header>

<div class="container" style="padding-top:20px;">
  <div class="profile-page" style="width:100%;">

    <!-- left: quick profile card -->
    <div class="profile-card">
      <?php if (!empty($user['avatar'])): ?>
        <div class="avatar-preview"><img src="<?= htmlspecialchars($user['avatar']) ?>" alt="avatar"></div>
      <?php else: ?>
        <div class="avatar-preview" style="background:linear-gradient(135deg,#5b57d9,#00b894); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:28px;">
          <?= strtoupper(substr($user['username'] ?? 'U',0,1)) ?>
        </div>
      <?php endif; ?>

      <h3 style="margin-top:10px;"><?= htmlspecialchars($user['fullname'] ?: $user['username']) ?></h3>
      <p class="muted"><?= htmlspecialchars($user['email']) ?></p>

      <div style="margin-top:12px;">
        <p><strong>Phone</strong><br><span class="muted"><?= htmlspecialchars($user['phone'] ?? '-') ?></span></p>
        <p><strong>Age</strong><br><span class="muted"><?= htmlspecialchars($user['age'] ?? '-') ?></span></p>
        <p><strong>Blood Group</strong><br><span class="muted"><?= htmlspecialchars($user['blood_group'] ?? '-') ?></span></p>
        <p><strong>Emergency</strong><br><span class="muted"><?= htmlspecialchars($user['emergency_contact'] ?? '-') ?></span></p>
      </div>

      <div style="margin-top:12px;">
        <a class="small" href="dashboard_patient.php" style="background:#eef6ff;color:#0056b3;text-decoration:none;">Back to Dashboard</a>
      </div>
    </div>

    <!-- right: editable form -->
    <div class="profile-edit">
      <?php if ($flash_success): ?><div class="message success"><?= htmlspecialchars($flash_success) ?></div><?php endif; ?>
      <?php if ($flash_error): ?><div class="message error"><?= htmlspecialchars($flash_error) ?></div><?php endif; ?>

      <form action="save_profile.php" method="post" enctype="multipart/form-data">
        <label>Full name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

        <label>Gender</label>
        <select name="gender">
          <option value="">--</option>
          <option <?= ($user['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
          <option <?= ($user['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
          <option <?= ($user['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
        </select>

        <label>Age</label>
        <input type="number" name="age" min="0" value="<?= htmlspecialchars($user['age'] ?? '') ?>">

        <label>Address</label>
        <textarea name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

        <label>Blood group</label>
        <input type="text" name="blood_group" value="<?= htmlspecialchars($user['blood_group'] ?? '') ?>">

        <label>Allergies (comma separated)</label>
        <input type="text" name="allergies" value="<?= htmlspecialchars($user['allergies'] ?? '') ?>">

        <label>Emergency contact</label>
        <input type="text" name="emergency_contact" value="<?= htmlspecialchars($user['emergency_contact'] ?? '') ?>">

        <label>Avatar (jpg/png, ≤ 2MB)</label>
        <input type="file" name="avatar" accept=".png,.jpg,.jpeg">

        <hr style="margin:16px 0;">

        <h4 style="margin:0 0 8px 0;">Change Password (optional)</h4>
        <label>Current password</label>
        <input type="password" name="current_password" placeholder="Leave blank to keep existing">

        <label>New password</label>
        <input type="password" name="new_password">

        <label>Confirm new password</label>
        <input type="password" name="confirm_password">

        <button class="btn" type="submit">Save profile</button>
      </form>
    </div>

  </div>
</div>

</body>
</html>
