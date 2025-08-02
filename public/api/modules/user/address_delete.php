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
$addressId = intval($_POST['address_id'] ?? 0);

if (!$addressId) { http_response_code(400); echo json_encode(['error'=>'address_id required']); exit; }

$db = Database::get();
$del= $db->prepare("DELETE FROM user_addresses WHERE address_id=? AND user_id=?");
$del->execute([$addressId, $userId]);

echo json_encode(['ok'=> $del->rowCount() ? 1 : 0]);
