<?php
session_start();
require_once __DIR__.'/../../lib/FileUpload.php';
require_once __DIR__.'/../../app/Models/ProductModel.php';

$imagePath=null;
try { $imagePath = FileUpload::saveImage($_FILES['image']??[]); }
catch(Throwable $e){ /* could store message */ }

ProductModel::create([
    'sku'          => $_POST['sku'],
    'product_name' => $_POST['product_name'],
    'description'  => $_POST['description'],
    'unit_price'   => (float)$_POST['unit_price'],
    'is_rx_only'   => isset($_POST['is_rx_only'])?1:0,
    'image_path'   => $imagePath
]);
header('Location: index.php');
