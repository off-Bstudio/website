<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin_id()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attempt_login($username, $password)) {
        redirect('dashboard.php');
    }
    flash('Incorrect username or password.', 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign in — Breaker Studio Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="login-screen">
  <div class="login-card">
    <div class="login-logo"><span class="mark"></span>BREAKER STUDIO</div>
    <p class="subtitle">Admin panel</p>

    <?php foreach (get_flashes() as $f): ?>
      <div class="flash <?= e($f['category']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign in</button>
    </form>

  </div>
</div>
</body>
</html>
