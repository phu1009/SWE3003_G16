<?php
/*
 *  Add a NEW extra address (max 4) for the logged-in user
 *  ------------------------------------------------------
 *  Expected POST  ────────────────────────────────────────
 *    address_name      string   (required, shown in UI)
 *    address_line1     string   (required)
 *    address_line2     string   (optional)
 *    city, state, postal_code, country
 *    latitude, longitude         (optional)
 *    is_default        0/1       (optional)  – if 1, clears other defaults
 *
 *  Returns JSON:
 *    { "ok":1, "address_id": 17 }           on success
 *    { "error":"...", ... }                 on failure
 */

$root = dirname(__DIR__, 4);                 // …/public
require_once "$root/lib/Auth.php";
require_once "$root/lib/Database.php";

header('Content-Type: application/json; charset=utf-8');

session_start();
if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$userId = (int)$_SESSION['user']['id'];

/* ---- (optional) RBAC check ------------------------------------------ */
if (function_exists('require_permission')) {
    //  change the permission code to whatever your ACL uses
    require_permission('profile.edit');
}

/* ---- validate basic fields ----------------------------------------- */
$line1 = trim($_POST['address_line1'] ?? '');
if ($line1 === '') {
    http_response_code(400);
    echo json_encode(['error' => 'address_line1 required']);
    exit;
}

$addrName = trim($_POST['address_name'] ?? '');
if ($addrName === '') $addrName = 'Extra';

/* ---- enforce max-4 extra addresses --------------------------------- */
$db   = Database::get();
$cntQ = $db->prepare('SELECT COUNT(*) FROM user_addresses WHERE user_id = ?');
$cntQ->execute([$userId]);
if ($cntQ->fetchColumn() >= 4) {
    http_response_code(409);
    echo json_encode(['error' => 'Address limit (max 4) reached']);
    exit;
}

/* ---- build INSERT params ------------------------------------------- */
$params = [
    ':uid'      => $userId,
    ':name'     => $addrName,
    ':l1'       => $line1,
    ':l2'       => trim($_POST['address_line2'] ?? ''),
    ':city'     => trim($_POST['city'] ?? ''),
    ':state'    => trim($_POST['state'] ?? ''),
    ':zip'      => trim($_POST['postal_code'] ?? ''),
    ':country'  => trim($_POST['country'] ?? 'Vietnam'),
    ':lat'      => ($_POST['latitude']  !== '') ? $_POST['latitude']  : null,
    ':lng'      => ($_POST['longitude'] !== '') ? $_POST['longitude'] : null,
    ':def'      => (isset($_POST['is_default']) && $_POST['is_default']) ? 1 : 0,
];

/* ---- if this one is default, clear previous defaults --------------- */
if ($params[':def']) {
    $db->prepare('UPDATE user_addresses SET is_default = 0 WHERE user_id = ?')
       ->execute([$userId]);
}

/* ---- insert row ---------------------------------------------------- */
$sql = "INSERT INTO user_addresses
          (user_id, address_name, address_line1, address_line2,
           city, state_region, postal_code, country,
           latitude, longitude, is_default)
        VALUES
          (:uid, :name, :l1, :l2,
           :city, :state, :zip, :country,
           :lat, :lng, :def)";

$db->prepare($sql)->execute($params);

echo json_encode(['ok' => 1, 'address_id' => $db->lastInsertId()]);
