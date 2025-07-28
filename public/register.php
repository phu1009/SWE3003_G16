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
            // auto-login
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
<!doctype html><html><head><meta charset="utf-8"><title>Sign up</title></head>
<body>
<h2>Register (Customer)</h2>
<?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <label>Full Name <input name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"></label><br>
    <label>Email <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label><br>
    <label>Password <input type="password" name="password" required></label><br>
    <label>Confirm Password <input type="password" name="password2" required></label><br>
    <button type="submit">Create Account</button>
</form>
<p><a href="<?= $cfg['base_url'] ?>/login.php">Back to login</a></p>
</body></html>
