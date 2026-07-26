<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Admin') ?> — Breaker Studio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="app-header">
  <div class="app-logo"><span class="mark"></span>BREAKER STUDIO — ADMIN</div>
  <div class="app-header-right">
    <span class="who"><?= e(current_admin_username()) ?></span>
    <a href="logout.php" class="btn btn-ghost btn-sm">Log out</a>
  </div>
</header>

<nav class="admin-subnav">
  <div class="container subnav-inner">
    <a href="dashboard.php" class="<?= $active_tab === 'accounts' ? 'current' : '' ?>">Accounts</a>
    <a href="games.php" class="<?= $active_tab === 'games' ? 'current' : '' ?>">Games</a>
    <a href="careers.php" class="<?= $active_tab === 'careers' ? 'current' : '' ?>">Careers</a>
  </div>
</nav>

<div class="container">
  <?php foreach (get_flashes() as $f): ?>
    <div class="flash <?= e($f['category']) ?>"><?= e($f['message']) ?></div>
  <?php endforeach; ?>
