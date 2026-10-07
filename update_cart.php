<?php
header('Content-Type: application/json');

require_once __DIR__ . '/classes/Cart.php';

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$cart = new Cart();
$cart->update($productId, $qty);

echo json_encode([
    'success' => true,
    'cartCount' => $cart->getCount()
]);
