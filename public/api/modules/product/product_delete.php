<?php
require_once __DIR__ . '/../../../../lib/Auth.php';
require_permission('product.delete');


require_once __DIR__ . '/../../../../lib/Database.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
if ($id<=0){
    http_response_code(400); echo '{"error":"bad id"}'; exit;
}
$db = Database::get();
$db->prepare('UPDATE products SET is_active = 0 WHERE product_id = ? LIMIT 1')
   ->execute([$id]);
echo '{"ok":1}';
