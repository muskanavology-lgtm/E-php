<?php
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../includes/functions.php';

if (Admin::isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $admin = new Admin();
    if ($admin->login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:#f1f3f6;">

<div class="form-box" style="margin-top:80px;">
  <h2>🔐 Admin Login</h2>

  <?php if ($error): ?>
    <div class="alert alert-error"><?php echo h($error); ?></div>
  <?php endif; ?>
  <?php if (isset($_GET['loggedout'])): ?>
    <div class="alert alert-success">You have been logged out.</div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn-primary">Login</button>
  </form>
  <p style="text-align:center; font-size:13px; color:#7e808c; margin-top:14px;">
    First time? Run <a href="../setup.php">setup.php</a> to create the default admin account.
  </p>
</div>

</body>
</html>
