<?php
/* ------------------------------------------------------
   Return the current logged‑in user profile as JSON
   Endpoint:   GET  api/modules/user/profile_get.php
-------------------------------------------------------*/
require_once __DIR__ . '/../../../../lib/Auth.php';
require_once __DIR__ . '/../../../../lib/Database.php';

header('Content-Type: application/json; charset=utf-8');

session_start();
if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = (int)$_SESSION['user']['id'];

$db = Database::get();

$sql = "SELECT 
            u.user_id,
            u.email,
            u.full_name,
            up.avatar_path,
            up.phone,
            up.address_line1,
            up.address_line2,
            up.city,
            up.state_region  AS state,
            up.postal_code,
            up.country,
            up.latitude,
            up.longitude
        FROM users u
        LEFT JOIN user_profiles up ON up.user_id = u.user_id
        WHERE u.user_id = :id
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->execute([':id' => $userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    http_response_code(404);
    echo json_encode(['error' => 'Profile not found']);
    exit;
}

/* ---------- small convenience: prepend slash for avatar ---------- */
if (!empty($profile['avatar_path']) && str_starts_with($profile['avatar_path'], 'images/')) {
    $profile['avatar_url'] = '/' . $profile['avatar_path'];
}

echo json_encode($profile);