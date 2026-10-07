<?php
header('Content-Type: application/json');

require_once __DIR__ . '/classes/Cart.php';
require_once __DIR__ . '/classes/Product.php';

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($productId <= 0 || $qty <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$productModel = new Product();
$product = $productModel->getById($productId);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

if ($product['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'This product is out of stock.']);
    exit;
}

$cart = new Cart();
$cart->add($productId, $qty);

echo json_encode([
    'success' => true,
    'cartCount' => $cart->getCount()
]);
