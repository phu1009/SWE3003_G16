<?php session_start(); ?>
<!doctype html><html><head><meta charset="utf-8"><title>New product</title></head><body>
<h2>Create product</h2>
<form enctype="multipart/form-data" method="post" action="store.php">
  SKU<br><input name="sku" required><br><br>
  Name<br><input name="product_name" required><br><br>
  Description<br><textarea name="description" rows="3"></textarea><br><br>
  Unit price<br><input type="number" name="unit_price" step="0.01" required><br><br>
  <label><input type="checkbox" name="is_rx_only"> Prescription‑only</label><br><br>
  Image<br><input type="file" name="image"><br><br>
  <button>Create</button> <a href="index.php">Cancel</a>
</form>
</body></html>
