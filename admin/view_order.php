<?php
require_once __DIR__ . '/../classes/Order.php';

$pageTitle = 'Order Details';
require_once __DIR__ . '/includes/admin_header.php';

$orderModel = new Order();
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $orderModel->getById($orderId);

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $orderModel->updateStatus($orderId, $_POST['status']);
    header('Location: view_order.php?id=' . $orderId . '&updated=1');
    exit;
}

$items = $orderModel->getItems($orderId);
?>

<div class="admin-topbar">
  <h1>Order #<?php echo (int)$order['id']; ?></h1>
  <a href="orders.php" class="btn-small btn-edit">← Back to Orders</a>
</div>

<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success">Order status updated.</div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 300px; gap:20px; align-items:start;">
  <div>
    <table class="admin-table">
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
      </tr>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?php echo h($item['product_name']); ?></td>
          <td><?php echo formatPrice($item['price']); ?></td>
          <td><?php echo (int)$item['qty']; ?></td>
          <td><?php echo formatPrice($item['price'] * $item['qty']); ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="form-box" style="margin:0;">
    <h2 style="font-size:18px;">Customer Details</h2>
    <p><b>Name:</b> <?php echo h($order['customer_name']); ?></p>
    <p><b>Phone:</b> <?php echo h($order['phone']); ?></p>
    <p><b>Address:</b><br><?php echo nl2br(h($order['address'])); ?></p>
    <p><b>Total:</b> <?php echo formatPrice($order['total']); ?></p>

    <form method="POST" action="view_order.php?id=<?php echo (int)$order['id']; ?>">
      <div class="form-group">
        <label for="status">Order Status</label>
        <select id="status" name="status">
          <?php foreach (['Placed', 'Confirmed', 'Out for Delivery', 'Delivered', 'Cancelled'] as $status): ?>
            <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-primary">Update Status</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
