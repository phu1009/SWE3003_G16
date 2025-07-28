<?php
session_start();
$_SESSION = [];
session_destroy();
$cfg = require __DIR__ . '/../config/config.php';
header('Location: '.$cfg['base_url'].'/login.php');
exit;
