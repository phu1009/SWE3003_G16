<?php
/* ------- RBAC & plumbing ------- */
require_once __DIR__ . '/../../../../lib/Auth.php';
require_permission('product.save');      // same privilege used for edits :contentReference[oaicite:3]{index=3}

require_once __DIR__ . '/../../../../lib/Database.php';
require_once __DIR__ . '/../../../../lib/FileUpload.php';   // helper that stores JPG/PNG files :contentReference[oaicite:4]{index=4}
header('Content-Type: application/json; charset=utf-8');

/* ------- validate input ------- */
$name  = trim($_POST['product_name'] ?? '');
$sku   = trim($_POST['sku'] ?? '');
$price = $_POST['unit_price'] ?? 0;
$rx    = $_POST['rx_type'] ?? 'OTC';

if ($name === '' || $sku === '' || !is_numeric($price)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']); exit;
}

/* ------- optional photo ------- */
$imagePath = null;
try {
    if (!empty($_FILES['photo']['tmp_name'])) {
        $imagePath = FileUpload::saveImage($_FILES['photo']);
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]); exit;
}

/* ------- insert row ------- */
$db = Database::get();
$db->beginTransaction();
try {
    $stmt = $db->prepare("
        INSERT INTO products (sku, product_name, unit_price, rx_type, image_path)
        VALUES (:s, :n, :p, :rx, :img)
    ");
    $stmt->execute([
        ':s'  => $sku,
        ':n'  => $name,
        ':p'  => $price,
        ':rx' => $rx,
        ':img'=> $imagePath
    ]);
    $db->commit();
    echo json_encode(['ok' => 1, 'product_id' => $db->lastInsertId()]);
} catch (Throwable $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
