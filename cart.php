<?php
require_once __DIR__ . '/classes/Cart.php';

$pageTitle = 'Your Cart';
require_once __DIR__ . '/includes/header.php';

$cart = new Cart();
$items = $cart->getDetailedItems();
$grandTotal = $cart->getGrandTotal();
$deliveryFee = $grandTotal > 0 && $grandTotal < 199 ? 25 : 0;
$finalTotal = $grandTotal + $deliveryFee;
?>

<h1 class="section-title">Your Cart</h1>

<?php if (empty($items)): ?>
  <p class="empty-msg">Your cart is empty. <a href="index.php" style="color:#0c831f; font-weight:700;">Start shopping →</a></p>
<?php else: ?>
  <div class="cart-wrap">
    <div>
      <?php foreach ($items as $item): ?>
        <div class="cart-item">
          <img src="<?php echo productImage($item['image']); ?>" alt="<?php echo h($item['name']); ?>">
          <div class="info">
            <div class="name"><?php echo h($item['name']); ?></div>
            <div class="price"><?php echo formatPrice($item['price']); ?> each</div>
            <button class="remove-link" data-id="<?php echo (int)$item['id']; ?>">Remove</button>
          </div>
          <div class="qty-box">
            <button class="qty-dec" data-id="<?php echo (int)$item['id']; ?>">-</button>
            <input type="text" id="qty-input-<?php echo (int)$item['id']; ?>" value="<?php echo (int)$item['qty']; ?>" readonly>
            <button class="qty-inc" data-id="<?php echo (int)$item['id']; ?>">+</button>
          </div>
          <div style="font-weight:800; min-width:80px; text-align:right;">
            <?php echo formatPrice($item['subtotal']); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="summary-box">
      <h3>Order Summary</h3>
      <div class="summary-row">
        <span>Subtotal</span>
        <span><?php echo formatPrice($grandTotal); ?></span>
      </div>
      <div class="summary-row">
        <span>Delivery Fee</span>
        <span><?php echo $deliveryFee > 0 ? formatPrice($deliveryFee) : 'FREE'; ?></span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span><?php echo formatPrice($finalTotal); ?></span>
      </div>
      <a href="checkout.php" class="btn-primary" style="display:block; text-align:center;">Proceed to Checkout</a>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
