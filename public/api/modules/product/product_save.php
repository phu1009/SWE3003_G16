<?php
/*  POST multipart: avatar + contact + main address  */
$root = dirname(__DIR__, 4);                          // …/SWE3003_G16
require_once "$root/lib/Auth.php";
require_once "$root/lib/Database.php";
require_once "$root/lib/FileUpload.php";

header('Content-Type: application/json; charset=utf-8');

session_start();
if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$userId = (int)$_SESSION['user']['id'];

/* ---------- collect & validate ---------- */
$email          = trim($_POST['email']          ?? '');
$phone          = trim($_POST['phone']          ?? '');
$address_line1  = trim($_POST['address_line1']  ?? '');
$address_line2  = trim($_POST['address_line2']  ?? '');
$city           = trim($_POST['city']           ?? '');
$state          = trim($_POST['state']          ?? '');
$postal_code    = trim($_POST['postal_code']    ?? '');
$country        = trim($_POST['country']        ?? 'Vietnam');
$lat            = $_POST['latitude']  ?? null;
$lng            = $_POST['longitude'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

/* ---------- avatar (optional) ---------- */
$avatarPath = null;
if (!empty($_FILES['avatar']['tmp_name'])) {
    try {
        $avatarDir  = $root . '/public/images/avatars';   // absolute
        $avatarPath = FileUpload::saveImage($_FILES['avatar'], $avatarDir);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

$db = Database::get();
$db->beginTransaction();

try {
    /* 1. update email in users table */
    $db->prepare('UPDATE users SET email = ? WHERE user_id = ?')
       ->execute([$email, $userId]);

    /* 2. insert / update profile */
    $sql = "
        INSERT INTO user_profiles
              (user_id, avatar_path, phone, address_line1, address_line2,
               city, state_region, postal_code, country, latitude, longitude)
        VALUES (:id, :avatar, :phone, :l1, :l2, :city, :state, :zip,
                :country, :lat, :lng)
        ON DUPLICATE KEY UPDATE
              avatar_path   = IFNULL(:avatar_up, avatar_path),
              phone         = :phone,
              address_line1 = :l1,
              address_line2 = :l2,
              city          = :city,
              state_region  = :state,
              postal_code   = :zip,
              country       = :country,
              latitude      = :lat,
              longitude     = :lng";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id'        => $userId,
        ':avatar'    => $avatarPath,
        ':avatar_up' => $avatarPath,        // reused in UPDATE part
        ':phone'     => $phone,
        ':l1'        => $address_line1,
        ':l2'        => $address_line2,
        ':city'      => $city,
        ':state'     => $state,
        ':zip'       => $postal_code,
        ':country'   => $country,
        ':lat'       => $lat,
        ':lng'       => $lng
    ]);

    $db->commit();
    echo json_encode(['ok' => 1]);
} catch (Throwable $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
