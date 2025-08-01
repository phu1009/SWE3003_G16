<?php
session_start();
require_once __DIR__ . '/../../app/Models/ProductModel.php';
$products = ProductModel::all();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Product List</title>
  <link rel="stylesheet" href="../css/index.css">
</head>
<body>
  <div class="container">
    <h1>Product List</h1>
    <a class="btn-create" href="create.php">+ New</a>

    <table class="product-table">
      <thead>
        <tr>
          <th>Img</th>
          <th>SKU</th>
          <th>Name</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <?php if ($p['image_path']) echo '<img src="/lc-pms/public/'.htmlspecialchars($p['image_path']).'" height="40">'; ?>
          </td>
          <td><?= htmlspecialchars($p['sku']) ?></td>
          <td><?= htmlspecialchars($p['product_name']) ?></td>
          <td><?= number_format($p['unit_price'], 2) ?></td>
          <td>
            <a class="btn-edit" href="edit.php?id=<?= $p['product_id'] ?>">Edit</a>
            <form action="delete.php" method="post" class="form-inline"
                  onsubmit="return confirm('Delete?');">
              <input type="hidden" name="id" value="<?= $p['product_id'] ?>">
              <button class="btn-delete">Del</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
