<?php
session_start();
require_once __DIR__ . '/../app/Models/UserModel.php';
$cfg = require __DIR__ . '/../config/config.php';

if (!empty($_SESSION['user'])) {
    header('Location: '.$cfg['base_url'].'/dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $pass1    = $_POST['password'] ?? '';
    $pass2    = $_POST['password2'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email.";
    } elseif ($pass1 !== $pass2 || strlen($pass1) < 6) {
        $error = "Passwords must match and be ≥ 6 chars.";
    } elseif (empty($fullName)) {
        $error = "Full name is required.";
    } else {
        $ok = UserModel::createCustomer($email, $pass1, $fullName);
        if ($ok) {
            $user = UserModel::findByEmail($email);
            $_SESSION['user'] = [
                'id'    => $user['user_id'],
                'email' => $user['email'],
                'name'  => $user['full_name'],
                'roles' => $user['roles'] ? explode(',', $user['roles']) : []
            ];
            header('Location: '.$cfg['base_url'].'/dashboard.php');
            exit;
        } else {
            $error = "Could not create account (email may exist).";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign up</title>
  <link rel="stylesheet" href="<?= $cfg['base_url'] ?>/css/register.css">
</head>
<body>
  <div class="register-container">
    <h2>Register (Customer)</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input id="full_name" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
      </div>

      <div class="form-group">
        <label for="password2">Re-enter Password</label>
        <input id="password2" type="password" name="password2" required>
      </div>

      <button type="submit">Create Account</button>
    </form>

    <p><a href="<?= $cfg['base_url'] ?>/login.php">Back to login</a></p>
  </div>
</body>
</html>
