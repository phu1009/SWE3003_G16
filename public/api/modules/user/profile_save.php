<?php
/* ------------------------------------------------------
   Save the logged‑in user's profile (avatar + contact).
   Endpoint: POST  api/modules/user/profile_save.php
-------------------------------------------------------*/
$root = dirname(__DIR__, 4);               // …/SWE3003_G16
require_once "$root/lib/Database.php";   // Auth.php not a class – we rely on $_SESSION
require_once "$root/lib/FileUpload.php";

header('Content-Type: application/json; charset=utf-8');

session_start();
if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$userId = (int) $_SESSION['user']['id'];

/* ---------- collect POST fields ---------- */
$input = [
    'email'         => trim($_POST['email']          ?? ''),
    'phone'         => trim($_POST['phone']          ?? ''),
    'address_line1' => trim($_POST['address_line1']  ?? ''),
    'address_line2' => trim($_POST['address_line2']  ?? ''),
    'city'          => trim($_POST['city']           ?? ''),
    'state_region'  => trim($_POST['state']          ?? ''),
    'postal_code'   => trim($_POST['postal_code']    ?? ''),
    'country'       => trim($_POST['country']        ?? 'Vietnam'),
    'latitude'      => $_POST['latitude']  !== '' ? $_POST['latitude']  : null,
    'longitude'     => $_POST['longitude'] !== '' ? $_POST['longitude'] : null,
];

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

/* ---------- handle avatar upload (optional) ---------- */
$avatarRelPath = null;
if (!empty($_FILES['avatar']['tmp_name'])) {
    try {
        $avatarRelPath = FileUpload::saveImage(
            $_FILES['avatar'],
            $root . '/public/images/avatars'      // absolute destination folder
        );
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

$db = Database::get();
$db->beginTransaction();
try {
    /* 1 ▪ update email in users table */
    $db->prepare('UPDATE users SET email = ? WHERE user_id = ?')
       ->execute([$input['email'], $userId]);

    /* 2 ▪ ensure a row exists in user_profiles */
    $db->prepare('INSERT IGNORE INTO user_profiles (user_id) VALUES (?)')
       ->execute([$userId]);

    /* 3 ▪ build dynamic UPDATE, skip avatar if not provided */
    $fields = [
        'phone', 'address_line1', 'address_line2', 'city', 'state_region',
        'postal_code', 'country', 'latitude', 'longitude'
    ];
    $set = [];
    $params = [];
    foreach ($fields as $f) {
        $set[] = "$f = :$f";
        $params[":$f"] = $input[$f];
    }
    if ($avatarRelPath !== null) {
        $set[] = 'avatar_path = :avatar';
        $params[':avatar'] = $avatarRelPath;
    }
    $params[':uid'] = $userId;

    $sql = 'UPDATE user_profiles SET ' . implode(', ', $set) . ' WHERE user_id = :uid';
    $db->prepare($sql)->execute($params);

    $db->commit();
    echo json_encode(['ok' => 1]);
} catch (Throwable $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
