<?php
require_once __DIR__ . '/Product.php';

class Cart
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // format: [product_id => quantity]
        }
    }

    // Add a product to the cart (increases quantity if already added)
    public function add($productId, $qty = 1)
    {
        $productId = (int)$productId;
        $qty = max(1, (int)$qty);

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $qty;
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
    }

    // Set an exact quantity for a product (used on the cart page +/- buttons)
    public function update($productId, $qty)
    {
        $productId = (int)$productId;
        $qty = (int)$qty;

        if ($qty <= 0) {
            $this->remove($productId);
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
    }

    // Remove one product completely
    public function remove($productId)
    {
        $productId = (int)$productId;
        unset($_SESSION['cart'][$productId]);
    }

    // Empty the whole cart (used after checkout)
    public function clear()
    {
        $_SESSION['cart'] = [];
    }

    // Raw [product_id => qty] array
    public function getItems()
    {
        return $_SESSION['cart'];
    }

    // Total number of items (for the cart badge in the header)
    public function getCount()
    {
        return array_sum($_SESSION['cart']);
    }

    // Full cart details with live product info (name, price, image, subtotal)
    public function getDetailedItems()
    {
        $productModel = new Product();
        $details = [];
        foreach ($_SESSION['cart'] as $productId => $qty) {
            $product = $productModel->getById($productId);
            if ($product) {
                $product['qty'] = $qty;
                $product['subtotal'] = $product['price'] * $qty;
                $details[] = $product;
            }
        }
        return $details;
    }

    // Grand total price of everything in the cart
    public function getGrandTotal()
    {
        $total = 0;
        foreach ($this->getDetailedItems() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }
}
