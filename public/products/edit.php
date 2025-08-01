<?php
session_start();
require_once __DIR__.'/../../app/Models/ProductModel.php';
$id = (int)($_GET['id']??0);
$product = ProductModel::find($id);
if(!$product){ echo "Not found"; exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Product</title>
  <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
  <div class="container">
    <h2>Edit Product</h2>
    <form enctype="multipart/form-data" method="post" action="update.php">
      <input type="hidden" name="id" value="<?=$product['product_id']?>">

      <label>SKU</label>
      <input name="sku" value="<?=htmlspecialchars($product['sku'])?>" required>

      <label>Name</label>
      <input name="product_name" value="<?=htmlspecialchars($product['product_name'])?>" required>

      <label>Description</label>
      <textarea name="description" rows="3"><?=htmlspecialchars($product['description'])?></textarea>

      <label>Unit price</label>
      <input type="number" step="0.01" name="unit_price" value="<?=htmlspecialchars($product['unit_price'])?>" required>

      <label>
        <input type="checkbox" name="is_rx_only" <?= $product['is_rx_only']?'checked':'' ?>>
        Prescription‑only
      </label>

      <label>Image</label>
      <input type="file" name="image">

      <?php if($product['image_path']): ?>
        <p>Current:</p>
        <img src="/lc-pms/public/<?=$product['image_path']?>" height="60">
      <?php endif; ?>

      <button type="submit">Save</button>
      <a href="index.php" class="back-link">Back</a>
    </form>
  </div>
</body>
</html>
