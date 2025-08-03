<?php
session_start();
$cfg = require __DIR__ . '/../config/config.php';

if (empty($_SESSION['user'])) {
    header('Location: ' . $cfg['base_url'] . '/login.php');
    exit;
}

$user  = $_SESSION['user'];
$roles = $user['roles'] ?? [];
$roleText = $roles ? implode(', ', $roles) : 'user';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <div class="container">
    <h2 class="mt-4">Hello, <?= htmlspecialchars($roleText) ?>!</h2>
    <p>Welcome, <?= htmlspecialchars($user['name']) ?>.</p>
    <a href="<?= $cfg['base_url'] ?>/logout.php" class="btn btn-outline-danger">Logout</a>
  </div>
</body>
</html>
