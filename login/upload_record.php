<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: cep.html");
    exit();
}
$user_id = (int)$_SESSION['user_id'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Medical Record</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <header class="header">
    <h1>Upload Medical Record</h1>
  </header>

  <div class="container">
    <?php include 'sidebar_patient.php'; ?>

    <main class="main">
      <div class="card">
        <h3>Select file</h3>
        <form method="post" action="save_record.php" enctype="multipart/form-data">
          <input type="hidden" name="user_id" value="<?= $user_id ?>">
          <input type="file" name="record_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
          <button class="btn" type="submit">Upload</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
