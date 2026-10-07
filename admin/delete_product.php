<?php
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Product.php';
Admin::requireLogin();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId > 0) {
    $productModel = new Product();
    $productModel->delete($productId);
}

header('Location: products.php?deleted=1');
exit;
