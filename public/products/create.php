<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Product</title>
  <link rel="stylesheet" href="../css/create.css">
</head>
<body>
  <div class="container">
    <h2>Create Product</h2>
    <form enctype="multipart/form-data" method="post" action="store.php">
      <label>SKU</label>
      <input name="sku" required>

      <label>Name</label>
      <input name="product_name" required>

      <label>Description</label>
      <textarea name="description" rows="3"></textarea>

      <label>Unit Price</label>
      <input type="number" name="unit_price" step="0.01" required>

      <label>
        <input type="checkbox" name="is_rx_only"> Prescription‑only
      </label>

      <label>Image</label>
      <input type="file" name="image">

      <button type="submit">Create</button>
      <a href="index.php" class="cancel-link">Cancel</a>
    </form>
  </div>
</body>
</html>
