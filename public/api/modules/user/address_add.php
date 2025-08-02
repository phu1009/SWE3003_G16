<?php
require_once __DIR__ . '/../../../../lib/Auth.php';
require_once __DIR__ . '/../../../../lib/Database.php';

header('Content-Type: application/json; charset=utf-8');

session_start();

if (empty($_SESSION['user']['id'])) {        // not logged in
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$userId = intval($_SESSION['user']['id']);

$db     = Database::get();

/* basic validation */
$line1 = trim($_POST['address_line1'] ?? '');
if ($line1 === '') { http_response_code(400); echo json_encode(['error'=>'address_line1 required']); exit; }

$params = [
    ':uid'  => $userId,
    ':lab'  => $_POST['label']         ?? 'other',
    ':l1'   => $line1,
    ':l2'   => $_POST['address_line2'] ?? '',
    ':city' => $_POST['city']          ?? '',
    ':state'=> $_POST['state']         ?? '',
    ':zip'  => $_POST['postal_code']   ?? '',
    ':country'=>$_POST['country']      ?? 'Vietnam',
    ':lat'  => $_POST['latitude']      ?: null,
    ':lng'  => $_POST['longitude']     ?: null,
    ':def'  => $_POST['is_default']    ? 1 : 0
];

if ($params[':def']) {
    /* unset other defaults */
    $db->prepare("UPDATE user_addresses SET is_default=0 WHERE user_id=?")
       ->execute([$userId]);
}

$sql = "INSERT INTO user_addresses
          (user_id,label,address_line1,address_line2,city,state_region,postal_code,
           country,latitude,longitude,is_default)
        VALUES
          (:uid,:lab,:l1,:l2,:city,:state,:zip,:country,:lat,:lng,:def)";
$db->prepare($sql)->execute($params);

echo json_encode(['ok'=>1,'address_id'=>$db->lastInsertId()]);
