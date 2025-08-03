<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cfg = $cfg ?? require __DIR__ . '/../config/config.php';
$isLoggedIn = !empty($_SESSION['user']);
?>

<link rel="stylesheet" href="<?= $cfg['base_url'] ?>/css/navbar.css">

<nav class="navbar">
  <ul class="navbar-menu">
    <li><a href="<?= $cfg['base_url'] ?>/dashboard.php">Dashboard</a></li>
    <li><a href="<?= $cfg['base_url'] ?>/catalogue.php">Catalogue</a></li>

    <?php if ($isLoggedIn): ?>
      <li><a href="<?= $cfg['base_url'] ?>/profile.php">Profile</a></li>
    <?php endif; ?>

    <li class="spacer"></li>

    <?php if ($isLoggedIn): ?>
      <li><a href="<?= $cfg['base_url'] ?>/logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="<?= $cfg['base_url'] ?>/login.php">Login</a></li>
    <?php endif; ?>
  </ul>
</nav>
