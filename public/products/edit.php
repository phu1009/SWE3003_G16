<?php
session_start();
require_once __DIR__.'/../../app/Models/ProductModel.php';
$id = (int)($_GET['id']??0);
$product = ProductModel::find($id);
if(!$product){ echo "Not found"; exit; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit</title></head><body>
<h2>Edit product</h2>
<form enctype="multipart/form-data" method="post" action="update.php">
  <input type="hidden" name="id" value="<?=$product['product_id']?>">
  SKU<br><input name="sku" value="<?=htmlspecialchars($product['sku'])?>" required><br><br>
  Name<br><input name="product_name" value="<?=htmlspecialchars($product['product_name'])?>" required><br><br>
  Description<br><textarea name="description" rows="3"><?=htmlspecialchars($product['description'])?></textarea><br><br>
  Unit price<br><input type="number" step="0.01" name="unit_price"
            value="<?=htmlspecialchars($product['unit_price'])?>" required><br><br>
  <label><input type="checkbox" name="is_rx_only"
      <?= $product['is_rx_only']?'checked':'' ?>> Prescription‑only</label><br><br>
  Image<br><input type="file" name="image">
  <?php if($product['image_path']): ?>
    <br>Current: <img src="/lc-pms/public/<?=$product['image_path']?>" height="60">
  <?php endif; ?>
  <br><br>
  <button>Save</button> <a href="index.php">Back</a>
</form>
</body></html>
