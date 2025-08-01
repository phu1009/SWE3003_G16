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
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="<?= $cfg['base_url'] ?>/css/dashboard.css">
</head>
<body>
  <div class="container">
    <h2>Hello, <?= htmlspecialchars($roleText) ?>!</h2>
    <p>Welcome, <?= htmlspecialchars($user['name'] ?? $user['email']) ?>.</p>
    <a href="<?= $cfg['base_url'] ?>/logout.php">Logout</a>
  </div>
</body>
</html>
