<?php
session_start();
require_once __DIR__.'/../../app/Models/ProductModel.php';

$id = (int)$_POST['id'];
ProductModel::delete($id);          // now hides instead of hard‑deletes
header('Location: index.php');
