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
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $user = UserModel::findByEmail($email);
    if ($user && hash_equals($user['password_hash'], $pass)) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'    => $user['user_id'],
            'email' => $user['email'],
            'name'  => $user['full_name'],
            'roles' => $user['roles'] ? explode(',', $user['roles']) : []
        ];
        header('Location: '.$cfg['base_url'].'/dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow p-4" style="width: 360px;">
      <h2 class="text-center mb-4">Login</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign in</button>
      </form>

      <p class="mt-3 text-center">
        <a href="<?= $cfg['base_url'] ?>/register.php">Create an account</a>
      </p>
    </div>
  </div>
</body>
</html>
