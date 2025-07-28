<?php
session_start();
require_once __DIR__.'/../../app/Models/ProductModel.php';
$products = ProductModel::all();
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Products</title></head><body>
<h1>Products</h1>
<a href="create.php">+ New</a>
<table border="1" cellpadding="6">
<thead><tr><th>Img</th><th>SKU</th><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($products as $p): ?>
<tr>
<td><?php if($p['image_path']) echo '<img src="/lc-pms/public/'.$p['image_path'].'" height="40">'; ?></td>
<td><?=htmlspecialchars($p['sku'])?></td>
<td><?=htmlspecialchars($p['product_name'])?></td>
<td><?=number_format($p['unit_price'],2)?></td>
<td>
  <a href="edit.php?id=<?=$p['product_id']?>">Edit</a>
  <form action="delete.php" method="post" style="display:inline"
        onsubmit="return confirm('Delete?');">
    <input type="hidden" name="id" value="<?=$p['product_id']?>">
    <button>Del</button>
  </form>
</td>
</tr>
<?php endforeach;?>
</tbody></table>
</body></html>
