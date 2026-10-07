<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Product.php';
class Order
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    /**
     * Places a new order.
     * $paymentMethod: 'COD' ya 'STRIPE'
     * $paymentStatus: 'PENDING' ya 'PAID'
     */
    public function place($customerName, $phone, $address, $cartItems, $total, $paymentMethod = 'COD', $paymentStatus = 'PENDING')
    {
        if (empty($cartItems)) {
            return false;
        }
        try {
            $this->conn->beginTransaction();
            // Insert Query with payment_method and payment_status
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_name, phone, address, total, status, payment_method, payment_status, created_at) 
                 VALUES (?, ?, ?, ?, 'Placed', ?, ?, NOW())"
            );
            $stmt->execute([$customerName, $phone, $address, $total, $paymentMethod, $paymentStatus]);
            $orderId = $this->conn->lastInsertId();
            $itemStmt = $this->conn->prepare(
                "INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)"
            );
            $productModel = new Product();
            foreach ($cartItems as $item) {
                // Check if 'qty' or 'quantity' key exists in array
                $qty = $item['qty'] ?? $item['quantity'] ?? 1;
                
                $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $qty]);
                $productModel->reduceStock($item['id'], $qty);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    /**
     * Updates payment status and Stripe ID after payment success
     */
    public function updatePaymentStatus($orderId, $paymentStatus, $stripePaymentId = null)
    {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE orders SET payment_status = ?, stripe_payment_id = ? WHERE id = ?"
            );
            return $stmt->execute([$paymentStatus, $stripePaymentId, $orderId]);
        } catch (Exception $e) {
            return false;
        }
    }
    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function getItems($orderId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    public function updateStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}