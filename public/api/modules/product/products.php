<?php
// --- database helper ---
require_once __DIR__ . '/../../../../lib/Auth.php';
require_permission('product.fetch');

header('Content-Type: application/json; charset=utf-8');

// --- basic whitelist for GET params ---
$type      = $_GET['type']      ?? null;   // e.g. Supplement
$rx        = $_GET['rx']        ?? null;   // OTC / RX / CONTROLLED
$group     = $_GET['group']     ?? null;   // Pregnant / Children / ...
$sortPrice = ($_GET['sort'] ?? '') === 'price_asc' ? 'ASC'
          : (($_GET['sort'] ?? '') === 'price_desc' ? 'DESC' : null);

$params = [];
$sql = "
 SELECT p.product_id, p.sku, p.product_name, p.unit_price, p.rx_type, p.image_path,
        t.type_name,
        b.brand_name,
        c.country_name AS origin_country,
        GROUP_CONCAT(g.group_name SEPARATOR ',') AS patient_groups
 FROM products p
 LEFT JOIN product_types t         ON t.type_id  = p.type_id
 LEFT JOIN brands        b         ON b.brand_id = p.brand_id
 LEFT JOIN countries     c         ON c.country_id = p.origin_country
 LEFT JOIN product_patient_groups pg ON pg.product_id = p.product_id
 LEFT JOIN patient_groups g        ON g.group_id = pg.group_id
 WHERE p.is_active = 1
";

/* dynamic filters */
if ($type) { $sql .= " AND t.type_name = ?";  $params[] = $type; }
if ($rx)   { $sql .= " AND p.rx_type   = ?";  $params[] = $rx;   }
if ($group){ $sql .= " AND g.group_name = ?"; $params[] = $group; }

/* group & sort */
$sql .= " GROUP BY p.product_id";
if ($sortPrice) $sql .= " ORDER BY p.unit_price $sortPrice";
else            $sql .= " ORDER BY p.product_name";

$stmt = Database::get()->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
