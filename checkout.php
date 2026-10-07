<?php
require_once __DIR__ . '/vendor/autoload.php'; // Composer SDK Autoload
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Cart.php';
require_once __DIR__ . '/classes/Order.php';

// Stripe Secret Key Init
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$cart = new Cart();
$items = $cart->getDetailedItems();
$grandTotal = $cart->getGrandTotal();
$deliveryFee = $grandTotal > 0 && $grandTotal < 199 ? 25 : 0;
$finalTotal = $grandTotal + $deliveryFee;

$errors = [];
$orderPlaced = false;
$orderId = $_GET['order_id'] ?? null;

// Stripe se Payment Success hone ke baad return hone par:
if (isset($_GET['payment']) && $_GET['payment'] === 'success' && $orderId) {
    $orderModel = new Order();
    
    // Database me status update karein
    if (method_exists($orderModel, 'updatePaymentStatus')) {
        $orderModel->updatePaymentStatus($orderId, 'PAID');
    }
    
    $orderPlaced = true;
    $cart->clear();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod'; // COD ya Stripe

    if ($name === '' || $phone === '' || $address === '') {
        $errors[] = 'Please fill in all fields.';
    }
    if (empty($items)) {
        $errors[] = 'Your cart is empty.';
    }

    if (empty($errors)) {
        $orderModel = new Order();

        if ($paymentMethod === 'cod') {
            // CASH ON DELIVERY FLOW
            $orderId = $orderModel->place($name, $phone, $address, $items, $finalTotal, 'COD', 'PENDING');
            if ($orderId) {
                $cart->clear();
                $orderPlaced = true;
            } else {
                $errors[] = 'Something went wrong while placing your order. Please try again.';
            }
        } elseif ($paymentMethod === 'stripe') {
            // ONLINE PAYMENT (STRIPE) FLOW
            try {
                // 1. Order DB me create karein
                $orderId = $orderModel->place($name, $phone, $address, $items, $finalTotal, 'STRIPE', 'PENDING');

                // 2. Stripe items prepare karein (Fixed qty key fallback)
                $lineItems = [];
                foreach ($items as $item) {
                    $itemQty = $item['qty'] ?? $item['quantity'] ?? 1;

                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'inr', // Apni currency set karein (inr / usd)
                            'product_data' => [
                                'name' => $item['name'] ?? 'Product',
                            ],
                            'unit_amount' => (int)($item['price'] * 100), // Amount in Paise/Cents
                        ],
                        'quantity' => (int)$itemQty,
                    ];
                }
                if ($deliveryFee > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'inr',
                            'product_data' => ['name' => 'Delivery Fee'],
                            'unit_amount' => (int)($deliveryFee * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => BASE_URL . 'checkout.php?payment=success&order_id=' . $orderId,
                    'cancel_url' => BASE_URL . 'checkout.php?payment=cancel',
                ]);
                header("Location: " . $session->url);
                exit();
            } catch (Exception $e) {
                $errors[] = 'Stripe Error: ' . $e->getMessage();
            }
        }
    }
}
$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>
<?php if ($orderPlaced): ?>
  <div class="form-box">
    <h2>🎉 Order Placed!</h2>
    <div class="alert alert-success">
      Thank you! Your order #<?php echo (int)$orderId; ?> has been placed successfully.
    </div>
    <a href="index.php" class="btn-primary" style="display:block; text-align:center;">Continue Shopping</a>
  </div>
<?php else: ?>

  <?php if (empty($items)): ?>
    <p class="empty-msg">Your cart is empty. <a href="index.php" style="color:#0c831f; font-weight:700;">Go back to shopping →</a></p>
  <?php else: ?>
    <div class="form-box">
      <h2>Checkout</h2>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
      <?php endforeach; ?>

      <div class="summary-row total" style="margin-bottom:16px;">
        <span>Order Total</span>
        <span><?php echo formatPrice($finalTotal); ?></span>
      </div>

      <form method="POST" action="checkout.php">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" value="<?php echo h($_POST['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" value="<?php echo h($_POST['phone'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="address">Delivery Address</label>
          <textarea id="address" name="address" required><?php echo h($_POST['address'] ?? ''); ?></textarea>
        </div>

        <!-- Payment Method Selection Radios -->
        <div class="form-group" style="margin-top:20px; background:#f9f9f9; padding:15px; border-radius:6px;">
          <label><strong>Select Payment Method:</strong></label>
          <div style="margin-top:10px;">
            <input type="radio" id="stripe" name="payment_method" value="stripe" checked>
            <label for="stripe" style="display:inline; margin-right:15px; cursor:pointer;">💳 Online Payment (Stripe)</label>
            <br><br>
            <input type="radio" id="cod" name="payment_method" value="cod">
            <label for="cod" style="display:inline; cursor:pointer;">💵 Cash on Delivery (COD)</label>
          </div>
        </div>

        <button type="submit" class="btn-primary" style="margin-top:20px;">Place Order</button>
      </form>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>