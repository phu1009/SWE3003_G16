
<?php
require_once __DIR__ . '/../../../../lib/Auth.php';
require_permission('product.save');

require_once __DIR__ . '/../../../../lib/Database.php';

header('Content-Type: application/json; charset=utf-8');

$db = Database::get();
$id = (int)($_POST['product_id'] ?? 0);
$name = trim($_POST['product_name'] ?? '');
$price = $_POST['unit_price'] ?? 0;
$rx    = $_POST['rx_type'] ?? 'OTC';

if ($id <= 0 || $name === '') {
    http_response_code(400);
    echo json_encode(['error'=>'Bad request']); exit;
}

$db->beginTransaction();
try {
    $extraSql = '';
    if (!empty($_FILES['photo']['tmp_name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $file = uniqid().'_'.time().'.'.$ext;
        move_uploaded_file($_FILES['photo']['tmp_name'],
            __DIR__.'/../../uploads/products/'.$file);
        $extraSql = ', image_path = :img';
    }

    $sql = "UPDATE products
              SET product_name = :n,
                  unit_price    = :p,
                  rx_type       = :rx
                  $extraSql
            WHERE product_id = :id LIMIT 1";
    $st = $db->prepare($sql);
    $st->bindValue(':n', $name);
    $st->bindValue(':p', $price);
    $st->bindValue(':rx', $rx);
    $st->bindValue(':id', $id, PDO::PARAM_INT);
    if ($extraSql) $st->bindValue(':img', $file);
    $st->execute();

    $db->commit();
    echo json_encode(['ok'=>1]);
} catch(Throwable $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
