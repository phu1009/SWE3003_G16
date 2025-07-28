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
<!doctype html><html><head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>
<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <label>Email <input name="email" type="email" required></label><br>
    <label>Password <input name="password" type="password" required></label><br>
    <button type="submit">Sign in</button>
</form>
<p><a href="<?= $cfg['base_url'] ?>/register.php">Create an account</a></p>
</body></html>
